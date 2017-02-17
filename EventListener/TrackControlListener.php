<?php
/**
 * @author Ali Atasever <aliatasever@gmail.com>
 */

namespace Hayzem\TwitterStreamBundle\EventListener;

use Hayzem\TwitterStreamBundle\Event\TrackEvent;

class TrackControlListener
{
    public static function getSubscribedEvents()
    {
        return array(
            'twitter_stream.event.track.control.start' => 'startTrackHandler',
            'twitter_stream.event.track.control.update' => 'updateTrackHandler',
            'twitter_stream.event.track.control.stop' => 'stopTrackHandler',
        );
    }

    public function startTrackHandler(TrackEvent $trackEvent)
    {
        $trackId = $trackEvent->getTrackId();
        $keywords = $trackEvent->getKeywords();

        exec('php bin/console hayzem:twitter:stream:control start '.$trackId.' '.$keywords);
    }

    public function updateTrackHandler(TrackEvent $trackEvent)
    {
        $trackId = $trackEvent->getTrackId();
        $keywords = $trackEvent->getKeywords();

        exec('php bin/console hayzem:twitter:stream:control restart '.$trackId.' '.$keywords);
    }

    public function stopTrackHandler(TrackEvent $trackEvent)
    {
        $trackId = $trackEvent->getTrackId();

        exec('php bin/console hayzem:twitter:stream:control stop '.$trackId);
    }
}