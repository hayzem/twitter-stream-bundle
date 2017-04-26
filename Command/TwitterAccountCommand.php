<?php
namespace Hayzem\TwitterStreamBundle\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Ali Atasever <aliatasever@gmail.com>
 */
class TwitterAccountCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hayzem:twitter:account:token')
            ->setDescription('Get tokens with twitter account info')
            ->addOption('username', null, InputOption::VALUE_REQUIRED, 'Twitter username')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Twitter password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client(['base_url' => 'https://api.twitter.com', 'defaults' => ['auth' => 'oauth']]);

        $oauth = new Oauth1([
            'consumer_key'    => '[your_api_key]',
            'consumer_secret' => '[your_api_secret]'
        ]);

        $res = $client->post('oauth/request_token', [
            'form_params' => [
                'oauth_callback' => 'http://127.0.0.1:8888/hayzem/twitter/tokens',
            ]
        ]);

        $params = (string)$res->getBody();

        parse_str($params);
//
//        $_SESSION['oauth_token'] = $oauth_token;
//        $_SESSION['oauth_token_secret'] = $oauth_token_secret;

        dump($params);
        die;
    }
}