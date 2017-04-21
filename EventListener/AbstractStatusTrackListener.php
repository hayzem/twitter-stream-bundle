<?php

namespace Hayzem\TwitterStreamBundle\EventListener;

use Hayzem\TwitterStreamBundle\Event\StatusEvent;
use Hayzem\TwitterStreamBundle\Event\TrackEvent;

/**
 * @author Ali Atasever <aliatasever@gmail.com>
 */
abstract class AbstractStatusTrackListener
{
    public static function getSubscribedEvents()
    {
        return array(
            'twitter_stream.event.track.start' => 'startStatusTrackHandler',
            'twitter_stream.event.track.status' => 'statusEventHandler',
            'twitter_stream.event.track.keywords' => 'keywordsUpdatedHandler',
            'twitter_stream.event.track.stop' => 'stopStatusTrackHandler',
        );
    }

    abstract public function startStatusTrackHandler();
    abstract public function statusEventHandler(StatusEvent $statusEvent);
    abstract public function keywordsUpdatedHandler(TrackEvent $trackEvent);
    abstract public function stopStatusTrackHandler();
}
