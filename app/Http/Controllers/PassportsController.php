<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\Exceptions\OAuthServerException;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;

class PassportsController extends Controller
{
    /**
     * Passport 登陆
     * @param Request $request
     * @param AuthorizationServer $authorizationServer
     * @param ServerRequestInterface $serverRequest
     * @return \Illuminate\Http\JsonResponse|\Psr\Http\Message\ResponseInterface
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function check(Request $request, AuthorizationServer $authorizationServer, ServerRequestInterface $serverRequest)
    {
        try {
            return $authorizationServer->respondToAccessTokenRequest($serverRequest, new Psr7Response)->withStatus(201);
        } catch (OAuthServerException $exception) {
            dd($exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 401);
        }
    }
}
