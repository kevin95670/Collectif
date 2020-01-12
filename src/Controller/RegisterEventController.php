<?php

namespace App\Controller;

use App\Form\EventType;
use App\Entity\Events;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegisterEventController extends AbstractController
{
    /**
     * @Route("/create/event", name="new_event")
     */
    public function register(Request $request)
    {
        $event = new Events();
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $event->setCreator($user);
            $categories = $form->get('categories')->getData();
            for ($i=0; $i < count($categories); $i++) { 
                $event->addCategory($categories[$i]);
            }
            die();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('index');
        }

        return $this->render(
            'registration/eventRegister.html.twig',
            array('form' => $form->createView())
        );
    }
}