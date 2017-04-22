<?php
/**
 * @author Ali Atasever <aliatasever@gmail.com>
 */

namespace Hayzem\TwitterStreamBundle\EventListener;

use Hayzem\TwitterStreamBundle\Event\TrackEvent;
use Monolog\Logger;

class TrackControlListener
{
    /**
     * @var string
     */
    protected $command;
    protected $commandTail;
    private $kernelRootDir;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct($kernelRootDir, Logger $logger)
    {
        $this->kernelRootDir = str_replace('/app', '', $kernelRootDir);
        $this->command = $this->kernelRootDir.'/bin/console hayzem:twitter:stream:control';
        $this->commandTail = ' > /dev/null 2>/dev/null &';
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'twitter_stream.event.track.control.start' => 'startTrackHandler',
            'twitter_stream.event.track.control.restart' => 'restartTrackHandler',
            'twitter_stream.event.track.control.stop' => 'stopTrackHandler',
        );
    }

    public function startTrackHandler(TrackEvent $trackEvent)
    {
        $trackId = $trackEvent->getTrackId();
        $keywords = $trackEvent->getKeywords();

        $this->logger->notice(
            '[twitter] starting tracking keywords',
            [
                'trackId' => $trackId['id_str']
            ]
        );

        exec('php '.$this->command.' start '.$trackId.' "'.$keywords.'" '.$this->commandTail);
    }

    public function restartTrackHandler(TrackEvent $trackEvent)
    {
        $trackId = $trackEvent->getTrackId();
        $keywords = $trackEvent->getKeywords();

        exec('php '.$this->command.' restart '.$trackId.' "'.$keywords.'" '.$this->commandTail);
    }

    public function stopTrackHandler(TrackEvent $trackEvent)
    {
        $trackId = $trackEvent->getTrackId();

        exec('php '.$this->command.' stop '.$trackId.$this->commandTail);
    }
}