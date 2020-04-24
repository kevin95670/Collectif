<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Events;
use App\Entity\Comment;
use App\Entity\Review;
use App\Form\EventsType;
use App\Data\SearchData;
use App\Form\SearchType;
use App\Form\CommentType;
use App\Form\ReviewType;
use App\Repository\EventsRepository;
use App\Repository\CommentRepository;
use App\Repository\ReviewRepository;
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
            $event->addUser($user);
            
            $categories = $form->get('categories')->getData();
            for ($i=0; $i < count($categories); $i++) { 
                $event->addCategory($categories[$i]);
                $categories[$i]->addEvents($event);
            }

            $user->addEvent($event);
            $event->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $event->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

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
    public function show(EventsRepository $eventsRepository, $id, CommentRepository $commentRepository, ReviewRepository $reviewRepository, Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $review = new Review();
        $formReview = $this->createForm(ReviewType::class, $review);
        $formReview->handleRequest($request);

        $comments = $commentRepository->getCommentsOfEvent($id);
        $reviews = $reviewRepository->getReviewsOfEvent($id);
        $event = $eventsRepository->getSingleEvent($id);

        $moyenne = $reviewRepository->getAverageOfEvent($id);

        if($this->getUser() != null){ // 1 si participe 0 sinon
            $participe = $eventsRepository->userAlreadyParticipate($id, $this->getUser());
        }
        else{ //Utilisateur déconnecté
            $participe = -1;
        }


        return $this->render('events/show.html.twig', [
            'event' => $event,
            'participe' => $participe,
            'form' => $form->createView(),
            'formReview' => $formReview->createView(),
            'comments' => $comments,
            'reviews' => $reviews,
            'moyenne' => $moyenne
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
        $event->addUser($user);
        $user->addEvent($event);
        $event->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirectToRoute('participations');
    }


    /**
     * @Route("/event/{id}/send", name="send_comment", methods={"GET","POST"})
     */
    public function send(Request $request, Events $event, $id): Response
    {
            $comment = new Comment();
            $texte = $request->get('comment');
            $texte = $texte['comment'];

            $comment->setComment($texte);
            
            $user = $this->getUser();
            $comment->setUser($user);
            $comment->setEvent($event);
            
            $user->addComment($comment);
            $event->addComment($comment);

            $comment->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('show_event',array('id' => $id));
    }

    /**
     * @Route("/event/{id}/rate", name="send_review", methods={"GET","POST"})
     */
    public function rate(Request $request, Events $event, $id): Response
    {
            $review = new Review();
            $texte = $request->get('review');
            $texte = $texte['comment'];
            $note = $request->get('review');
            $note = intval($note['note']);

            $review->setComment($texte);
            $review->setNote($note);
            
            $user = $this->getUser();
            $review->setUser($user);
            $review->setEvent($event);
            
            $user->addReview($review);
            $event->addReview($review);

            $review->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('show_event',array('id' => $id));
    }

    /**
     * @Route("/event/{id}/leave", name="leave_event", methods={"GET","POST"})
     */
    public function leave(Request $request, Events $event): Response
    {
        $user = $this->getUser();
        $event->removeUser($user);
        $user->removeEvent($event);
        $event->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirectToRoute('participations');
    }
}
