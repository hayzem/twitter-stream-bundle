<?php

namespace Hayzem\TwitterStreamBundle\Controller;

use Abraham\TwitterOAuth\TwitterOAuth;
use Guzzle\Http\Message\Header;
use Guzzle\Http\Message\Response;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Hayzem\TwitterStreamBundle\Event\AuthEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    /**
     * @Route("/hayzem/twitter/login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $consumerKey = $this->container->getParameter('twitter_consumer_key');
        $consumerSecret = $this->container->getParameter('twitter_consumer_secret');

        $twitter = new TwitterOAuth($consumerKey,$consumerSecret);
        $result = $twitter->oauth('oauth/request_token', [
            'oauth_callback' => 'http://cleantracker.dev/hayzem/twitter/oauth',
        ]);

        $session = new Session();
        $session->set('oauth_token', $result['oauth_token']);
        $session->set('oauth_token_secret', $result['oauth_token_secret']);

        $redirectUrl = $twitter->url('oauth/authenticate', ['oauth_token' => $result['oauth_token']]);

        return $this->redirect($redirectUrl);
    }

    /**
     * @Route("/hayzem/twitter/oauth")
     * @param Request $request
     * @return string
     */
    public function getAction(Request $request)
    {
        $request->query->get('oauth_verifier');

        $session = new Session();
        $session->set('oauth_verifier', $request->query->get('oauth_verifier'));

        $consumerKey = $this->container->getParameter('twitter_consumer_key');
        $consumerSecret = $this->container->getParameter('twitter_consumer_secret');

        $twitter = new TwitterOAuth(
            $consumerKey,
            $consumerSecret,
            $session->get('oauth_token'),
            $session->get('oauth_token_secret')
        );

        $response = $twitter->oauth('oauth/access_token',[
            'oauth_verifier' => $request->query->get('oauth_verifier')
        ]);

        $authEvent = new AuthEvent();
        $authEvent->setConsumerKey($consumerKey);
        $authEvent->setConsumerSecret($consumerSecret);
        $authEvent->setOauthToken($session->get('oauth_token'));
        $authEvent->setOauthTokenSecret($response['oauth_token_secret']);
        $authEvent->setUserId($response['user_id']);
        $authEvent->setScreenName($response['screen_name']);
        $authEvent->setXAuthExpires($response['x_auth_expires']);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->dispatch('twitter_stream.event.auth', $authEvent);

        return $this->redirect('/admin');
    }
}
