<?php

declare(strict_types=1);


namespace Plugin\Luckydog\CrudCode;

use Hyperf\Command\Concerns\InteractsWithIO;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class UninstallScript
{
    use InteractsWithIO;

    public function __construct()
    {
        global $argv;
        $this->input = new ArrayInput($argv);
        $this->output = new SymfonyStyle($this->input, new ConsoleOutput());
    }

    public function __invoke()
    {
        $this->output->confirm('Do you want to uninstall the plugin?');
    }
}
