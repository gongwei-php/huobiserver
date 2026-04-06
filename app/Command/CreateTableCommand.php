<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;
use App\Crontab\CreateTableCrontab;

#[Command(name: "crontab:create_table")]
class CreateTableCommand extends HyperfCommand
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('手动执行创建下个月的钱包流水分表');
    }

    public function handle()
    {
        // 直接执行你写的创建表逻辑
        $crontab = new \App\Crontab\CreateTableCrontab();
        $crontab->execute();

        $this->line('✅ 分表创建执行完成', 'info');
    }
}