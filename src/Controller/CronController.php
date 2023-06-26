<?php

namespace App\Controller;

use App\Service\NotifyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{


    private NotifyService $notifyService;

    public function __construct(NotifyService $notifyService)
    {
        $this->notifyService = $notifyService;
    }

    #[Route('/cron/notify-events')]
    public function notifyEvents()
    {
        $this->notifyService->notify();
    }
}