<?php

namespace CalendarClient\Middlewares;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Authorization implements MiddlewareInterface
{
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
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $config = $this->container->get('config');
        $clientSecret = $config['security']['clientSecret'];
        if ($request->getParsedBodyParam('client_secret') === $clientSecret) {
            return $next($request, $response);
        }

        return $response->withStatus(401);
    }

}
