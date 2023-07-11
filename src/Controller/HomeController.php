<?php

namespace App\Controller;

use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    #[Route('/', name: 'homepage', options: ['_menu' => 'main', '_menu_title' => 'Home'])]
    public function index(): Response
    {
        $nextEvents = $this->eventService->getNextEvents();
        return $this->render('home.html.twig', [
            'events' => $nextEvents,
        ]);
    }
}