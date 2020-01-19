<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Events;
use App\Form\EventsType;
use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class EventsController extends AbstractController
{
    /**
     * @Route("/events", name="events")
     */
    public function showAll(EventsRepository $eventsRepository)
    {
        $allEvents = $eventsRepository->getAllEvents();


        return $this->render('events/index.html.twig',[
            'events' => $allEvents
        ]);
    }

    /**
     * @Route("/events/{category}", name="events_category")
     */
    public function showByCategory(EventsRepository $eventsRepository, $category)
    {
        $eventsOfThisCategory = $eventsRepository->getAllEventsOfCategory($category);

        return $this->render('events/index.html.twig',[            
            'events' => $eventsOfThisCategory
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
            $categories = $form->get('categories')->getData();
            for ($i=0; $i < count($categories); $i++) { 
                $event->addCategory($categories[$i]);
                $categories[$i]->addBelonging($event);
            }

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


        return $this->render('events/show.html.twig', [
            'event' => $event,
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
}
