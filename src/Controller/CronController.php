<?php

namespace App\Controller;

use App\Service\NotifyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{


    private NotifyService $notifyService;

    public function __construct(NotifyService $notifyService)
    {
        $this->notifyService = $notifyService;
    }

    #[Route('/cron/notify-events')]
    public function notifyEvents(Request $request)
    {
        $this->checkKey($request);
        $notifiedEvents = $this->notifyService->notify();
        return new Response('Benachrichtigung für ' . count($notifiedEvents) . ' Event(s) wurden gesendet.');
    }

    protected function checkKey(Request $request) {
        $key = $request->get('key');
        $cronKey = $_ENV['CRON_KEY'];

        if ($key !== $cronKey) {
            throw new HttpException(403, "Request parameter 'key' is missing or wrong.");
        }
    }
}