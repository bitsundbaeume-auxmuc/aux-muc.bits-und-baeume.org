<?php

namespace App\Service;

use App\Exception\EventNotFoundException;
use Eluceo\iCal\Domain\ValueObject\Date;
use JsonException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;

class EventService
{

    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getAllEvents() : array
    {
        try {
            $content = file_get_contents($this->kernel->getProjectDir() . '/public/events.json');
            return json_decode($content, null, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new HttpException(500, "public/events.json ist kein valides JSON");
        }
    }

    public function getNextEvent() : ?\stdClass
    {
        $events = $this->getAllEvents();

        $events = array_filter($events, function($event) {
            return time() - strtotime($event->startDate) < 0;
        });

        usort($events, function ($a, $b) {
            return strtotime($a->startDate) - strtotime($b->startDate);
        });

        if (count($events) > 0) {
            return $events[0];
        } else {
            return NULL;
        }
    }

    public function getById(string $id): \stdClass
    {
        $events = $this->getAllEvents();

        foreach ($events as $event) {
            if ($event->{'@id'} == $id) {
                return $event;
            }
        }

        throw new EventNotFoundException("Kein Event mit dieser ID gefunden");
    }
}