<?php

declare(strict_types=1);

use think\migration\Migrator;
use think\migration\db\Column;

class CreateUsersTable extends Migrator
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
        $table = $this->table('users', ['engine' => 'InnoDB', 'charset' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
        $table->addColumn('username', 'string', ['limit' => 50, 'null' => false, 'comment' => '用户名'])
              ->addColumn('password', 'string', ['limit' => 255, 'null' => false, 'comment' => '密码'])
              ->addColumn('email', 'string', ['limit' => 100, 'null' => true, 'comment' => '邮箱'])
              ->addColumn('phone', 'string', ['limit' => 20, 'null' => true, 'comment' => '手机号'])
              ->addColumn('real_name', 'string', ['limit' => 50, 'null' => true, 'comment' => '真实姓名'])
              ->addColumn('avatar', 'string', ['limit' => 255, 'null' => true, 'comment' => '头像'])
              ->addColumn('status', 'boolean', ['null' => false, 'default' => 1, 'comment' => '状态：1-启用，0-禁用'])
              ->addColumn('last_login_time', 'datetime', ['null' => true, 'comment' => '最后登录时间'])
              ->addColumn('last_login_ip', 'string', ['limit' => 50, 'null' => true, 'comment' => '最后登录IP'])
              ->addColumn('created_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
              ->addColumn('updated_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'comment' => '更新时间'])
              ->addIndex(['username'], ['unique' => true, 'name' => 'uk_username'])
              ->addIndex(['status'], ['name' => 'idx_status'])
              ->addIndex(['created_at'], ['name' => 'idx_created_at'])
              ->create();
    }
}