<?php
namespace Hayzem\TwitterStreamBundle\Service;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Monolog\Logger;

/**
 * @author Ali Atasever <aliatasever@gmail.com>
 */
class TwitterStreamService
{
    public function test(){
        dump('twitter stream test');
    }

    public function createGuzzleLoggingMiddleware($messageFormat)
    {
        return Middleware::log(
            $this->logger,
            new MessageFormatter($messageFormat)
        );
    }
}