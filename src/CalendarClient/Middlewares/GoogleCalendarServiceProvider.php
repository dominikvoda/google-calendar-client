<?php

namespace CalendarClient\Middlewares;

use Google_Service_Calendar;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class GoogleCalendarServiceProvider implements MiddlewareInterface
{
    const SERVICE_NAME = 'googleCalendarService';
    const CALENDAR_ID = 'calendarId';

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
     * @param callable $next
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $config = $this->container->get('config');
        $googleCalendarService = $this->createGoogleCalendarService($config);
        $this->container[self::SERVICE_NAME] = $googleCalendarService;

        return $next($request, $response);
    }

    /**
     * @param array $config
     *
     * @return Google_Service_Calendar
     */
    private function createGoogleCalendarService(array $config): Google_Service_Calendar
    {
        $client = $this->createGoogleClient($config['google']['client']);

        $calendarService = new Google_Service_Calendar($client);
        $this->container[self::CALENDAR_ID] = $config['google']['calendar']['id'];

        return $calendarService;
    }

    /**
     * @param array $config
     *
     * @return \Google_Client
     */
    private function createGoogleClient(array $config): \Google_Client
    {
        $keyFile = __DIR__ . '/../../../' . $config['keyFile'];

        $client = new \Google_Client();
        $client->setApplicationName($config['applicationName']);
        $client->setAuthConfigFile($keyFile);
        $client->useApplicationDefaultCredentials();
        $client->addScope([Google_Service_Calendar::CALENDAR]);

        return $client;
    }
}
