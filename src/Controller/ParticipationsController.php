<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EventsRepository;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class ParticipationsController extends AbstractController
{
    /**
     * @Route("/participation", name="participations")
     */
    public function showParticipations(EventsRepository $eventsRepository, CategoriesRepository $categoriesRepository, PaginatorInterface $paginator, Request $request)
    {
    	$participationsQuery = $eventsRepository->getUserParticipations($this->getUser());
        $categories = $categoriesRepository->findAll();
        $pagination = $paginator->paginate(
            $participationsQuery, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('participations/participations.html.twig',[
        	'events' => $pagination,
            'categories' => $categories
        ]);
    }
}
