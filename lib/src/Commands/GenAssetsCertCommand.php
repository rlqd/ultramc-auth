<?php

namespace Lib\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenAssetsCertCommand extends Command
{
    protected static $defaultName = 'gen-assets-cert';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return self::SUCCESS;
    }
}