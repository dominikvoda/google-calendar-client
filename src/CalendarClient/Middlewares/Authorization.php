<?php

namespace CalendarClient\Middlewares;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Authorization implements MiddlewareInterface
{
    const CLIENT_SECRET = '08afd6f9ae0c6017d105b4ce580de885';

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
        if ($request->getParsedBodyParam('client_secret') === self::CLIENT_SECRET) {
            return $next($request, $response);
        }

        return $response->withStatus(401);
    }

}
