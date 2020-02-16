<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EventsRepository;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ParticipationsController extends AbstractController
{
    /**
     * @Route("/participation", name="participations")
     */
    public function showParticipations(EventsRepository $eventsRepository, CategoriesRepository $categoriesRepository)
    {
    	$participations = $eventsRepository->getUserParticipations($this->getUser());
        $categories = $categoriesRepository->findAll();

        return $this->render('participations/participations.html.twig',[
        	'events' => $participations,
            'categories' => $categories
        ]);
    }
}
