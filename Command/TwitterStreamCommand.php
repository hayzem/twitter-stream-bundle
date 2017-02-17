<?php
/**
 * @author Ali Atasever <aliatasever@gmail.com>
 */

namespace Hayzem\TwitterStreamBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class TwitterStreamCommand extends Command
{
    private $command = 'hayzem:twitter:stream:track';

    protected function configure()
    {
        $this
            ->setName('hayzem:twitter:stream:control')
            ->setDescription('Control twitter stream statuses')
            ->addArgument('mode',InputArgument::REQUIRED,'Tracking keywords separated with comma')
            ->addArgument('trackId',InputArgument::REQUIRED,'Tracking ID')
            ->addArgument('keywords',InputArgument::OPTIONAL,'Tracking keywords separated with comma');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mode = $input->getArgument('mode');
        if($mode == "start"){
            /**
             * 1- check if it is already running
             * 2- if not run "php bin/console hayzem:twitter:stream:track istanbul 3"
             */
            if(!$this->isRunning($input->getArgument('trackId'))){
                $output->writeln('Process doesn\'t exist. Starting...');
                $this->start($input->getArgument('keywords'),$input->getArgument('trackId'));
            }

            $output->writeln('Tracking started!');
        }elseif ($mode == "restart"){
            /**
             * 1- check if it is already running
             * 2- if not, start with new keywords and trackId
             * 3- if running, check if the keywords changed
             * 4- if changed, stop and start
             */

        }elseif ($mode == "stop"){
            $pid = $this->isRunning($input->getArgument('trackId'));
            if(!$pid){
                $output->writeln('Tracking is not running!');
                die;
            }
            exec("kill -9 ".$pid);
            $output->writeln('Tracking stopped for '.$input->getArgument('trackId'));
        }
    }

    protected function start($keywords, $trackId)
    {
        exec("php bin/console $this->command $keywords $trackId > /dev/null 2>/dev/null &");
    }

    protected function isRunning($trackId){
        exec("ps -ef | grep -o '.*$this->command.* $trackId' | grep -v grep | awk '{print $2}'", $pids);
        if (count($pids) > 0) {
            return $pids[0];
        }

        return false;
    }

    protected function getKeywords($trackId){

    }
}