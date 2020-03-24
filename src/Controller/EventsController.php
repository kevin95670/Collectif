<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Events;
use App\Form\EventsType;
use App\Data\SearchData;
use App\Form\SearchType;
use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventsController extends AbstractController
{
    /**
     * @Route("/events", name="events")
     */
    public function showAll(EventsRepository $eventsRepository,Request $request)
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);
        $events = $eventsRepository->getAllEvents($data);

        if ($request->get('ajax')){
            return new JsonResponse([
                'contenu' => $this->renderView('events/_events.html.twig', ['events' => $events]),
                'pagination' => $this->renderView('events/_pagination.html.twig', ['events' => $events])
            ]);
        }

        return $this->render('events/index.html.twig',[
            'events' => $events,
            'filters' => $form->createView()
        ]);
    }

    /**
     * @Route("/events/{category}", name="events_category")
     */
    public function showByCategory(EventsRepository $eventsRepository, $category,Request $request)
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        $form->remove('categories');
        $form->handleRequest($request);
        $events = $eventsRepository->getAllEventsOfCategory($category, $data);

        if ($request->get('ajax')){
            return new JsonResponse([
                'contenu' => $this->renderView('events/_events.html.twig', ['events' => $events]),
                'pagination' => $this->renderView('events/_pagination.html.twig', ['events' => $events])
            ]);
        }

        return $this->render('events/index.html.twig',[            
            'events' => $events,
            'filters' => $form->createView(),
            'showCategoryFilter' => 0
        ]);
    }

    /**
    * @Route("/participation", name="participations")
    */
    public function showParticipations(EventsRepository $eventsRepository, Request $request)
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);
        $participations = $eventsRepository->getUserParticipations($this->getUser(), $data);

        if ($request->get('ajax')){
            return new JsonResponse([
                'contenu' => $this->renderView('events/_events.html.twig', ['events' => $participations]),
                'pagination' => $this->renderView('events/_pagination.html.twig', ['events' => $participations])
            ]);
        }

        return $this->render('participations/participations.html.twig',[
            'events' => $participations,
            'filters' => $form->createView()
        ]);
    }

    /**
     * @Route("/event/new", name="new_event", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $event = new Events();
        $form = $this->createForm(EventsType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $event->setCreator($user);
            $event->addIdUser($user);
            
            $categories = $form->get('categories')->getData();
            for ($i=0; $i < count($categories); $i++) { 
                $event->addCategory($categories[$i]);
                $categories[$i]->addBelonging($event);
            }

            $user->addEvent($event);
            $event->setUpdatedAt(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('index');
        }

        return $this->render(
            'events/new.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/event/{id}", name="show_event", methods={"GET"})
     */
    public function show(EventsRepository $eventsRepository, $id): Response
    {
        $event = $eventsRepository->getSingleEvent($id);

        if($this->getUser() != null){
            $participe = $eventsRepository->userAlreadyParticipate($id, $this->getUser());
        }
        else{
            $participe = -1;
        }


        return $this->render('events/show.html.twig', [
            'event' => $event,
            'participe' => $participe,
        ]);
    }

    /**
     * @Route("/event/{id}/edit", name="edit_event", methods={"GET","POST"})
     */
    public function edit(Request $request, Events $event): Response
    {
        $form = $this->createForm(EventsType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('events/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/event/{id}", name="delete_event", methods={"DELETE"})
     */
    public function delete(Request $request, Events $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/event/{id}/join", name="join_event", methods={"GET","POST"})
     */
    public function join(Request $request, Events $event): Response
    {
        $user = $this->getUser();
        $event->addIdUser($user);
        $user->addEvent($event);
        $event->setUpdatedAt(new \DateTime('now'));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirectToRoute('participations');
    }

    /**
     * @Route("/event/{id}/leave", name="leave_event", methods={"GET","POST"})
     */
    public function leave(Request $request, Events $event): Response
    {
        $user = $this->getUser();
        $event->removeIdUser($user);
        $user->removeEvent($event);
        $event->setUpdatedAt(new \DateTime('now'));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirectToRoute('participations');
    }
}
