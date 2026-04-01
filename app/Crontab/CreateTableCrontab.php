<?php

declare(strict_types=1);

namespace App\Crontab;

use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\DbConnection\Db;

#[Crontab(
    name: "create_table",
    rule: "0 0 1,28 * *",
    callback: "execute",
    memo: "每月1号、28号创建下个月表"
)]
class CreateTableCrontab
{
    public function execute()
    {
        $suffix = date('Ym', strtotime('+1 month'));
        $table = 'hb_member_wallet_log_' . $suffix;

        $exists = Db::select("SHOW TABLES LIKE ?", [$table]);
        if ($exists) {
            var_dump("表已存在：" . $table);
            return true;
        }

        // 建表 SQL
        $sql = <<<SQL
            CREATE TABLE `{$table}` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='资产流水表_{$suffix}';
        SQL;

        Db::statement($sql);

        var_dump("表创建成功：" . $table);
        return true;
    }
}