<?php

namespace App;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response;

use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;

/**
 * Class Handler
 * @package App
 */
class Handler implements RequestHandlerInterface
{
    private $emitter;
    public function __construct (EmitterInterface $emitter) {
        $this->emitter = $emitter;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface {
        [$receivedContentType] = $request->getHeaders()['content-type'];  
        $response = (new Response())                                           
            ->withStatus(200)                                                  
            ->withAddedHeader('Content-Type', $receivedContentType);           
        $response->getBody()->write($request->getBody()->getContents());       
                                                                               
        return $response; 
    }
}

