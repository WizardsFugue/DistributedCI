<?php
/**
 * 
 * 
 * 
 * 
 */

namespace WizardsFugue\DistributedCI\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WizardsFugue\DistributedCI\Manager;


class Generate  extends Command
{


    protected function configure()
    {
        $this
            ->setName('ci:generate')
            ->setDescription('generates HTML pages and graphs from results')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $manager = new Manager();
        $manager->updateGitInformations();

        $manager->generateStatistics($output);
    }
}