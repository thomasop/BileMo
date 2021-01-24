<?php

namespace App\Handler;

use Symfony\Component\HttpFoundation\Response;

class Cache
{
    public function save($view)
    {
        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(3600);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $this->response($view, $response);

        return $response;
    }

    public function response($view, $response)
    {
        $view->setResponse($response);
    }
}
