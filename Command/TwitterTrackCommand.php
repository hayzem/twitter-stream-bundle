<?php
namespace Hayzem\TwitterStreamBundle\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Hayzem\TwitterStreamBundle\DependencyInjection\GuzzleClientFactory;
use Hayzem\TwitterStreamBundle\Event\StatusEvent;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Ali Atasever <aliatasever@gmail.com>
 */
class TwitterTrackCommand extends ContainerAwareCommand
{
    const BLOCK_SIZE = 1;
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Client $client
     * @param Logger $logger
     */
    public function __construct(Client $client, Logger $logger)
    {
        parent::__construct();

        $this->client = $client;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName('hayzem:twitter:stream:track')
            ->setDescription('Track statuses')
            ->addOption('trackId', null, InputOption::VALUE_REQUIRED, 'Track statuses with tracking id')
            ->addOption('keywords', null, InputOption::VALUE_REQUIRED, 'Tracking keywords separated with comma');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info("Opening twitter stream...");

        $eventDispatcher = $this->getContainer()->get('event_dispatcher');

        $options = $input->getOptions();

        $trackId = $options['trackId'];
        $keywords = $options['keywords'];

        $response = $this->getResponse($keywords);

        $stream = $response->getBody();

        // Read until the stream is closed
        $this->logger->info('Reading stream');
        $line = '';
        while (!$stream->eof()) {
            $line .= $stream->read(static::BLOCK_SIZE);
            while (strstr($line, "\r\n") !== false) {
                list($json, $line) = explode("\r\n", $line, 2);
                $this->logger->debug('Got a line', ['line' => $json]);
                if (trim($json) == '') {
                    $this->logger->debug('Keep alive');
                    continue;
                }
                $data = json_decode($json, true);
                if (isset($data['text'])) {
                    $this->logger->info('Received tweet', ['tweet' => $data]);
                    //Filter replies and retweets
                    if ($data['user']['id_str']) {
                        $statusEvent = new StatusEvent();
                        $statusEvent->setStatusData($data);
                        $statusEvent->setOptions([
                            'trackId' => $trackId
                        ]);
                        try {
                            $eventDispatcher->dispatch('twitter_stream.event.track.status', $statusEvent);
                            $this->logger->notice(
                                'New status',
                                [
                                    'TweetId' => $data['id_str']
                                ]
                            );
                        } catch (\Exception $e) {
                            $this->logger->error(
                                "Failed to dispatch",
                                [
                                    'TweetId' => $data['id_str'],
                                    'Exception' => $e->getMessage()
                                ]
                            );
                        }
                    } else {
                        $this->logger->info('Ignored tweet', ['TweetId' => $data['id_str']]);
                    }
                } else {
                    $this->logger->debug(
                        'Other message',
                        [
                            'message' => $data
                        ]
                    );
                }
            }
        }

        $this->logger->info('Stream finished');
    }

    private function getNewClient()
    {
        $options = [];
        $twitterOauthParameters = [
            'consumer_key' => '',
            'consumer_secret' => '',
            'token' => '',
            'token_secret' => '',
        ];

        $oauth = new Oauth1($twitterOauthParameters);

        return GuzzleClientFactory::get($options, $oauth, $this->logger);
    }

    private function getResponse($keywords)
    {
        $response = null;

        try {
            $response = $this->client->post('statuses/filter.json', [
                'form_params' => [
                    'track' => $keywords
                ],
                'stream' => true
            ]);
        } catch (ClientException $e) {
            $this->logger->error(
                "Twitter refused connection",
                [
                    'Exception' => $e
                ]
            );
            if($e->getCode()) //if too much requests change the client
            {
//                $client = $this->getNewClient();
            }
        }

        return $response;
    }
}
