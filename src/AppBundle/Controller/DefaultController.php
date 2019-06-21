<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Document;
use AppBundle\Entity\User;
use AppBundle\Entity\Post;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Notification;
use AppBundle\Form\UploadPostType;
use AppBundle\Form\PhotoType;
use AppBundle\Form\PerfilType;
use AppBundle\Repository\FollowerRepository;
use Doctrine\ORM\Query\Expr\Func;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use AppBundle\Form\CuentaType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, Security $security, $posts = null)
    {

        if ($security->getUser()) {
            $user_session = $this->getUser();
            //Sacar lista seguidores
            $seguidores = $user_session->getFollowed();
            if ($posts == null) {
                if (count($seguidores)) {
                    $array_idseguidores = [];
                    foreach ($seguidores as $seguidor) {
                        $array_idseguidores[] = $seguidor->getId();
                        if ($seguidor->getPhoto()) {
                            $photo = $this->getDoctrine()
                                ->getRepository(Photo::class)
                                ->loadPhotoById($seguidor->getPhoto()->getId());
                            $photo->setEnlace($this->getDocumentFromPrivateBucket($photo->getPhotoName()));
                        }
                    }

                    $posts = $this->getDoctrine()
                        ->getRepository(Post::class)
                        ->listPostOfFollowers($array_idseguidores);
                    $posts = $this->getPostWithParameters($posts);
                    $parametros['posts'] = $posts;
                    $parametros['seguidores'] = $seguidores;

                }
            }
            $parametros['base_dir'] = realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR;
            return $this->render('/front/app/index.html.twig', $parametros);
        } else {

            $posts = $this->getDoctrine()
                ->getRepository(Post::class)
                ->listPosts();

            $posts = $this->getPostWithParameters($posts);

            return $this->render('/front/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'posts' => $posts,
            ]);
        }
    }


    /**
     * @Route("/ajax/ajax_post_index", options={"expose"=true}, name="ajax_post_index"))
     */
    public function ajaxPostIndexAction(Request $request)
    {
        $offset = $request->get('offset');
        $user_session = $this->getUser();
        //Sacar lista seguidores
        $seguidores = $user_session->getFollowed();

        $array_idseguidores = [];
        foreach ($seguidores as $seguidor) {
            $array_idseguidores[] = $seguidor->getId();
        }

        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->listPostOfFollowers($array_idseguidores, $offset);

        $max = count($posts);

        $posts = $this->searchShowPostAction($request, $posts, $offset, $max);

        return $posts;
    }


    /**
     * @Route("/{username}", name="perfil")
     */
    public function perfilAction(Request $request, Security $security, $username)
    {
        //Formulario subir video
        $user_session = $this->getUser();
        $post = new Post();
        $form = $this->createForm(UploadPostType::class, $post);
        $form->handleRequest($request);
        $result['error'] = false;
        if ($form->get('subir_video')->isClicked()) {
            if ($form->isSubmitted() && $form->isValid()) {
                $validator = $this->get('validator');
                $errors = $validator->validate($post);
                if (count($errors) > 0) {
                    $errorsString = (string)$errors;
                    $result['error'] = true;
                    $result['errors'] = $errorsString;
                }
                $user_id = $this->getUser()->getId();
                $post->setUserId($user_id);
                $em = $this->getDoctrine()->getManager();
                $em->persist($post);

                $em->persist($user_session);
                $em->flush();
//                $em = $this->getDoctrine()->getManager();
//                $RAW_QUERY = 'SELECT * AS num
//                      FROM follow
//                      WHERE followed_id = :user_id
//
//                      ';
//                $statement = $em->getConnection()->prepare($RAW_QUERY);
//                $statement->bindValue('user_id', $user_session->getId());
//                $statement->execute();
//                $followers = $statement->fetchAll();
                $result['confirmacion'] = "El video se ha subido exitosamente";
            }
        }

        $usuario = $this->getDoctrine()
            ->getRepository(User::class)
            ->loadUserByUsername($username);
        $num_posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->countPosts($usuario->getId());

        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->getPostsByUserId($usuario->getId(), 0);

        $posts = $this->getPostWithParameters($posts);

        //Numero de seguidores
        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT COUNT(*) AS num 
                      FROM follow 
                      WHERE followed_id = :user_id
                      
                      ';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->bindValue('user_id', $usuario->getId());
        $statement->execute();
        $num_followers = $statement->fetch();
        $usuario->isfollowed = false;

        if ($user_session->getId() != $usuario->getId() && $user_session->isFollower($usuario->getId())) {
            $usuario->isfollowed = true;
        }

        $parametros = [
            'posts' => $posts,
            'user' => $usuario,
            'num_posts' => $num_posts,
            'num_followed' => count($usuario->getFollowed()),
            'num_followers' => $num_followers['num'],
            'subir_video' => $form->createView(),
            'result' => $result,
            'photo' => null,
        ];
        //Photo
        if ($usuario->getPhoto()) {
            $photo = $this->getDoctrine()
                ->getRepository(Photo::class)
                ->loadPhotoById($usuario->getPhoto()->getId());
            $parametros['photo'] = $this->getDocumentFromPrivateBucket($photo->getPhotoName());
        }
        $seguidores = $user_session->getFollowed();
        if (count($seguidores)) {
            foreach ($seguidores as $seguidor) {
                if ($seguidor->getPhoto()) {
                    $photo = $this->getDoctrine()
                        ->getRepository(Photo::class)
                        ->loadPhotoById($seguidor->getPhoto()->getId());
                    $photo->setEnlace($this->getDocumentFromPrivateBucket($photo->getPhotoName()));
                }
            }
            $parametros['seguidores'] = $seguidores;
        }
        return $this->render('front/app/perfil.html.twig', $parametros);
    }


    /**
     * @Route("/ajax/ajax_post_perfil", options={"expose"=true}, name="ajax_post_perfil"))
     */
    public function ajaxPostPerfilAction(Request $request)
    {
        $offset = $request->get('offset');
        $user_id = $request->get('user_id');

        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->getPostsByUserId($user_id, $offset);

        $max = count($posts);

        $response = $this->searchShowPostAction($request, $posts, $offset, $max);
        return $response;
    }


    /**
     * @Route("/explorar/", name="explorar")
     */
    public function explorarAction(Request $request, Security $security)
    {
        //Los videos mas comentados
        //TODO Esto mejor hacerlo desde los repositorios
        $em = $this->getDoctrine()->getManager();
        $RAW_QUERY = 'SELECT c.post_id, count(c.post_id) as numero
                    from comments c
                    group by post_id 
                    ORDER BY numero DESC
                    LIMIT 0,3;
                  ';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $array_posts = $statement->fetchAll();
        $posts_comments = [];
        foreach ($array_posts as $fila) {
            $posts_comments[] = $this->getDoctrine()
                ->getRepository(Post::class)
                ->getPostById($fila['post_id']);
        }
        //Los videos mas gustados
        $RAW_QUERY = 'SELECT pl.post_id, count(pl.post_id) as numero
                    from post_likes pl
                    group by post_id 
                    ORDER BY numero DESC
                    LIMIT 0,3;
                  ';
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();
        $array_posts = $statement->fetchAll();
        $posts_likes = [];
        foreach ($array_posts as $fila) {
            $posts_likes[] = $this->getDoctrine()
                ->getRepository(Post::class)
                ->getPostById($fila['post_id']);
        }
        //Seguidores para aside
        $user_session = $this->getUser();
        $seguidores = $user_session->getFollowed();
        if (count($seguidores)) {
            foreach ($seguidores as $seguidor) {
                if ($seguidor->getPhoto()) {
                    $photo = $this->getDoctrine()
                        ->getRepository(Photo::class)
                        ->loadPhotoById($seguidor->getPhoto()->getId());
                    $photo->setEnlace($this->getDocumentFromPrivateBucket($photo->getPhotoName()));
                }
            }
        }
        $posts_comments = $this->getPostWithParameters($posts_comments);
        $posts_likes = $this->getPostWithParameters($posts_likes);

        return $this->render('/front/app/explorar.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'seguidores' => $seguidores,
            'posts_comments' => $posts_comments,
            'posts_likes' => $posts_likes,
        ]);

    }


    /**
     * @Route("/ajax/ajax_explorar", options={"expose"=true}, name="ajax_explorar"))
     */
    public function ajaxExplorarAction(Request $request)
    {
        $offset = $request->get('offset');
        $seccion = $request->get('seccion');
        $em = $this->getDoctrine()->getManager();

        if ($seccion == 'comentados') {
            //TODO Esto mejor hacerlo desde los repositorios
            $RAW_QUERY = 'SELECT c.post_id, count(c.post_id) as numero
                    from comments c
                    group by post_id 
                    ORDER BY numero DESC
                    LIMIT ' . $offset . ',3';

            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();

            $array_posts = $statement->fetchAll();
            $posts = [];
            foreach ($array_posts as $fila) {
                $posts[] = $this->getDoctrine()
                    ->getRepository(Post::class)
                    ->getPostById($fila['post_id']);
            }
        } else if ($seccion == 'likes') {
            //Los videos mas gustados
            $RAW_QUERY = 'SELECT pl.post_id, count(pl.post_id) as numero
                    from post_likes pl
                    group by post_id 
                    ORDER BY numero DESC
                    LIMIT ' . $offset . ',3;
                  ';
            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();
            $array_posts = $statement->fetchAll();
            $posts = [];
            foreach ($array_posts as $fila) {
                $posts[] = $this->getDoctrine()
                    ->getRepository(Post::class)
                    ->getPostById($fila['post_id']);
            }
        }

        $max = count($posts);
        $response = $this->searchShowPostAction($request, $posts, $offset, $max);
        return $response;
    }


    /**
     * @Route("/ajax/ajax_searchShowPost", options={"expose"=true}, name="ajax_searchShowPost"))
     */
    public function searchShowPostAction(Request $request, $posts = null, $offset = 0, $max = 9)
    {
        if (!$posts) {
            $result = $request->get('posts');
            if ($result) {
                $posts = [];
                $em = $this->getDoctrine()->getManager();
                foreach ($result['posts'] as $id_post => $cancion) {
                    $posts[] = $em->getRepository('AppBundle:Post')->getPostById($id_post);
                }
            }
        }

        foreach ($posts as $post) {
            //Videos recibidos por AWS S3 client
            $post->setEnlace($this->getDocumentFromPrivateBucket($post->getVideoName()));

            //Nombre del usuario del post
            $usuario = $this->getDoctrine()
                ->getRepository(User::class)
                ->loadUserByUserId($post->getUserId());
            $post->name_user = $usuario->getUserName();
            $post->id_user = $usuario->getId();
            //Segir usuario
            $user_session = $this->getUser();
            if ($user_session) {
                $id_user_session = $user_session->getId();
                $id_user_post = $post->getUserId();
                //Comprobar si ya sigue a ese usuario
                $post->followed = false;
                if ($id_user_session != $id_user_post && $user_session->isFollower($id_user_post)) {
                    $post->followed = true;
                }
                //He clickado en  Me gusta de este post
                $post->liked = false;
                if ($post->hasLike($id_user_session)) {
                    $post->liked = true;
                }
                foreach ($post->getComments() as $comment) {
                    $comment->username_comment = $comment->getUser()->getUserName();
                    $comment->id_user_comment = $comment->getUser()->getId();
                    $comment->id_user_post = $comment->getPost()->getId();
                }
            }
        }

        $response = array(
            'offset' => $offset,
            'max' => $max,
            "code" => 200,
            "html" => $this->render('front/templates/posts.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'posts' => $posts,
            ])->getContent());
        return new JsonResponse($response);
    }


    /**
     * @Route("/configuracion/", name="configuracion")
     */
    public function configuracionAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user_session = $this->getUser();
        if ($user_session->getPhoto()) {
            $photo = $this->getDoctrine()
                ->getRepository(Photo::class)
                ->loadPhotoById($user_session->getPhoto()->getId());
        } else {
            $photo = new Photo();
        }

        $form_photo = $this->createForm(PhotoType::class, $photo);
        $form_photo->handleRequest($request);

        //Form perfil
        $form_perfil = $this->createForm(PerfilType::class, $user_session);
        $form_perfil->handleRequest($request);

        if ($form_photo->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user_session->setPhoto($photo);
            $entityManager->persist($photo);
            $entityManager->persist($user_session);
            $entityManager->flush();
            return $this->redirectToRoute('configuracion');
        }
        $photo->setEnlace($this->getDocumentFromPrivateBucket($photo->getPhotoName()));

        if ($form_perfil->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user_session);
            $entityManager->flush();
            return $this->redirectToRoute('configuracion');
        }

        //Form cuenta
        $form_cuenta = $this->createForm(CuentaType::class, $user_session);
        $form_cuenta->handleRequest($request);
        if ($form_cuenta->isSubmitted() && $form_cuenta->isValid()) {
            $password = $passwordEncoder->encodePassword($user_session, $user_session->getPlainPassword());
            $user_session->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user_session);
            $entityManager->flush();
            return $this->redirectToRoute('configuracion');
        }

        $parametros = [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'form_cuenta' => $form_cuenta->createView(),
            'form_perfil' => $form_perfil->createView(),
            'form_photo' => $form_photo->createView(),
        ];

        $seguidores = $user_session->getFollowed();
        if (count($seguidores)) {
            foreach ($seguidores as $seguidor) {
                if ($seguidor->getPhoto()) {
                    $photo = $this->getDoctrine()
                        ->getRepository(Photo::class)
                        ->loadPhotoById($seguidor->getPhoto()->getId());
                    $photo->setEnlace($this->getDocumentFromPrivateBucket($photo->getPhotoName()));
                }
            }
            $parametros['seguidores'] = $seguidores;
        }
        return $this->render('/front/app/configuracion.html.twig', $parametros);

    }


    /**
     * @Route("/mensajes/", name="mensajes")
     */
    public function mensajesAction(Request $request, Security $security)
    {
        //Seguidores para aside
        $user_session = $this->getUser();
        $seguidores = $user_session->getFollowed();

        return $this->render('/front/app/mensajes.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'seguidores' => $seguidores,
        ]);
    }


    /**
     * @Route("/notificaciones/", name="notificaciones")
     */
    public function notificationsAction(Request $request, Security $security, $posts = null)
    {
        $user_session = $this->getUser();
        $seguidores = $user_session->getFollowed();
        $notifications = [];
        $array_id_seguidores = [];
        if (count($seguidores)) {
            foreach ($seguidores as $seguidor) {
                $array_id_seguidores[] = $seguidor->getId();
                if ($seguidor->getPhoto()) {
                    $photo = $this->getDoctrine()
                        ->getRepository(Photo::class)
                        ->loadPhotoById($seguidor->getPhoto()->getId());
                    $photo->setEnlace($this->getDocumentFromPrivateBucket($photo->getPhotoName()));
                }
            }
            $parametros['seguidores'] = $seguidores;
            $notifications = $this->getDoctrine()
                ->getRepository(Notification::class)
                ->listNotificationOnArray($array_id_seguidores);
        }

        return $this->render('/front/app/notificaciones.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'seguidores' => $seguidores,
            'notificaciones' => $notifications,
        ]);
    }



    public function getPostWithParameters($posts)
    {
        foreach ($posts as $post) {
            //Videos recibidos por AWS S3 client
            $post->setEnlace($this->getDocumentFromPrivateBucket($post->getVideoName()));

            //Nombre del usuario del post
            $usuario = $this->getDoctrine()
                ->getRepository(User::class)
                ->loadUserByUserId($post->getUserId());
            $post->name_user = $usuario->getUserName();
            $post->id_user = $usuario->getId();
            //Segir usuario
            $user_session = $this->getUser();
            if ($user_session) {
                $id_user_session = $user_session->getId();
                $id_user_post = $post->getUserId();
                //Comprobar si ya sigue a ese usuario
                $post->followed = false;
                if ($id_user_session != $id_user_post && $user_session->isFollower($id_user_post)) {
                    $post->followed = true;
                }
                //He clickado en  Me gusta de este post
                $post->liked = false;

                if ($post->hasLike($id_user_session)) {
                    $post->liked = true;
                }
                foreach ($post->getComments() as $comment) {
                    $comment->username_comment = $comment->getUser()->getUserName();
                    $comment->id_user_comment = $comment->getUser()->getId();
                    $comment->id_user_post = $comment->getPost()->getId();
                }
            }
        }
        return $posts;
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
