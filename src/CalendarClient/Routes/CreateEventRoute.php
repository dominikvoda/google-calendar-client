<?php

namespace CalendarClient\Routes;

use CalendarClient\Middlewares\GoogleCalendarServiceProvider;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class CreateEventRoute implements RouteCallbackInterface
{
    const ROUTE = '/events/create';

    /**
     * @var Container
     */
    private $container;

    /**
     * GoogleClientProvider constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $arguments
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $arguments): Response
    {
        /** @var \Google_Service_Calendar $calendarService */
        $calendarService = $this->container->get(GoogleCalendarServiceProvider::SERVICE_NAME);
        $calendarId = $this->container->get(GoogleCalendarServiceProvider::CALENDAR_ID);

        $body = $request->getParsedBody();
        $event = $this->createEvent($body);

        $createdEvent = $calendarService->events->insert($calendarId, $event);

        if ($createdEvent instanceof \Google_Service_Calendar_Event) {
            return $response->withJson(['id' => $createdEvent->getId()]);
        }

        return $response->withJson(['message' => 'Can\' create event'], 400);
    }

    /**
     * @param $body
     *
     * @return \Google_Service_Calendar_Event
     */
    private function createEvent($body): \Google_Service_Calendar_Event
    {
        $startTimestamp = $body['timestamp'];
        $endTimestamp = $startTimestamp + 3600;

        $start = new \Google_Service_Calendar_EventDateTime();
        $start->setDateTime(date(DATE_ISO8601, $startTimestamp));

        $end = new \Google_Service_Calendar_EventDateTime();
        $end->setDateTime(date(DATE_ISO8601, $endTimestamp));

        $event = new \Google_Service_Calendar_Event();
        $event->setDescription($this->getDescription($body));
        $event->setSummary($body['title']);
        $event->setStart($start);
        $event->setEnd($end);

        return $event;
    }

    /**
     * @param $body
     *
     * @return string
     */
    private function getDescription($body)
    {
        $request = $body['description'];
        $description = '';
        $description .= sprintf('<b>Jméno: </b> %s<br>', $request['email']);
        $description .= sprintf('<b>E-mail: </b> %s<br>', $request['email']);
        $description .= sprintf('<b>Telefon: </b> %s<br>', $request['telephone']);
        $description .= sprintf('<b>SPZ: </b> %s<br>', $request['spz']);
        $description .= sprintf('<b>Typ: </b> %s<br>', $request['type']);
        $description .= sprintf('<b>Majitel: </b> %s<br>', $request['owner']);
        $description .= sprintf('<b>Poznámka: </b> %s<br>', $request['note']);

        return $description;
    }

}
