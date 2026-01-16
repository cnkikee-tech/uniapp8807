<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use think\facade\Db;
use app\common\model\BusinessCard;
use app\common\model\User;

class CardsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // 测试前清理数据
        Db::execute('TRUNCATE TABLE business_cards');
        Db::execute('TRUNCATE TABLE users');
    }

    /**
     * 测试名片创建
     */
    public function testCardCreation()
    {
        // 先创建用户
        $user = new User();
        $userData = [
            'username' => 'cardcreator',
            'password' => 'password123',
            'nickname' => '名片创建者',
            'email' => 'creator@example.com',
            'phone' => '13800138000'
        ];
        $createdUser = $user->createUser($userData);

        $cardData = [
            'name' => '张三',
            'position' => '产品经理',
            'company' => '科技有限公司',
            'phone' => '13900139000',
            'email' => 'zhangsan@company.com',
            'address' => '北京市朝阳区科技园',
            'user_id' => $createdUser['id']
        ];

        $card = new BusinessCard();
        $result = $card->createCard($cardData);

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('张三', $result['name']);
        $this->assertEquals('产品经理', $result['position']);
        $this->assertEquals('科技有限公司', $result['company']);
    }

    /**
     * 测试名片更新
     */
    public function testCardUpdate()
    {
        // 创建测试数据
        $user = new User();
        $userData = [
            'username' => 'cardupdater',
            'password' => 'password123',
            'nickname' => '名片更新者',
            'email' => 'updater@example.com',
            'phone' => '13800138000'
        ];
        $createdUser = $user->createUser($userData);

        $card = new BusinessCard();
        $cardData = [
            'name' => '李四',
            'position' => '技术总监',
            'company' => '创新科技',
            'phone' => '13900139000',
            'email' => 'lisi@company.com',
            'address' => '上海市浦东新区',
            'user_id' => $createdUser['id']
        ];
        $createdCard = $card->createCard($cardData);

        // 更新名片信息
        $updateData = [
            'name' => '李四（更新）',
            'position' => 'CTO',
            'company' => '创新科技有限公司'
        ];
        
        $updatedCard = $card->updateCard($createdCard['id'], $updateData);

        $this->assertEquals('李四（更新）', $updatedCard['name']);
        $this->assertEquals('CTO', $updatedCard['position']);
        $this->assertEquals('创新科技有限公司', $updatedCard['company']);
    }

    /**
     * 测试名片删除
     */
    public function testCardDeletion()
    {
        // 创建测试数据
        $user = new User();
        $userData = [
            'username' => 'carddeleter',
            'password' => 'password123',
            'nickname' => '名片删除者',
            'email' => 'deleter@example.com',
            'phone' => '13800138000'
        ];
        $createdUser = $user->createUser($userData);

        $card = new BusinessCard();
        $cardData = [
            'name' => '王五',
            'position' => '销售经理',
            'company' => '销售公司',
            'phone' => '13900139000',
            'email' => 'wangwu@company.com',
            'address' => '广州市天河区',
            'user_id' => $createdUser['id']
        ];
        $createdCard = $card->createCard($cardData);

        // 删除名片
        $deleteResult = $card->deleteCard($createdCard['id']);
        $this->assertTrue($deleteResult);

        // 验证名片已被删除
        $deletedCard = $card->find($createdCard['id']);
        $this->assertNull($deletedCard);
    }

    /**
     * 测试名片搜索
     */
    public function testCardSearch()
    {
        // 创建测试数据
        $user = new User();
        $userData = [
            'username' => 'cardsearcher',
            'password' => 'password123',
            'nickname' => '名片搜索者',
            'email' => 'searcher@example.com',
            'phone' => '13800138000'
        ];
        $createdUser = $user->createUser($userData);

        $card = new BusinessCard();
        
        // 创建多个名片用于搜索测试
        $cardsData = [
            [
                'name' => '张三',
                'position' => '产品经理',
                'company' => '科技有限公司',
                'phone' => '13900139000',
                'email' => 'zhangsan@company.com',
                'address' => '北京市朝阳区',
                'user_id' => $createdUser['id']
            ],
            [
                'name' => '李四',
                'position' => '技术总监',
                'company' => '创新科技',
                'phone' => '13900139001',
                'email' => 'lisi@company.com',
                'address' => '上海市浦东新区',
                'user_id' => $createdUser['id']
            ],
            [
                'name' => '王五',
                'position' => '销售经理',
                'company' => '销售公司',
                'phone' => '13900139002',
                'email' => 'wangwu@company.com',
                'address' => '广州市天河区',
                'user_id' => $createdUser['id']
            ]
        ];

        foreach ($cardsData as $cardData) {
            $card->createCard($cardData);
        }

        // 测试按姓名搜索
        $searchResults = $card->searchCards(['name' => '张三']);
        $this->assertCount(1, $searchResults);
        $this->assertEquals('张三', $searchResults[0]['name']);

        // 测试按公司搜索
        $searchResults = $card->searchCards(['company' => '科技']);
        $this->assertCount(2, $searchResults);

        // 测试按职位搜索
        $searchResults = $card->searchCards(['position' => '经理']);
        $this->assertCount(1, $searchResults);
        $this->assertEquals('王五', $searchResults[0]['name']);
    }

    /**
     * 测试名片分页
     */
    public function testCardPagination()
    {
        // 创建测试数据
        $user = new User();
        $userData = [
            'username' => 'cardpaginator',
            'password' => 'password123',
            'nickname' => '名片分页测试',
            'email' => 'paginator@example.com',
            'phone' => '13800138000'
        ];
        $createdUser = $user->createUser($userData);

        $card = new BusinessCard();
        
        // 创建15个名片用于分页测试
        for ($i = 1; $i <= 15; $i++) {
            $cardData = [
                'name' => "用户{$i}",
                'position' => "职位{$i}",
                'company' => "公司{$i}",
                'phone' => "139001390{$i}",
                'email' => "user{$i}@company.com",
                'address' => "地址{$i}",
                'user_id' => $createdUser['id']
            ];
            $card->createCard($cardData);
        }

        // 测试第一页
        $page1 = $card->getCardsList(['page' => 1, 'page_size' => 10]);
        $this->assertCount(10, $page1['list']);
        $this->assertEquals(15, $page1['total']);

        // 测试第二页
        $page2 = $card->getCardsList(['page' => 2, 'page_size' => 10]);
        $this->assertCount(5, $page2['list']);
        $this->assertEquals(15, $page2['total']);
    }

    /**
     * 测试名片数据验证
     */
    public function testCardValidation()
    {
        $card = new BusinessCard();
        
        // 测试无效数据
        $invalidData = [
            'name' => '', // 姓名为空
            'position' => '', // 职位为空
            'company' => '', // 公司为空
            'phone' => '123', // 无效手机号
            'email' => 'invalid-email', // 无效邮箱
        ];

        $errors = $card->validateCardData($invalidData);
        
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('position', $errors);
        $this->assertArrayHasKey('company', $errors);
        $this->assertArrayHasKey('phone', $errors);
        $this->assertArrayHasKey('email', $errors);
    }

    /**
     * 测试名片浏览量统计
     */
    public function testCardViewCount()
    {
        // 创建测试数据
        $user = new User();
        $userData = [
            'username' => 'viewcounter',
            'password' => 'password123',
            'nickname' => '浏览量测试',
            'email' => 'view@example.com',
            'phone' => '13800138000'
        ];
        $createdUser = $user->createUser($userData);

        $card = new BusinessCard();
        $cardData = [
            'name' => '赵六',
            'position' => '市场总监',
            'company' => '市场公司',
            'phone' => '13900139000',
            'email' => 'zhaoliu@company.com',
            'address' => '深圳市南山区',
            'user_id' => $createdUser['id'],
            'view_count' => 0
        ];
        $createdCard = $card->createCard($cardData);

        // 增加浏览量
        $card->incrementViewCount($createdCard['id']);
        $card->incrementViewCount($createdCard['id']);
        $card->incrementViewCount($createdCard['id']);

        $updatedCard = $card->find($createdCard['id']);
        $this->assertEquals(3, $updatedCard['view_count']);
    }

    protected function tearDown(): void
    {
        // 测试后清理数据
        Db::execute('TRUNCATE TABLE business_cards');
        Db::execute('TRUNCATE TABLE users');
        parent::tearDown();
    }
}