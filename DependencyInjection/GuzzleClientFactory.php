<?php

namespace Hayzem\TwitterStreamBundle\DependencyInjection;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

/**
 * @author Ali Atasever <aliatasever@gmail.com>
 */
class GuzzleClientFactory
{
    public static function get(array $options, Oauth1 $oauth1)
    {
        $stack = HandlerStack::create();
        $stack->unshift($oauth1);
        $options['handler'] = $stack;

        return new Client($options);
    }
}