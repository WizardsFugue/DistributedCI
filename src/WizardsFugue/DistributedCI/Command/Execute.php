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

class Execute extends Command
{


    protected function configure()
    {
        $this
            ->setName('ci:execute')
            ->setDescription('triggers the CI as configured in config.json')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $manager = new Manager();
        $manager->updateGitInformations();
        //var_dump($manager->getVersions());
        
        $manager->gatherStats($output);
    }
}