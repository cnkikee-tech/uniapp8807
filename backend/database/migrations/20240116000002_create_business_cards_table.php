<?php

declare(strict_types=1);

use think\migration\Migrator;
use think\migration\db\Column;

class CreateBusinessCardsTable extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('business_cards', ['engine' => 'InnoDB', 'charset' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
        $table->addColumn('user_id', 'integer', ['limit' => 11, 'null' => true, 'signed' => false, 'comment' => '创建用户ID'])
              ->addColumn('name', 'string', ['limit' => 50, 'null' => false, 'comment' => '姓名'])
              ->addColumn('position', 'string', ['limit' => 100, 'null' => true, 'comment' => '职务'])
              ->addColumn('company', 'string', ['limit' => 200, 'null' => true, 'comment' => '公司名称'])
              ->addColumn('company_address', 'string', ['limit' => 500, 'null' => true, 'comment' => '公司地址'])
              ->addColumn('phone', 'string', ['limit' => 20, 'null' => false, 'comment' => '电话'])
              ->addColumn('email', 'string', ['limit' => 100, 'null' => true, 'comment' => '邮箱'])
              ->addColumn('avatar', 'string', ['limit' => 255, 'null' => true, 'comment' => '头像'])
              ->addColumn('wechat', 'string', ['limit' => 50, 'null' => true, 'comment' => '微信号'])
              ->addColumn('website', 'string', ['limit' => 200, 'null' => true, 'comment' => '公司网站'])
              ->addColumn('description', 'text', ['null' => true, 'comment' => '个人简介'])
              ->addColumn('status', 'boolean', ['null' => false, 'default' => 1, 'comment' => '状态：1-启用，0-禁用'])
              ->addColumn('sort_order', 'integer', ['limit' => 11, 'null' => false, 'default' => 0, 'comment' => '排序'])
              ->addColumn('view_count', 'integer', ['limit' => 11, 'null' => false, 'default' => 0, 'comment' => '查看次数'])
              ->addColumn('created_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
              ->addColumn('updated_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
              ->addIndex(['user_id'], ['name' => 'idx_user_id'])
              ->addIndex(['status'], ['name' => 'idx_status'])
              ->addIndex(['name'], ['name' => 'idx_name'])
              ->addIndex(['company'], ['name' => 'idx_company'])
              ->addIndex(['position'], ['name' => 'idx_position'])
              ->addIndex(['created_at'], ['name' => 'idx_created_at'])
              ->addIndex(['sort_order'], ['name' => 'idx_sort_order'])
              ->create();
    }
}