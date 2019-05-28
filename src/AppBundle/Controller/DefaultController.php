<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Document;
use AppBundle\Entity\User;
use AppBundle\Form\UploadPostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use AppBundle\Entity\Post;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Firewall\ContextListener;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\Iam\IamClient;
use Aws\Sts\StsClient;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, Security $security)
    {
        if ($security->getUser()) {
            $list_post = $this->getDoctrine()
                ->getRepository(Post::class)
                ->listPosts();
            $posts = [];
            foreach ($list_post as $post) {
                $posts[] = $this->getDocumentFromPrivateBucket($post->getVideoName());
            }

            return $this->render('/front/app/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'posts' => $posts,
            ]);
        } else {
            $list_post = $this->getDoctrine()
                ->getRepository(Post::class)
                ->listPosts();
            $posts = [];
            //Videos recibidos por AWS S3 client
            foreach ($list_post as $post) {
                $posts[] = $this->getDocumentFromPrivateBucket($post->getVideoName());
            }

            return $this->render('/front/index.html.twig',[
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'posts'=>$posts,
            ]);
        }
    }

    /**
     * @Route("/subir", name="upload")
     */
    public function uploadAction(Request $request, Security $security)
    {

        $post = new Post();
        $form = $this->createForm(UploadPostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $validator = $this->get('validator');
            $errors = $validator->validate($post);
            if (count($errors) > 0) {
                /*
                 * Uses a __toString method on the $errors variable which is a$client = new IamClient([
    'region' => 'us-west-2',
    'version' => '2010-05-08'
]);
                 * ConstraintViolationList object. This gives us a nice string
                 * for debugging.
                 */
                $errorsString = (string)$errors;
                return new Response($errorsString);
            }
            $user_id = $this->getUser()->getId();
            $post->setUserId($user_id);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return new Response('The author is valid! Yes!');
        }

        return $this->render('front/app/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param string $documentName
     *
     * @return \Aws\Result|bool
     */
    public function getDocumentFromPrivateBucket($documentName)
    {
        $assets_key = $this->container->getParameter('assets_key');
        $assets_uri = $this->container->getParameter('assets_uri');
        $assets_secret = $this->container->getParameter('assets_secret');
        $assets_region = $this->container->getParameter('assets_region');
        $assets_bucket = $this->container->getParameter('assets_bucket');
        $assets_version = $this->container->getParameter('assets_version');

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
            // Get the object.
//            $result =  $s3->getObject([
//                'Bucket' => $assets_bucket,
//                'Key' => 'assets/' . $documentName,
//            ]);
//            header("Content-Type: {$result['ContentType']}");
//            echo ;
//
//
//            $response = new BinaryFileResponse($result['Body']);
//            $response->setAutoEtag(true);
//            $response->headers->set('Content-Type', 'video/mp4');
            $presignedUrl = (string)$request->getUri();
            return $presignedUrl;

        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
}
