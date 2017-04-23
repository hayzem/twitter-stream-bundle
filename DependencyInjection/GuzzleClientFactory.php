<?php

namespace Hayzem\TwitterStreamBundle\DependencyInjection;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Hayzem\TwitterStreamBundle\Service\TwitterStreamService;
use Monolog\Logger;

/**
 * @author Ali Atasever <aliatasever@gmail.com>
 */
class GuzzleClientFactory
{
    public static function get(array $options, Oauth1 $oauth1, Logger $logger)
    {
        $handlerStack = HandlerStack::create();

        $handlerStack->push(
            Middleware::log(
                $logger,
                new MessageFormatter()
                //this throws array to string conversation exception
//                new MessageFormatter([
//                    '{method} {uri} HTTP/{version} {req_body}',
//                    'RESPONSE: {code} - {res_body}',
//                ])
            )
        );

        $handlerStack->unshift($oauth1);
        $options['handler'] = $handlerStack;

        $client = null;

        try {
            $client = new Client($options);
            $logger->notice(
                'New client created'
            );
        } catch (\Exception $e) {
            $logger->error(
                "Client failed on connection",
                [
                    'Exception' => $e
                ]
            );
        }

        return $client;
    }
}