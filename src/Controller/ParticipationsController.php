<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ParticipationsController extends AbstractController
{
    /**
     * @Route("/participation", name="participations")
     */
    public function showParticipations(EventsRepository $eventsRepository)
    {
    	$participations = $eventsRepository->getUserParticipations($this->getUser());

        return $this->render('participations/participations.html.twig',[
        	'events' => $participations
        ]);
    }
}
