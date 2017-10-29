<?php

namespace CalendarClient\Routes;

use CalendarClient\Middlewares\GoogleCalendarServiceProvider;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class DeleteEventRoute implements RouteCallbackInterface
{
    const ROUTE = '/events/delete';

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

        $requestBody = $request->getParsedBody();

        if (isset($requestBody['event_id'])) {
            try{
                /** @var \GuzzleHttp\Psr7\Response $deleteResponse */
                $deleteResponse = $calendarService->events->delete($calendarId, $requestBody['event_id']);

                if ((string)$deleteResponse->getBody() === '') {
                    return $response->withJson(['message' => 'success']);
                }
            }
            catch (\Google_Service_Exception $e){
                return $response->withJson(['message' => 'failed'], 400);
            }
        }

        return $response->withJson(['message' => 'failed'], 400);
    }
}
