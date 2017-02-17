<?php
namespace Hayzem\TwitterStreamBundle\Command;

use GuzzleHttp\Client;
use Hayzem\TwitterStreamBundle\Event\StatusEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Client $client
     * @param LoggerInterface $logger
     */
    public function __construct(Client $client, LoggerInterface $logger)
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
            ->addArgument('keywords',InputArgument::REQUIRED,'Tracking keywords separated with comma')
            ->addArgument('trackId',InputArgument::REQUIRED,'Track statuses with tracking id');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info("Opening twitter stream...");

        $eventDispatcher = $this->getContainer()->get('event_dispatcher');

        $response = $this->client->post('statuses/filter.json', [
            'form_params' => [
                'track' => $input->getArgument('keywords')
            ],
            'stream' => true
        ]);

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
                            'trackId' => $input->getArgument('trackId')
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
                                    'Exception' => $e
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
}
