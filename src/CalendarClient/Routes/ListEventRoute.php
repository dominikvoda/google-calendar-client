<?php

namespace CalendarClient\Routes;

use CalendarClient\Middlewares\GoogleCalendarServiceProvider;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class ListEventRoute implements RouteCallbackInterface
{
    const ROUTE = '/events/list';

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

        $events = $calendarService->events->listEvents($calendarId)->getItems();

        $responseArray = [];

        /** @var \Google_Service_Calendar_Event $event */
        foreach ($events as $event) {
            $data = [
                'id'          => $event->getId(),
                'title'       => $event->getSummary(),
                'description' => $event->getDescription(),
                'start'       => $event->getStart()->getDateTime(),
                'end'         => $event->getEnd()->getDateTime(),
                'status'      => $event->getStatus(),
                'visibility'  => $event->getVisibility(),
            ];

            $responseArray[] = $data;
        }

        return $response->withJson($responseArray);
    }

}
