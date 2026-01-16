<?php

declare(strict_types=1);

use think\migration\Seeder;
use think\facade\Db;
use think\facade\Hash;

class InitData extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     */
    public function run()
    {
        // 插入默认管理员用户
        $adminUser = [
            'username' => 'admin',
            'password' => Hash::make('123456'),
            'email' => 'admin@example.com',
            'real_name' => '管理员',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        Db::name('users')->insert($adminUser);
        
        // 插入示例名片数据
        $sampleCards = [
            [
                'user_id' => 1,
                'name' => '张三',
                'position' => '技术总监',
                'company' => '科技有限公司',
                'company_address' => '北京市朝阳区建国路88号',
                'phone' => '13800138000',
                'email' => 'zhangsan@example.com',
                'avatar' => '',
                'wechat' => 'zhangsan123',
                'website' => 'https://www.techcompany.com',
                'description' => '资深技术专家，10年开发经验，专注于企业级应用开发',
                'status' => 1,
                'sort_order' => 10,
                'view_count' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => 1,
                'name' => '李四',
                'position' => '产品经理',
                'company' => '创新科技有限公司',
                'company_address' => '上海市浦东新区张江高科技园区',
                'phone' => '13900139000',
                'email' => 'lisi@example.com',
                'avatar' => '',
                'wechat' => 'lisi456',
                'website' => 'https://www.innovation.com',
                'description' => '专注产品设计6年，擅长用户体验设计和产品规划',
                'status' => 1,
                'sort_order' => 20,
                'view_count' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => 1,
                'name' => '王五',
                'position' => '市场总监',
                'company' => '营销顾问有限公司',
                'company_address' => '广州市天河区珠江新城',
                'phone' => '13700137000',
                'email' => 'wangwu@example.com',
                'avatar' => '',
                'wechat' => 'wangwu789',
                'website' => 'https://www.marketing.com',
                'description' => '资深市场营销专家，擅长品牌推广和市场策略制定',
                'status' => 1,
                'sort_order' => 30,
                'view_count' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => 1,
                'name' => '赵六',
                'position' => '设计总监',
                'company' => '创意设计工作室',
                'company_address' => '深圳市南山区科技园',
                'phone' => '13600136000',
                'email' => 'zhaoliu@example.com',
                'avatar' => '',
                'wechat' => 'zhaoliu321',
                'website' => 'https://www.design.com',
                'description' => '资深UI/UX设计师，8年设计经验，擅长品牌视觉设计',
                'status' => 1,
                'sort_order' => 40,
                'view_count' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => 1,
                'name' => '孙七',
                'position' => '运营总监',
                'company' => '互联网运营公司',
                'company_address' => '杭州市西湖区文三路',
                'phone' => '13500135000',
                'email' => 'sunqi@example.com',
                'avatar' => '',
                'wechat' => 'sunqi654',
                'website' => 'https://www.operation.com',
                'description' => '资深互联网运营专家，擅长用户增长和社群运营',
                'status' => 1,
                'sort_order' => 50,
                'view_count' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        Db::name('business_cards')->insertAll($sampleCards);
        
        // 插入系统配置
        $settings = [
            [
                'key' => 'site_name',
                'value' => '智能名片系统',
                'description' => '网站名称',
                'type' => 'string',
                'group' => 'site',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'site_logo',
                'value' => '/static/logo.png',
                'description' => '网站Logo',
                'type' => 'string',
                'group' => 'site',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'cards_per_page',
                'value' => '10',
                'description' => '每页显示名片数量',
                'type' => 'int',
                'group' => 'site',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'key' => 'max_upload_size',
                'value' => '2048',
                'description' => '最大上传文件大小(KB)',
                'type' => 'int',
                'group' => 'upload',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        Db::name('settings')->insertAll($settings);
        
        $this->output->writeln('数据库初始化数据插入成功！');
    }
}