<?php

namespace CalendarClient\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;

interface MiddlewareInterface
{
    /**
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     */
    public function __invoke(Request $request, Response $response, callable $next);
}
