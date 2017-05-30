<?php

namespace Hayzem\TwitterStreamBundle\EventListener;

use Hayzem\TwitterStreamBundle\Event\AuthEvent;

/**
 * @author Ali Atasever <aliatasever@gmail.com>
 */
abstract class AbstractAuthListener
{
    public static function getSubscribedEvents()
    {
        return array(
            'twitter_stream.event.auth' => 'newAuthHandler',
        );
    }

    abstract public function newAuthEventHandler(AuthEvent $authEvent);
}
