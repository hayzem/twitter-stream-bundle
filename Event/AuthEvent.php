<?php
namespace Hayzem\TwitterStreamBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Ali Atasever <aliatasever@gmail.com>
 */
class AuthEvent extends Event
{
    private $consumerKey;
    private $consumerSecret;
    private $oauthToken;
    private $oauthTokenSecret;
    private $userId;
    private $screenName;
    private $xAuthExpires;

    /**
     * @param mixed $consumerKey
     * @return AuthEvent
     */
    public function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * @param mixed $consumerSecret
     * @return AuthEvent
     */
    public function setConsumerSecret($consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * @param mixed $oauthToken
     * @return AuthEvent
     */
    public function setOauthToken($oauthToken)
    {
        $this->oauthToken = $oauthToken;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOauthToken()
    {
        return $this->oauthToken;
    }

    /**
     * @param mixed $oauthTokenSecret
     * @return AuthEvent
     */
    public function setOauthTokenSecret($oauthTokenSecret)
    {
        $this->oauthTokenSecret = $oauthTokenSecret;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOauthTokenSecret()
    {
        return $this->oauthTokenSecret;
    }

    /**
     * @param mixed $userId
     * @return AuthEvent
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @param $screenName
     * @return AuthEvent
     */
    public function setScreenName($screenName)
    {
        $this->screenName = $screenName;
        return $this;
    }

    /**
     * @param mixed $xAuthExpires
     * @return AuthEvent
     */
    public function setXAuthExpires($xAuthExpires)
    {
        $this->xAuthExpires = $xAuthExpires;
        return $this;
    }
}