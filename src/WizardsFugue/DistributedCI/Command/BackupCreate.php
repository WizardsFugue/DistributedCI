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

class BackupCreate extends Command
{
    
    
    protected function configure()
    {
        $this
            ->setName('backup:create')
            ->setDescription('creates a backup filled into an archive file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $output->writeln('command ended');
    }
}