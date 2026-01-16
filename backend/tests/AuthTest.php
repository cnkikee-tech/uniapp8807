<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use think\facade\Db;
use app\common\utils\JwtUtil;
use app\common\model\User;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // 测试前清理数据
        Db::execute('TRUNCATE TABLE users');
    }

    /**
     * 测试用户注册
     */
    public function testUserRegistration()
    {
        $userData = [
            'username' => 'testuser',
            'password' => 'password123',
            'nickname' => '测试用户',
            'email' => 'test@example.com',
            'phone' => '13800138000'
        ];

        $user = new User();
        $result = $user->createUser($userData);

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('testuser', $result['username']);
        $this->assertEquals('测试用户', $result['nickname']);
        $this->assertNotEquals('password123', $result['password']); // 密码应该被加密
    }

    /**
     * 测试用户登录
     */
    public function testUserLogin()
    {
        // 先创建一个测试用户
        $userData = [
            'username' => 'logintest',
            'password' => 'password123',
            'nickname' => '登录测试',
            'email' => 'login@example.com',
            'phone' => '13900139000'
        ];
        
        $user = new User();
        $createdUser = $user->createUser($userData);

        // 测试正确密码登录
        $loginResult = $user->authenticate('logintest', 'password123');
        $this->assertArrayHasKey('id', $loginResult);
        $this->assertEquals('logintest', $loginResult['username']);

        // 测试错误密码登录
        $wrongLogin = $user->authenticate('logintest', 'wrongpassword');
        $this->assertFalse($wrongLogin);
    }

    /**
     * 测试JWT令牌生成和验证
     */
    public function testJwtToken()
    {
        $payload = [
            'user_id' => 123,
            'username' => 'testuser',
            'role' => 'admin'
        ];

        // 生成令牌
        $token = JwtUtil::encode($payload);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);

        // 验证令牌
        $decoded = JwtUtil::decode($token);
        $this->assertEquals(123, $decoded['user_id']);
        $this->assertEquals('testuser', $decoded['username']);
        $this->assertEquals('admin', $decoded['role']);
    }

    /**
     * 测试JWT令牌过期
     */
    public function testJwtTokenExpiration()
    {
        $payload = [
            'user_id' => 123,
            'username' => 'testuser',
            'exp' => time() - 3600 // 1小时前过期
        ];

        $token = JwtUtil::encode($payload);
        
        // 验证过期令牌应该失败
        $this->expectException(\Exception::class);
        JwtUtil::decode($token);
    }

    /**
     * 测试用户信息验证
     */
    public function testUserValidation()
    {
        $userData = [
            'username' => 'a', // 太短的用户名
            'password' => '123', // 太短的密码
            'email' => 'invalid-email', // 无效的邮箱
            'phone' => '123' // 无效的手机号
        ];

        $user = new User();
        $result = $user->validateUserData($userData);
        
        $this->assertArrayHasKey('username', $result);
        $this->assertArrayHasKey('password', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('phone', $result);
    }

    /**
     * 测试用户状态管理
     */
    public function testUserStatus()
    {
        $userData = [
            'username' => 'statustest',
            'password' => 'password123',
            'nickname' => '状态测试',
            'email' => 'status@example.com',
            'phone' => '13700137000',
            'status' => 1
        ];

        $user = new User();
        $createdUser = $user->createUser($userData);

        // 禁用用户
        $user->updateStatus($createdUser['id'], 0);
        $disabledUser = $user->find($createdUser['id']);
        $this->assertEquals(0, $disabledUser['status']);

        // 启用用户
        $user->updateStatus($createdUser['id'], 1);
        $enabledUser = $user->find($createdUser['id']);
        $this->assertEquals(1, $enabledUser['status']);
    }

    protected function tearDown(): void
    {
        // 测试后清理数据
        Db::execute('TRUNCATE TABLE users');
        parent::tearDown();
    }
}