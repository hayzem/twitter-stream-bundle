<?php

namespace Hayzem\TwitterStreamBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Ali Atasever <aliatasever@gmail.com>
 */
class StatusEvent extends Event
{
    /**
     * @var mixed
     */
    private $statusData;

    /**
     * @var mixed
     */
    private $options;

    /**
     * @param mixed $statusData
     *
     * @return StatusEvent
     */
    public function setStatusData(array $statusData)
    {
        $this->statusData = $statusData;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusData()
    {
        return $this->statusData;
    }

    /**
     * @param mixed $options
     *
     * @return StatusEvent
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }
}
