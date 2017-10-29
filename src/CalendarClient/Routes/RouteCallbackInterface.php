<?php

namespace CalendarClient\Routes;

use Slim\Http\Request;
use Slim\Http\Response;

interface RouteCallbackInterface
{
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $arguments
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $arguments): Response;
}
