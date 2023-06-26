<?php

namespace App\Controller;

use App\Exception\EventNotFoundException;
use App\Service\EventService;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\Entity\TimeZone;
use Eluceo\iCal\Domain\Enum\EventStatus;
use Eluceo\iCal\Domain\ValueObject\Alarm;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\EmailAddress;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\Organizer;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\UniqueIdentifier;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class IcalController extends AbstractController
{
    private EventService $eventService;
    private RouterInterface $router;

    public function __construct(EventService $eventService, RouterInterface $router)
    {
        $this->eventService = $eventService;
        $this->router = $router;
    }

    #[Route("/ics/{id}")]
    public function generateIcal(string $id): Response {
        try {
            $event = $this->eventService->getById($id);

            $icalEvent = new Event(new UniqueIdentifier($event->{'@id'}));

            if (isset($event->name)) {
                $icalEvent->setSummary($event->name);
            }

            if (isset($event->description)) {
                $icalEvent->setDescription($event->description);
            }

            if (isset($event->location)) {
                $location = '';

                if (isset($event->location->name)) {
                    $location .= $event->location->name;
                }

                if (isset($event->location->address)) {
                    $location .= ", ";

                    if (isset($event->location->address->streetAddress)) {
                        $location .= $event->location->address->streetAddress;
                    }

                    if (isset($event->location->address->postalCode)) {
                        $location .= ', ' . $event->location->address->postalCode;
                    }

                    if (isset($event->location->address->addressLocality)) {
                        $location .= ' ' . $event->location->address->addressLocality;
                    }
                }

                $icalEvent->setLocation(new Location($location));
            }

            if (isset($event->organizer)) {
                $organizer = new Organizer(new EmailAddress('kontakt@aux-muc.bits-und-baeume.org'), $event->organizer->name);
                $icalEvent->setOrganizer($organizer);
            }

            $icalEvent->setStatus(EventStatus::CONFIRMED());

            $timeZone = new \DateTimeZone('Europe/Berlin');
            $startDate = new \DateTimeImmutable($event->startDate, $timeZone);
            $endDate = new \DateTimeImmutable($event->endDate, $timeZone);

            $occurence = new TimeSpan(new DateTime($startDate, true), new DateTime($endDate, true));
            $icalEvent->setOccurrence($occurence);

            $interval = new \DateInterval('PT60M');
            $interval->invert = true;

            $icalEvent->addAlarm(new Alarm(new Alarm\DisplayAction("Erinnerung"), new Alarm\RelativeTrigger($interval)));

            $calendar = new Calendar([$icalEvent]);
            $icalTimezone = TimeZone::createFromPhpDateTimeZone($timeZone, $startDate, $endDate);
            $calendar->addTimeZone($icalTimezone);

            $ical = (new CalendarFactory())->createCalendar($calendar);

            $response = new Response((string) $ical);
            $response->headers->add([
                'Content-Type' => 'text/calendar; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="bub-aux-muc-'. $id .'.ics"',
            ]);

            return $response;


        } catch (EventNotFoundException $e) {
            throw new HttpException(404, "Kein Event mit dieser ID gefunden");
        }
    }
}