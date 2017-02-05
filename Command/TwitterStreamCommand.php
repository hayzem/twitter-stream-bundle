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
    protected function configure()
    {
        $this
            ->setName('hayzem:twitter:stream:control')
            ->setDescription('Control twitter stream statuses')
            ->addArgument('mode',InputArgument::REQUIRED,'Tracking keywords separated with comma');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if($input->getArgument('mode') == "start"){
            $process = new Process('php bin/console hayzem:twitter:stream:control istanbul');

            dump('status:'.$process->getStatus());

            if($process->isRunning()){
                dump('çalışıyor !');
                die;
            }

            $process->start();
            $process->wait(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    echo 'ERR > '.$buffer;
                } else {
                    echo 'OUT > '.$buffer;
                }
            });
            dump('çalıştırıldı ! PID:'.$process->getPid());
        }
    }
}