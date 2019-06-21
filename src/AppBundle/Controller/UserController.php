<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * Para boton de seguir al usuario
     * @Route("/ajax/ajax_follow", options={"expose"=true}, name="ajax_follow"))
     */
    public function followAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id_user_post = $request->get('id_user');
        $id_user_session = $request->get('id_user_session');
        $user_session = $em->getRepository('AppBundle:User')->loadUserByUserId($id_user_session);
        $user_post = $em->getRepository('AppBundle:User')->loadUserByUserId($id_user_post);
        if($user_session->isFollower($id_user_post)){
            $user_session->removeFollowed($user_post);
                $em->persist($user_session);
                $em->flush();
                $result[$user_post->getId()]['follow'] = false;
            }else{
                $user_session->addFollowed($user_post);
                $em->persist($user_session);
                $em->flush();
                $result[$user_post->getId()]['follow'] = true;
            }
        return new JsonResponse($result);
    }
}