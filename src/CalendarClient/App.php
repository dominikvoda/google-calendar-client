<?php

namespace CalendarClient;

use CalendarClient\Middlewares\Authorization;
use CalendarClient\Middlewares\GoogleCalendarServiceProvider;
use CalendarClient\Routes\CreateEventRoute;
use CalendarClient\Routes\DefaultRoute;
use CalendarClient\Routes\DeleteEventRoute;
use CalendarClient\Routes\ListEventRoute;
use Slim\App as SlimApp;
use Slim\Container;

class App
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var Container
     */
    private $container;

    /**
     * App constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->container = $this->createSlimContainer($config);
        $this->slimApp = $this->createSlimApp($this->container);
    }

    /**
     *
     */
    public function run()
    {
        $this->slimApp->run();
    }

    /**
     * @return Container
     */
    public function createSlimContainer(array $config): Container
    {
        $container = new Container($config['slim']['container']);
        $container['config'] = $config;

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return SlimApp
     */
    private function createSlimApp(Container $container)
    {
        $app = new SlimApp($container);

        $this->registerRoutes($app);

        return $app;
    }

    /**
     * @param SlimApp $app
     */
    private function registerRoutes(SlimApp $app)
    {
        $googleClientProvider = new GoogleCalendarServiceProvider($this->container);
        $authorization = new Authorization($this->container);

        $app->get(DefaultRoute::ROUTE, new DefaultRoute());

        $create = $app->post(CreateEventRoute::ROUTE, new CreateEventRoute($this->container));
        $create->add($authorization);
        $create->add($googleClientProvider);

        $delete = $app->post(DeleteEventRoute::ROUTE, new DeleteEventRoute($this->container));
        $delete->add($authorization);
        $delete->add($googleClientProvider);

        $list = $app->post(ListEventRoute::ROUTE, new ListEventRoute($this->container));
        $list->add($authorization);
        $list->add($googleClientProvider);
    }
}
