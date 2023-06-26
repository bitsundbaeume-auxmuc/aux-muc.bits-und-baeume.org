<?php

namespace App\Service;

use App\Exception\MatrixRoomNotPresentException;
use MatrixPhp\MatrixClient;
use MatrixPhp\Room;
use Twig\Environment;

class NotifyService
{
    private EventService $eventService;
    private Environment $twig;

    public function __construct(EventService $eventService, Environment $twig)
    {
        $this->eventService = $eventService;
        $this->twig = $twig;
    }

    public function notify()
    {
        $events = $this->getEventsToNotify();
        foreach ($events as $event) {
            $this->sendNotificationToMatrix($event);
        }
    }

    protected function getEventsToNotify() {
        $events = $this->eventService->getAllEvents();
        return array_filter($events, function($item) {
           $startDate = new \DateTime($item->startDate);
           $now = new \DateTime();

           if ($startDate < $now) {
               return false;
           }

           $difference = $startDate->diff($now);
           if ($difference->days == 7 || $difference->days == 0) {
               return true;
           }

           return false;
        });
    }

    protected function sendNotificationToMatrix($event) {
        $USERNAME = $_ENV['MATRIX_USERNAME'];
        $PASSWORD = $_ENV['MATRIX_PASSWORD'];
        $SERVER = $_ENV['MATRIX_SERVER'];
        $ROOM_ID = $_ENV['MATRIX_ROOM_ID'];

        $client = new MatrixClient($SERVER);
        $client->login($USERNAME, $PASSWORD);

        $rooms = $client->getRooms();
        if (!array_key_exists($ROOM_ID, $rooms)) {
            throw new MatrixRoomNotPresentException("The matrix room with id " . $ROOM_ID . " is not present.");
        }
        /** @var Room $room */
        $room = $rooms[$ROOM_ID];
        $room->sendHtml($this->buildMatrixMessage($event));
    }

    protected function buildMatrixMessage($event): string {
        return $this->twig->render('matrix-event-notification.html.twig', [
            'event' => $event,
        ]);
    }
}