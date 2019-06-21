<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Notification;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;

class PostController extends Controller
{


    /**
     * @Route("/ajax/ajax_like", options={"expose"=true}, name="ajax_like"))
     */
    public function likesAction(Request $request)
    {
        $user_session = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $id_post = $request->get('id_post');
        $id_user = $request->get('id_user');
        $user = $em->getRepository('AppBundle:User')->loadUserByUserId($id_user);
        $post = $em->getRepository('AppBundle:Post')->getPostById($id_post);
        $result['error'] = false;
        if (!$post) {
            $result['error'] = true;
        } else {
            if ($post->hasLike($id_user)) {
                $post->removeLike($user);
                $em->persist($post);
                $em->flush();
                $result[$post->getId()]['like'] = false;
            } else {
                $post->addLike($user);
                $em->persist($post);
                $em->persist($user_session);
                $em->flush();
                $result[$post->getId()]['like'] = true;
            }
        }
        return new JsonResponse($result);
    }


    /**
     * @Route("/ajax/ajax_comments", options={"expose"=true}, name="ajax_comments"))
     */
    public function commentsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id_post = $request->get('id_post');
        $id_user = $request->get('id_user');
        $id_user_session = $request->get('id_user_session');
        $respuesta = $request->get('respuesta'); //Respuesta a un comentario
        $stringComment = $request->get('comment'); //Respuesta a un comentario
        $user_session = $em->getRepository('AppBundle:User')->loadUserByUserId($id_user_session);
        $user_comment = $em->getRepository('AppBundle:User')->loadUserByUserId($id_user);
        $post = $em->getRepository('AppBundle:Post')->getPostById($id_post);
        $result[$post->getId()]['comment'] = false;

        if ($respuesta == 'true') {
            //TODO: Cuando el comentario es una respuesta de otro comentario
        } else if ($respuesta == 'false') {
            $comment = new Comment($stringComment);
            $comment->setCreatedAt(new  \DateTime());
            $comment->setPost($post);
            $comment->setUser($user_session);
            $post->addComment($comment);
            $user_comment->addComment($comment);
            $em->persist($user_comment);
            $em->persist($post);
            $em->persist($comment);
            $notificacion = new Notification();
            $notificacion
                ->setTitle('comment')
                ->setDescription('nuevo comentario')
                ->setRoute('homepage')
                ->setParameters(['id'=> $post->getId()]);
            $em->persist($notificacion);
            $em->flush();
            $pusher = $this->get('mrad.pusher.notificaitons');
            $pusher->trigger($notificacion);
            $result['comment'] = true;
            $result['comment-content'] = $stringComment;
            $result['comment-user'] = $user_session->getUserName();
        }
        return new JsonResponse($result);
    }


    /**
     * @Route("/ajax/ajax_delete_comment", options={"expose"=true}, name="ajax_delete_comment"))
     */
    public function deleteCommentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id_comment = $request->get('id_comment');
        $em->getRepository(Post::class)->deleteComment($id_comment);
        $em->flush();
        $result['delete'] = true;
        return new JsonResponse($result);
    }

    /**
     * @Route("/ajax/delete_video", options={"expose"=true}, name="delete_video"))
     */
    public function deleteVideoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id_post = $request->get('id_post');
        $post = $em->getRepository(Post::class)->getPostById($id_post);
         $this->deleteDocumentFromPrivateBucket($post->getVideoName());
        $em->getRepository(Post::class)->deletePost($id_post);
        $em->flush();
        $result['delete'] = true;
        $result['id_post'] = $id_post;
        return new JsonResponse($result);
    }

    /**
     * @param string $documentName
     *
     * @return \Aws\Result|bool
     */
    public function deleteDocumentFromPrivateBucket($documentName)
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
//
//            $cmd = $s3->getCommand('DeleteObject', [
//                'Bucket' => $assets_bucket,
//                'Key' => 'assets/' . $documentName,
//            ]);

            $cmd = $s3->deleteObject([
                'Bucket' => $assets_bucket,
                'Key'    => 'assets/' . $documentName,
            ]);
            return $cmd;

        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }

    }

}