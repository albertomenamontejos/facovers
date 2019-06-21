<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Document;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Entity\Photo;
use AppBundle\Repository\FollowerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class PerfilController extends Controller
{

    /**
     * @Route("/ajax/reload_stadistics", options={"expose"=true}, name="reload_stadistics"))
     */
    public function reloadStadisticsAction(Request $request)
    {
        //Numero de seguidores del usuario del perfil
        $user_id = $request->get('user_id');
        $usuario = $this->getDoctrine()
            ->getRepository(User::class)
            ->loadUserByUserId($user_id);
        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT COUNT(*) as num FROM follow where followed_id = :user_id';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('user_id', $usuario->getId());
        $statement->execute();
        $num_followers = $statement->fetch();

        //Numero de videos del usuario del perfil
        $num_posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->countPosts($usuario->getId());

        $result['estadisticas'] =  array(
            "code" => 200,
            'num_followers' => $num_followers['num'] ,
            'num_videos' => $num_posts);
        return new JsonResponse($result);
    }


    /**
     * @Route("/ajax/reload_videos", options={"expose"=true}, name="reload_videos"))
     */
    public function reloadVideosAction(Request $request)
    {
        //Numero de seguidores del usuario del perfil
        $user_id = $request->get('user_id');
        $usuario = $this->getDoctrine()
            ->getRepository(User::class)
            ->loadUserByUserId($user_id);
        //Numero de videos del usuario del perfil
        $num_posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->countPosts($usuario->getId());

        $result['estadisticas'] =  array(
            "code" => 200,
            'num_videos' => $num_posts);
        return new JsonResponse($result);
    }


    /**
     * @Route("/ajax/reload_followers", options={"expose"=true}, name="reload_followers"))
     */
    public function reloadFollowersAction(Request $request)
    {
        //Numero de seguidores del usuario del perfil
        $user_id = $request->get('user_id');

        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT *  FROM follow where followed_id = :user_id'; //Mis seguidores
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('user_id', $user_id);
        $statement->execute();
        $filas = $statement->fetchAll();
        $result=[];
        foreach ($filas as $fila) {
            $follow = $this->getDoctrine()
                ->getRepository(User::class)
                ->loadUserByUserId($fila['user_id']);
            $photo = null;
                if($follow->getPhoto()){
                    $photo = $this->getDoctrine()
                        ->getRepository(Photo::class)
                        ->loadPhotoById($follow->getPhoto()->getId());
                    $photo->setEnlace($this->getDocumentFromPrivateBucket($photo->getPhotoName()));
                }
            $result['users'][] = [
                'user_id' => $follow->getId(),
                'username' => $follow->getUserName(),
                'photo' => $photo
            ];
        }
        return new JsonResponse($result);
    }


    /**
     * @Route("/ajax/show_followed", options={"expose"=true}, name="show_followed"))
     */
    public function showFollowedAction(Request $request)
    {

        $user_id = $request->get('user_id');
        $followed = $request->get('followed');
        //Numero de seguidores
        $em = $this->getDoctrine()->getManager();
        if ($followed == 'true') {
            $RAW_QUERY = 'SELECT *  FROM follow where user_id = :user_id'; //Personas que yo sigo
        } else {
            $RAW_QUERY = 'SELECT *  FROM follow where followed_id = :user_id'; //Mis seguidores
        }
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('user_id', $user_id);
        $statement->execute();
        $filas = $statement->fetchAll();

        if (!$filas) {
            $result['seguidos']['error'] = array(
                "code" => 200,
                "html" => $this->render('front/error/error_posts_ajax.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                ])->getContent());
        } else {
            foreach ($filas as $fila) {
                $photo = null;
                if ($followed == 'true') {
                    $follow = $this->getDoctrine()
                        ->getRepository(User::class)
                        ->loadUserByUserId($fila['followed_id']);
                    if($follow->getPhoto()){
                        $photo = $this->getDoctrine()
                            ->getRepository(Photo::class)
                            ->loadPhotoById($follow->getPhoto()->getId());
                        $photo= $this->getDocumentFromPrivateBucket($photo->getPhotoName());
                    }
                } else {
                    $follow = $this->getDoctrine()
                        ->getRepository(User::class)
                        ->loadUserByUserId($fila['user_id']);
                    if($follow->getPhoto()){
                        $photo = $this->getDoctrine()
                            ->getRepository(Photo::class)
                            ->loadPhotoById($follow->getPhoto()->getId());
                        $photo= $this->getDocumentFromPrivateBucket($photo->getPhotoName());
                    }
                }
                $result['users'][] = [
                    'user_id' => $follow->getId(),
                    'username' => $follow->getUserName(),
                    'photo' => $photo,
                ];
            }
        }
        return new JsonResponse($result);
    }

    /**
     * @param string $documentName
     *
     * @return \Aws\Result|bool
     */
    public function getDocumentFromPrivateBucket($documentName)
    {
        $assets_key = $this->container->getParameter('assets_key');
        $assets_secret = $this->container->getParameter('assets_secret');
        $assets_region = $this->container->getParameter('assets_region');
        $assets_bucket = $this->container->getParameter('assets_bucket');

        try {
            $s3 = new S3Client([
                'version' => 'latest',
                'region' => $assets_region,
                'credentials' => [
                    'key' => $assets_key,
                    'secret' => $assets_secret,
                ],
            ]);

            $cmd = $s3->getCommand('GetObject', [
                'Bucket' => $assets_bucket,
                'Key' => 'assets/' . $documentName,
            ]);

            $request = $s3->createPresignedRequest($cmd, '+24 hours');
            $presignedUrl = (string)$request->getUri();
            return $presignedUrl;

        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }



}