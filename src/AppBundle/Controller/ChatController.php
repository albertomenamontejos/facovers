<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Chat;
use AppBundle\Entity\Message;
use AppBundle\Entity\Photo;
use AppBundle\Repository\FollowerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


class ChatController extends Controller
{
    /**
     * @Route("/ajax/chat", options={"expose"=true}, name="ajax_chat"))
     */
    public function chatAction(Request $request){
        //cargar chat si ya esta creado
        $user_session = $this->getUSer();
        $id_user = $request->get('id_user');
        $em = $this->getDoctrine()->getManager();
        $usuario_receptor =   $em->getRepository('AppBundle:User')->loadUserByUserId($id_user);
        $mensajes = [];

        if($chat = $this->existeChat($user_session,$usuario_receptor)){
            $mensajes = $chat->getMensajes();
        }

        if(!$chat = $this->existeChat($user_session,$usuario_receptor)){
            $chat = new Chat();
            $chat->addUser($user_session);
            $chat->addUser($usuario_receptor);
            $user_session->addChat($chat);
            $usuario_receptor->addChat($chat);
        }
        $em->persist($user_session);
        $em->persist($usuario_receptor);
        $em->persist($chat);
        $em->flush();
        if ($usuario_receptor->getPhoto()) {
            $photo = $this->getDoctrine()
                ->getRepository(Photo::class)
                ->loadPhotoById($usuario_receptor->getPhoto()->getId());
            $photo->setEnlace($this->getDocumentFromPrivateBucket($photo->getPhotoName()));
        }
        $response = array(
            "code" => 200,
            "html_cabecera" => $this->render('front/templates/cabecera.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'usuario' => $usuario_receptor,
            ])->getContent(),
            "html_mensajes" => $this->render('front/templates/mensajes.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'mensajes' => $mensajes,
            ])->getContent());
        return new JsonResponse($response);
    }


    /**
     * @Route("/ajax/enviar_mensaje", options={"expose"=true}, name="enviar_mensaje"))
     */
    public function enviarMensaje(Request $request){
        $user_session = $this->getUser();
        $id_user = $request->get('id_user');
        $mensaje_request = $request->get('mensaje');
        $em = $this->getDoctrine()->getManager();
        $usuario_receptor =   $em->getRepository('AppBundle:User')->loadUserByUserId($id_user);
        $mensaje = new Message();
        $mensaje->setUser($user_session);
        $mensaje->setMensaje($mensaje_request);
        if($chat = $this->existeChat($user_session,$usuario_receptor)){
            $chat->addMensaje($mensaje);
        }else{
            $chat = new Chat();
            $chat->addUser($user_session);
            $chat->addUser($usuario_receptor);
            $chat->addMensaje($mensaje);
            $user_session->addChat($chat);
            $usuario_receptor->addChat($chat);
        }
        $em->persist($user_session);
        $em->persist($usuario_receptor);
        $em->persist($chat);
        $em->persist($mensaje);
        $em->flush();
        $chat = $this->existeChat($user_session,$usuario_receptor);
        $mensajes = $chat->getMensajes();
        $response = array(
            "code" => 200,
            "html_mensajes" => $this->render('front/templates/mensajes.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'mensajes' => $mensajes,
            ])->getContent());
        return new JsonResponse($response);
    }

    /**
     * @Route("/ajax/reload_mensajes", options={"expose"=true}, name="reload_mensaje"))
     */
    public function reloadMensaje(Request $request){
        $user_session = $this->getUSer();
        $em = $this->getDoctrine()->getManager();
        $user_session->getChats();
        $id_user = $request->get('id_user');
        $usuario_receptor =   $em->getRepository('AppBundle:User')->loadUserByUserId($id_user);
        $chat = $this->existeChat($user_session,$usuario_receptor);
        $mensajes = $chat->getMensajes();
        $response = array(
            "code" => 200,
            "html_mensajes" => $this->render('front/templates/mensajes.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'mensajes' => $mensajes,
            ])->getContent());
        return new JsonResponse($response);
    }

    public function reloadMessage($user_session,$usuario_receptor){

    }


    public function existeChat($user_session,$usuario_receptor){
      $ct_user_session = $user_session->getChats();
      foreach($ct_user_session as $chat){
        foreach($chat->getUsers() as $usuario){
            if($usuario->getId() == $usuario_receptor->getId()){
                return $chat;
            }
        }
      }
      return false;
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