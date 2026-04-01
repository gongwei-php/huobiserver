<?php

declare(strict_types=1);

namespace App\Crontab;

use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\DbConnection\Db;

#[Crontab(
    name: "create_table",
    rule: "0 0 1,28 * *",
    callback: "execute",
    memo: "每月1号、28号检查/创建当前月/下个月表"
)]
class CreateTableCrontab
{
    public function execute()
    {
        $suffix1 = date('Ym', strtotime('+1 month'));
        $table1 = 'hb_member_wallet_log_' . $suffix1;

        $exists1 = Db::select("SHOW TABLES LIKE '{$table1}'");
        if (!$exists1) {
            // 建表 SQL
            $sql1 = <<<SQL
                CREATE TABLE `{$table1}` (
                    `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '日志ID',
                    `user_id` int unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                    `change_type` tinyint NOT NULL DEFAULT 0 COMMENT '变动类型 1=充值 2=提现 3=收益 4=扣除',
                    `change_amount` decimal(30,8) NOT NULL DEFAULT 0.00000000 COMMENT '变动金额',
                    `before_balance` decimal(30,8) NOT NULL DEFAULT 0.00000000 COMMENT '变动前余额',
                    `after_balance` decimal(30,8) NOT NULL DEFAULT 0.00000000 COMMENT '变动后余额',
                    `order_no` varchar(64) NOT NULL DEFAULT '' COMMENT '关联订单号',
                    `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
                    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_user_id` (`user_id`),
                    KEY `idx_created_at` (`created_at`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='资产流水表_{$suffix1}';
            SQL;

            Db::statement($sql1);

            var_dump("表创建成功：" . $table1);
        }

        $suffix2 = date('Ym');
        $table2 = 'hb_member_wallet_log_' . $suffix2;

        $exists2 = Db::select("SHOW TABLES LIKE '{$table2}'");
        if (!$exists2) {
            // 建表 SQL
            $sql2 = <<<SQL
                CREATE TABLE `{$table2}` (
                    `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '日志ID',
                    `user_id` int unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                    `change_type` tinyint NOT NULL DEFAULT 0 COMMENT '变动类型 1=充值 2=提现 3=收益 4=扣除',
                    `change_amount` decimal(30,8) NOT NULL DEFAULT 0.00000000 COMMENT '变动金额',
                    `before_balance` decimal(30,8) NOT NULL DEFAULT 0.00000000 COMMENT '变动前余额',
                    `after_balance` decimal(30,8) NOT NULL DEFAULT 0.00000000 COMMENT '变动后余额',
                    `order_no` varchar(64) NOT NULL DEFAULT '' COMMENT '关联订单号',
                    `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
                    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_user_id` (`user_id`),
                    KEY `idx_created_at` (`created_at`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='资产流水表_{$suffix2}';
            SQL;

            Db::statement($sql2);

            var_dump("表创建成功：" . $table2);
        }

        return true;
    }
}