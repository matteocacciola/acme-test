<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Setup the platform
 */
class SetupCommand extends ContainerAwareCommand {

    const COMMAND_NAME = 'acme:setup';
    const COMMAND_DESC = 'Initialize the platform';
    
    /** @var \Symfony\Component\Console\Application $application */
    protected $application;

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output) {
        parent::initialize($input, $output);
    }

    /**
     * @see Command
     */
    protected function configure() {
        $this
                ->setName(self::COMMAND_NAME)
                ->setDescription(self::COMMAND_DESC)
                ->setHelp(<<<EOT
The <info>%command.name%</info> command initializes the platform.   
EOT
        );
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->application = $this->getApplication();
        
        $startedAt = microtime(true);
        $initialMemory = memory_get_usage();

        $output->writeln('<info>Executing setup</info>');
        
        /* Drop the database */
        $output->writeln('<info>Dropping database</info>');
        $command = $this->application->find('doctrine:database:drop');
        $arguments = array(
            'command' => 'doctrine:database:drop',
            '--force' => true,
            '--if-exists' => true,
            '--verbose' => true,
            '--env' => 'dev'
        );
        $commandInput = new ArrayInput($arguments);
        $command->run($commandInput, $output);

        /* Create database */
        $output->writeln('<info>Creating database</info>');
        $command = $this->application->find('doctrine:database:create');
        $arguments = array(
            'command' => 'doctrine:database:create',
            '--verbose' => true,
            '--env' => 'dev'
        );
        $commandInput = new ArrayInput($arguments);
        $command->run($commandInput, $output);

        /* Create schema */
        $output->writeln('<info>Creating database schema</info>');
        $command = $this->application->find('doctrine:schema:create');
        $arguments = array(
            'command' => 'doctrine:schema:create',
            '--verbose' => true,
            '--env' => 'dev'
        );
        $commandInput = new ArrayInput($arguments);
        $command->run($commandInput, $output);

        $output->writeln('<comment>Filling example data... </comment>');
        $command = $this->application->find('doctrine:fixtures:load');
        $arguments = array(
            'command' => 'doctrine:fixtures:load',
            '--verbose' => true,
            '--append' => true,
            '--env' => 'dev'
        );
        $commandInput = new ArrayInput($arguments);
        $command->run($commandInput, $output);

        $output->writeln('<comment>SETUP COMPLETED... Done!</comment>');

        $endedAt = microtime(true);
        $finalMemory = memory_get_usage();
        $output->writeln(sprintf('<comment>Execution time: %d seconds</comment>', ($endedAt - $startedAt)));
        $output->writeln(sprintf('<comment>Execution memory: %d KB</comment>', ($finalMemory - $initialMemory) / 1024));
        $output->writeln('<info>Done.</info>');
        
        return true;
    }

}
