<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ReviewController extends AbstractController
{
    /**
     * @Route("/review/{id}", name="delete_review", methods={"DELETE"})
     */
    public function delete(Request $request, Review $review): Response
    {
        $event_id = $review->getEvent()->getId();
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('show_event',array('id' => $event_id));
    }
}
