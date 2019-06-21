<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Document;
use AppBundle\Entity\Photo;
use AppBundle\Repository\FollowerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


class AsideController extends Controller
{

    /**
     * @Route("/ajax/user_aside", options={"expose"=true}, name="user_aside"))
     */
    public function userAsideAction(Request $request)
    {
        $user_session = $this->getUser();
        $seguidores= $user_session->getFollowed();
        if (count($seguidores)) {

            foreach($seguidores as $seguidor){
                if($seguidor->getPhoto()){
                    $photo = $this->getDoctrine()
                        ->getRepository(Photo::class)
                        ->loadPhotoById($seguidor->getPhoto()->getId());
                    $photo->setEnlace($this->getDocumentFromPrivateBucket($photo->getPhotoName()));
                }
            }
            $result['aside'] =  array(
                "code" => 200,
                "html" => $this->render('front/templates/seguidos.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                    'seguidores' => $seguidores
                ])->getContent());

        } else {
            $result['aside']= array(
                "code" => 200,
                "html" => $this->render('front/error/asideNotUsers.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                ])->getContent());
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