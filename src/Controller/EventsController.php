<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EventsController extends AbstractController
{
    /**
     * @Route("/events", name="events")
     */
    public function showAll(EventsRepository $eventsRepository)
    {
        $allEvents = $eventsRepository->getAllEvents();


        return $this->render('events/events.html.twig',[
            'events' => $allEvents
        ]);
    }

    /**
     * @Route("/events/{category}", name="events_category")
     */
    public function showByCategory(EventsRepository $eventsRepository, $category)
    {
        $eventsOfThisCategory = $eventsRepository->getAllEventsOfCategory($category);

        return $this->render('events/events.html.twig',[            
            'events' => $eventsOfThisCategory
        ]);
    }

    /**
     * @Route("/create/event", name="new-event")
     */
    public function new(EventsRepository $eventsRepository)
    {

        return $this->render('events/events.html.twig');
    }
}
