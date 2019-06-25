<?php

namespace AppBundle\Controller;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Post;
use AppBundle\Utils\UtilsPost;

class IndexController extends Controller
{

    /**
     * @Route("/ajax/ajax_search", options={"expose"=true}, name="ajax_search"))
     */
    public function searchAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        if(strlen($requestString) != 0 ){
            $suf = substr($requestString,0,1);
            $cadena = substr($requestString,1,strlen($requestString));
            if( $suf != '@' && $suf != '#'){
                $posts = $em->getRepository('AppBundle:Post')->findEntitiesByString($requestString);

            }elseif($suf == '@'){
                $array_users = $em->getRepository('AppBundle:User')->findEntitiesByString($cadena);

                $array_id_users = [];
                foreach($array_users as $user_id){
                $array_id_users[] = $user_id['id'];
                }
                $posts = $em->getRepository('AppBundle:Post')->listPostByArrayId($array_id_users);
            }elseif($suf != '#'){
                $posts = $em->getRepository('AppBundle:Post')->findEntitiesByHastag($requestString);
            }
        }

        if(empty($posts) || !$posts){
            $result['posts']['error'] = array(
                "code" => 200,
                "html" => $this->render('front/error/error_posts_ajax.html.twig',[
                    'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                ])->getContent());
        }else{
            $result['posts'] = UtilsPost::getRealEntities($posts);
        }
        return new JsonResponse($result);

    }



}