<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Document;
use AppBundle\Entity\Event;
use AppBundle\Repository\FollowerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


class EventsController extends Controller
{
    /**
     * @Route("/ajax/event_inscrip", options={"expose"=true}, name="event_inscrip"))
     */
    public function inscripcionAction(Request $request){
        $user_session = $this->getUser();
        $id_event = $request->get('id_event');
        $evento = $this->getDoctrine()
            ->getRepository(Event::class)
            ->loadEventById($id_event);
        $evento->addAssistant($user_session);
        $em = $this->getDoctrine()->getManager();
        $em->persist($evento);
        $em->flush();
        $result['confirmacion'] = 'Inscripcion realizada con exito';
        $result['ok'] = true;
        return new JsonResponse($result);
    }

    /**
     * @Route("/ajax/event_remove_inscrip", options={"expose"=true}, name="event_remove_inscrip"))
     */
    public function removeInscripAction(Request $request){
        $user_session = $this->getUser();
        $id_event = $request->get('id_event');
        $evento = $this->getDoctrine()
            ->getRepository(Event::class)
            ->loadEventById($id_event);
        $evento->removeAssistant($user_session);
        $em = $this->getDoctrine()->getManager();
        $em->persist($evento);
        $em->flush();
        $result['ok'] = true;
        return new JsonResponse($result);
    }

    /**
     * @Route("/ajax/borrar_evento", options={"expose"=true}, name="borrar_evento"))
     */
    public function borrarEventoAction(Request $request){
        $id_event = $request->get('id_event');
        $evento = $this->getDoctrine()
            ->getRepository(Event::class)
            ->loadEventById($id_event);
        $evento->removeAssistants();
        $em = $this->getDoctrine()->getManager();
        $em->persist($evento);
        $em->flush();
         $this->getDoctrine()
            ->getRepository(Event::class)
            ->deleteEvent($id_event);
        $em = $this->getDoctrine()->getManager();
        $em->persist($evento);
        $em->flush();
        $result['ok'] = true;
        $result['id_user'] = $this->getUser()->getId();
        return new JsonResponse($result);
    }
    /**
     * @Route("/ajax/reload_events", options={"expose"=true}, name="reload_events"))
     */
    public function reloadEvents(Request $request){
        $user_session = $this->getUser();
        $seguidores = $user_session->getFollowed();
        $eventos_usuario = [];

        if (count($seguidores)) {
            foreach ($seguidores as $seguidor) {
                foreach ($seguidor->getEvents() as $evento) {
                    $evento->user_session_assistant = $evento->isAssistant($user_session);
                    $eventos_usuario[] = $evento;
                }
            }
        }
        $result= array(
            "code" => 200,
            "html" => $this->render('front/templates/nav_eventos.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'eventos_aside' => $eventos_usuario
            ])->getContent());
        return new JsonResponse($result);
    }
}