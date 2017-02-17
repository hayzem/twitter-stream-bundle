<?php
/**
 * @author Ali Atasever <aliatasever@gmail.com>
 */

namespace Hayzem\TwitterStreamBundle\Event;


class TrackEvent
{
    /**
     * @var string
     *
     * comma separated keywords
     */
    private $keywords;

    /**
     * @var string
     */
    private $trackId;

    /**
     * @param string $keywords
     * @return TrackEvent
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param string $trackId
     * @return TrackEvent
     */
    public function setTrackId($trackId)
    {
        $this->trackId = $trackId;
        return $this;
    }

    /**
     * @return string
     */
    public function getTrackId()
    {
        return $this->trackId;
    }
}