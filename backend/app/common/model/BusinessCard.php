<?php
declare(strict_types=1);

namespace app\common\model;

use think\Model;

/**
 * 名片模型
 */
class BusinessCard extends Model
{
    /**
     * 数据表名
     * @var string
     */
    protected $name = 'business_cards';

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
        'sort_order' => 'integer',
        'view_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 关联用户
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 获取启用的名片列表
     * @param int $page
     * @param int $pageSize
     * @return \think\Paginator
     */
    public static function getActiveCards(int $page = 1, int $pageSize = 10)
    {
        return self::where('status', 1)
            ->order('sort_order', 'desc')
            ->order('created_at', 'desc')
            ->paginate($pageSize, false, ['page' => $page]);
    }

    /**
     * 搜索名片
     * @param string $keyword
     * @param int $page
     * @param int $pageSize
     * @return \think\Paginator
     */
    public static function searchCards(string $keyword, int $page = 1, int $pageSize = 10)
    {
        return self::where('status', 1)
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->whereOr('company', 'like', "%{$keyword}%")
                    ->whereOr('position', 'like', "%{$keyword}%")
                    ->whereOr('phone', 'like', "%{$keyword}%")
                    ->whereOr('email', 'like', "%{$keyword}%");
            })
            ->order('sort_order', 'desc')
            ->order('created_at', 'desc')
            ->paginate($pageSize, false, ['page' => $page]);
    }

    /**
     * 根据ID获取名片详情
     * @param int $id
     * @return BusinessCard|null
     */
    public static function findById(int $id): ?BusinessCard
    {
        return self::find($id);
    }

    /**
     * 创建名片
     * @param array $data
     * @return BusinessCard
     */
    public static function createCard(array $data): BusinessCard
    {
        $card = new self();
        $card->save($data);
        return $card;
    }

    /**
     * 更新名片
     * @param int $id
     * @param array $data
     * @return bool
     */
    public static function updateCard(int $id, array $data): bool
    {
        return self::where('id', $id)->update($data) > 0;
    }

    /**
     * 删除名片
     * @param int $id
     * @return bool
     */
    public static function deleteCard(int $id): bool
    {
        return self::where('id', $id)->delete() > 0;
    }

    /**
     * 批量删除名片
     * @param array $ids
     * @return int
     */
    public static function batchDeleteCards(array $ids): int
    {
        return self::whereIn('id', $ids)->delete();
    }

    /**
     * 更新名片状态
     * @param int $id
     * @param int $status
     * @return bool
     */
    public static function updateStatus(int $id, int $status): bool
    {
        return self::where('id', $id)->update(['status' => $status]) > 0;
    }

    /**
     * 更新名片排序
     * @param int $id
     * @param int $sortOrder
     * @return bool
     */
    public static function updateSort(int $id, int $sortOrder): bool
    {
        return self::where('id', $id)->update(['sort_order' => $sortOrder]) > 0;
    }

    /**
     * 增加查看次数
     * @param int $id
     * @return bool
     */
    public static function incrementViewCount(int $id): bool
    {
        return self::where('id', $id)->inc('view_count')->update() > 0;
    }

    /**
     * 获取名片统计信息
     * @return array
     */
    public static function getStats(): array
    {
        $total = self::count();
        $active = self::where('status', 1)->count();
        $inactive = self::where('status', 0)->count();
        $todayNew = self::whereDate('created_at', date('Y-m-d'))->count();
        $totalViews = self::sum('view_count');

        return [
            'total_cards' => $total,
            'active_cards' => $active,
            'inactive_cards' => $inactive,
            'today_new_cards' => $todayNew,
            'total_views' => $totalViews
        ];
    }
}