<?php

namespace OpenFaaS;

#use Laminas\Http\Request;
#use Laminas\Http\Response;
use Throwable;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Stratigility\Middleware\ErrorResponseGenerator;

use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

use Laminas\Diactoros\ServerRequestFactory;

/**
 * Class Factory
 * @package OpenFaas
 */
class Factory
{
    public function createNewFunctionHandler() {
        $serverRequestFactory = [ServerRequestFactory::class, 'fromGlobals'];
        $emitter = new SapiEmitter();
        
        $errorResponseGenerator = function (Throwable $e) {
            $generator = new ErrorResponseGenerator();
            return $generator($e, new ServerRequest(), new Response());
        };

        $handler = new \App\Handler($emitter);
        
        $runner = new RequestHandlerRunner(
            $handler,
            $emitter,
            $serverRequestFactory,
            $errorResponseGenerator
        );

        $runner->run();

        // $request = new Request();
        // $request->setMethod($_SERVER['HTTP_METHOD']);
        // $request->setUri($_SERVER['HTTP_PATH']);
        // $request->getHeaders()->addHeaders(['Http_Content_Type' => $_SERVER['Http_Content_Type']]);
        // $request->setContent(stream_get_contents(STDIN));
        
        // $response = new Response();
        // (new \App\Handler())->handle($request, $response);
        // echo $response;
    }
}

