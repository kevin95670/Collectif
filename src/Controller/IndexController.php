<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EventsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(EventsRepository $eventsRepository)
    {
    	$nbEventByCategory = $eventsRepository->getEventsGroupByCategories();

        return $this->render('index/index.html.twig',[
        	'nbEvent' => $eventsRepository->getNbEvents(),
        	'nbEventByCategory' => $nbEventByCategory,
        ]);
    }
}
