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
            $posts = $em->getRepository('AppBundle:Post')->findEntitiesByString($requestString);
        }else{
            $posts= $this->getDoctrine()
                ->getRepository(Post::class)
                ->listPosts();
        }
        if(!$posts){
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