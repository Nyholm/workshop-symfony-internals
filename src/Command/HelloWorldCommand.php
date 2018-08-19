<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorldCommand extends Command
{
    protected static $defaultName = 'hello';

    protected function configure()
    {
        $this
            ->setDescription('Says hello to a user')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name to say hello to.', 'World')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello '.$input->getArgument('name'));
    }
}