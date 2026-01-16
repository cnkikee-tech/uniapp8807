<?php
declare(strict_types=1);

namespace app\api\controller;

use app\BaseController;
use app\common\model\User;
use app\common\utils\JwtUtil;
use think\facade\Validate;
use think\exception\ValidateException;

/**
 * 认证控制器
 */
class Auth extends BaseController
{
    /**
     * 用户登录
     * @return \think\response\Json
     */
    public function login()
    {
        try {
            $data = $this->request->post();
            
            // 验证输入参数
            $validate = Validate::rule([
                'username' => 'require|length:3,50',
                'password' => 'require|length:6,50'
            ])->message([
                'username.require' => '用户名不能为空',
                'username.length' => '用户名长度必须在3-50个字符之间',
                'password.require' => '密码不能为空',
                'password.length' => '密码长度必须在6-50个字符之间'
            ]);
            
            if (!$validate->check($data)) {
                throw new ValidateException($validate->getError());
            }
            
            // 查找用户
            $user = User::findByUsername($data['username']);
            if (!$user) {
                return $this->error('用户名或密码错误', 401);
            }
            
            // 验证密码
            if (!$user->checkPassword($data['password'])) {
                return $this->error('用户名或密码错误', 401);
            }
            
            // 检查用户状态
            if ($user->status != 1) {
                return $this->error('账号已被禁用', 403);
            }
            
            // 更新登录信息
            $user->updateLoginInfo($this->request->ip());
            
            // 生成JWT Token
            $token = JwtUtil::encode([
                'user_id' => $user->id,
                'username' => $user->username,
                'real_name' => $user->real_name
            ]);
            
            // 返回用户信息（隐藏密码）
            $userData = $user->hidden(['password'])->toArray();
            
            return $this->success([
                'token' => $token,
                'user' => $userData,
                'expires_in' => config('jwt.expire')
            ], '登录成功');
            
        } catch (ValidateException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->error('登录失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 用户登出
     * @return \think\response\Json
     */
    public function logout()
    {
        try {
            $token = $this->request->header('Authorization');
            
            if ($token && preg_match('/Bearer\s+(.*)$/i', $token, $matches)) {
                $token = $matches[1];
                // 将Token加入黑名单
                JwtUtil::addToBlacklist($token);
            }
            
            return $this->success(null, '登出成功');
            
        } catch (\Exception $e) {
            return $this->error('登出失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 获取用户信息
     * @return \think\response\Json
     */
    public function user()
    {
        try {
            $token = $this->request->header('Authorization');
            
            if (!$token || !preg_match('/Bearer\s+(.*)$/i', $token, $matches)) {
                return $this->error('未提供有效的Token', 401);
            }
            
            $token = $matches[1];
            
            // 验证Token
            if (!JwtUtil::verify($token)) {
                return $this->error('Token无效或已过期', 401);
            }
            
            // 检查Token是否在黑名单中
            if (JwtUtil::isBlacklisted($token)) {
                return $this->error('Token已被注销', 401);
            }
            
            // 获取用户信息
            $userInfo = JwtUtil::getUserInfo($token);
            $user = User::findById($userInfo['id']);
            
            if (!$user || $user->status != 1) {
                return $this->error('用户不存在或已被禁用', 404);
            }
            
            // 返回用户信息（隐藏密码）
            $userData = $user->hidden(['password'])->toArray();
            
            return $this->success($userData, '获取用户信息成功');
            
        } catch (\Exception $e) {
            return $this->error('获取用户信息失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 刷新Token
     * @return \think\response\Json
     */
    public function refresh()
    {
        try {
            $token = $this->request->header('Authorization');
            
            if (!$token || !preg_match('/Bearer\s+(.*)$/i', $token, $matches)) {
                return $this->error('未提供有效的Token', 401);
            }
            
            $token = $matches[1];
            
            // 验证Token
            if (!JwtUtil::verify($token)) {
                return $this->error('Token无效或已过期', 401);
            }
            
            // 检查Token是否在黑名单中
            if (JwtUtil::isBlacklisted($token)) {
                return $this->error('Token已被注销', 401);
            }
            
            // 刷新Token
            $newToken = JwtUtil::refresh($token);
            
            // 将旧Token加入黑名单
            JwtUtil::addToBlacklist($token);
            
            return $this->success([
                'token' => $newToken,
                'expires_in' => config('jwt.expire')
            ], 'Token刷新成功');
            
        } catch (\Exception $e) {
            return $this->error('Token刷新失败：' . $e->getMessage(), 500);
        }
    }
}