<?php
declare(strict_types=1);

namespace app\common\model;

use think\Model;
use think\facade\Hash;

/**
 * 用户模型
 */
class User extends Model
{
    /**
     * 数据表名
     * @var string
     */
    protected $name = 'users';

    /**
     * 自动时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 创建时间字段
     * @var string
     */
    protected $createTime = 'created_at';

    /**
     * 更新时间字段
     * @var string
     */
    protected $updateTime = 'updated_at';

    /**
     * 字段类型转换
     * @var array
     */
    protected $type = [
        'status' => 'integer',
        'last_login_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * 密码修改器
     * @param string $value
     * @return string
     */
    public function setPasswordAttr(string $value): string
    {
        return Hash::make($value);
    }

    /**
     * 检查密码
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    /**
     * 更新最后登录信息
     * @param string $ip
     * @return bool
     */
    public function updateLoginInfo(string $ip): bool
    {
        $this->last_login_time = date('Y-m-d H:i:s');
        $this->last_login_ip = $ip;
        return $this->save();
    }

    /**
     * 根据用户名查找用户
     * @param string $username
     * @return User|null
     */
    public static function findByUsername(string $username): ?User
    {
        return self::where('username', $username)->find();
    }

    /**
     * 根据ID查找用户
     * @param int $id
     * @return User|null
     */
    public static function findById(int $id): ?User
    {
        return self::find($id);
    }

    /**
     * 获取启用状态的用户列表
     * @param int $page
     * @param int $pageSize
     * @return \think\Paginator
     */
    public static function getActiveUsers(int $page = 1, int $pageSize = 10)
    {
        return self::where('status', 1)
            ->order('created_at', 'desc')
            ->paginate($pageSize, false, ['page' => $page]);
    }

    /**
     * 搜索用户
     * @param string $keyword
     * @param int $page
     * @param int $pageSize
     * @return \think\Paginator
     */
    public static function searchUsers(string $keyword, int $page = 1, int $pageSize = 10)
    {
        return self::where('status', 1)
            ->where(function ($query) use ($keyword) {
                $query->where('username', 'like', "%{$keyword}%")
                    ->whereOr('real_name', 'like', "%{$keyword}%")
                    ->whereOr('email', 'like', "%{$keyword}%");
            })
            ->order('created_at', 'desc')
            ->paginate($pageSize, false, ['page' => $page]);
    }
}