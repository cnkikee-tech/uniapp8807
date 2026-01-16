<?php
declare(strict_types=1);

namespace app\api\controller;

use app\BaseController;
use app\common\model\BusinessCard;
use app\common\utils\JwtUtil;
use think\facade\Validate;
use think\exception\ValidateException;

/**
 * 名片控制器
 */
class Cards extends BaseController
{
    /**
     * 获取名片列表
     * @return \think\response\Json
     */
    public function index()
    {
        try {
            $page = (int) $this->request->param('page', 1);
            $pageSize = (int) $this->request->param('page_size', 10);
            $keyword = $this->request->param('keyword', '');
            $status = $this->request->param('status', 1);
            
            // 验证分页参数
            if ($page < 1) $page = 1;
            if ($pageSize < 1 || $pageSize > 100) $pageSize = 10;
            
            // 搜索名片
            if (!empty($keyword)) {
                $cards = BusinessCard::searchCards($keyword, $page, $pageSize);
            } else {
                $cards = BusinessCard::getActiveCards($page, $pageSize);
            }
            
            // 过滤状态
            if ($status !== '') {
                $cards = $cards->filter(function ($card) use ($status) {
                    return $card->status == $status;
                });
            }
            
            // 格式化数据
            $items = [];
            foreach ($cards as $card) {
                $items[] = [
                    'id' => $card->id,
                    'name' => $card->name,
                    'position' => $card->position,
                    'company' => $card->company,
                    'phone' => $card->phone,
                    'email' => $card->email,
                    'avatar' => $card->avatar,
                    'view_count' => $card->view_count,
                    'created_at' => $card->created_at
                ];
            }
            
            $pagination = [
                'page' => $cards->currentPage(),
                'page_size' => $cards->listRows(),
                'total' => $cards->total(),
                'total_pages' => $cards->lastPage()
            ];
            
            return $this->paginate($items, $pagination, '获取名片列表成功');
            
        } catch (\Exception $e) {
            return $this->error('获取名片列表失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 获取名片详情
     * @param int $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        try {
            $card = BusinessCard::findById((int) $id);
            
            if (!$card) {
                return $this->error('名片不存在', 404);
            }
            
            // 增加查看次数
            BusinessCard::incrementViewCount((int) $id);
            
            // 返回详细信息
            $data = [
                'id' => $card->id,
                'name' => $card->name,
                'position' => $card->position,
                'company' => $card->company,
                'company_address' => $card->company_address,
                'phone' => $card->phone,
                'email' => $card->email,
                'avatar' => $card->avatar,
                'wechat' => $card->wechat,
                'website' => $card->website,
                'description' => $card->description,
                'view_count' => $card->view_count + 1, // 包含本次查看
                'status' => $card->status,
                'created_at' => $card->created_at,
                'updated_at' => $card->updated_at
            ];
            
            return $this->success($data, '获取名片详情成功');
            
        } catch (\Exception $e) {
            return $this->error('获取名片详情失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 创建名片
     * @return \think\response\Json
     */
    public function save()
    {
        try {
            $data = $this->request->post();
            
            // 验证输入参数
            $validate = Validate::rule([
                'name' => 'require|length:2,50',
                'position' => 'length:0,100',
                'company' => 'length:0,200',
                'company_address' => 'length:0,500',
                'phone' => 'require|mobile',
                'email' => 'email',
                'avatar' => 'length:0,255',
                'wechat' => 'length:0,50',
                'website' => 'url',
                'description' => 'length:0,1000',
                'status' => 'in:0,1',
                'sort_order' => 'number'
            ])->message([
                'name.require' => '姓名不能为空',
                'name.length' => '姓名长度必须在2-50个字符之间',
                'position.length' => '职务长度不能超过100个字符',
                'company.length' => '公司名称长度不能超过200个字符',
                'company_address.length' => '公司地址长度不能超过500个字符',
                'phone.require' => '电话不能为空',
                'phone.mobile' => '手机号格式不正确',
                'email.email' => '邮箱格式不正确',
                'avatar.length' => '头像地址长度不能超过255个字符',
                'wechat.length' => '微信号长度不能超过50个字符',
                'website.url' => '网站地址格式不正确',
                'description.length' => '个人简介长度不能超过1000个字符',
                'status.in' => '状态值不正确',
                'sort_order.number' => '排序值必须是数字'
            ]);
            
            if (!$validate->check($data)) {
                throw new ValidateException($validate->getError());
            }
            
            // 获取当前用户信息
            $userInfo = $this->getCurrentUser();
            if ($userInfo) {
                $data['user_id'] = $userInfo['id'];
            }
            
            // 设置默认值
            $data['status'] = $data['status'] ?? 1;
            $data['sort_order'] = $data['sort_order'] ?? 0;
            $data['view_count'] = 0;
            
            // 创建名片
            $card = BusinessCard::createCard($data);
            
            // 返回创建的名片信息
            $result = [
                'id' => $card->id,
                'name' => $card->name,
                'position' => $card->position,
                'company' => $card->company,
                'phone' => $card->phone,
                'email' => $card->email,
                'created_at' => $card->created_at
            ];
            
            return $this->success($result, '名片创建成功');
            
        } catch (ValidateException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->error('创建名片失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 更新名片
     * @param int $id
     * @return \think\response\Json
     */
    public function update($id)
    {
        try {
            $data = $this->request->put();
            
            // 验证名片是否存在
            $card = BusinessCard::findById((int) $id);
            if (!$card) {
                return $this->error('名片不存在', 404);
            }
            
            // 验证输入参数
            $validate = Validate::rule([
                'name' => 'length:2,50',
                'position' => 'length:0,100',
                'company' => 'length:0,200',
                'company_address' => 'length:0,500',
                'phone' => 'mobile',
                'email' => 'email',
                'avatar' => 'length:0,255',
                'wechat' => 'length:0,50',
                'website' => 'url',
                'description' => 'length:0,1000',
                'status' => 'in:0,1',
                'sort_order' => 'number'
            ])->message([
                'name.length' => '姓名长度必须在2-50个字符之间',
                'position.length' => '职务长度不能超过100个字符',
                'company.length' => '公司名称长度不能超过200个字符',
                'company_address.length' => '公司地址长度不能超过500个字符',
                'phone.mobile' => '手机号格式不正确',
                'email.email' => '邮箱格式不正确',
                'avatar.length' => '头像地址长度不能超过255个字符',
                'wechat.length' => '微信号长度不能超过50个字符',
                'website.url' => '网站地址格式不正确',
                'description.length' => '个人简介长度不能超过1000个字符',
                'status.in' => '状态值不正确',
                'sort_order.number' => '排序值必须是数字'
            ]);
            
            if (!$validate->check($data)) {
                throw new ValidateException($validate->getError());
            }
            
            // 更新名片
            $result = BusinessCard::updateCard((int) $id, $data);
            
            if (!$result) {
                return $this->error('更新名片失败', 500);
            }
            
            return $this->success([
                'id' => $id,
                'updated_at' => date('Y-m-d H:i:s')
            ], '名片更新成功');
            
        } catch (ValidateException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->error('更新名片失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 删除名片
     * @param int $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        try {
            $card = BusinessCard::findById((int) $id);
            if (!$card) {
                return $this->error('名片不存在', 404);
            }
            
            $result = BusinessCard::deleteCard((int) $id);
            
            if (!$result) {
                return $this->error('删除名片失败', 500);
            }
            
            return $this->success(null, '名片删除成功');
            
        } catch (\Exception $e) {
            return $this->error('删除名片失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 批量删除名片
     * @return \think\response\Json
     */
    public function batchDelete()
    {
        try {
            $data = $this->request->delete();
            
            // 验证输入参数
            $validate = Validate::rule([
                'ids' => 'require|array|min:1',
                'ids.*' => 'number'
            ])->message([
                'ids.require' => '请选择要删除的名片',
                'ids.array' => '参数格式不正确',
                'ids.min' => '至少选择一张名片',
                'ids.*.number' => '名片ID必须是数字'
            ]);
            
            if (!$validate->check($data)) {
                throw new ValidateException($validate->getError());
            }
            
            $ids = $data['ids'];
            $deletedCount = BusinessCard::batchDeleteCards($ids);
            
            return $this->success([
                'deleted_count' => $deletedCount
            ], '批量删除成功');
            
        } catch (ValidateException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->error('批量删除失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 更新名片状态
     * @param int $id
     * @return \think\response\Json
     */
    public function updateStatus($id)
    {
        try {
            $data = $this->request->put();
            
            // 验证输入参数
            $validate = Validate::rule([
                'status' => 'require|in:0,1'
            ])->message([
                'status.require' => '状态不能为空',
                'status.in' => '状态值不正确'
            ]);
            
            if (!$validate->check($data)) {
                throw new ValidateException($validate->getError());
            }
            
            $card = BusinessCard::findById((int) $id);
            if (!$card) {
                return $this->error('名片不存在', 404);
            }
            
            $result = BusinessCard::updateStatus((int) $id, $data['status']);
            
            if (!$result) {
                return $this->error('更新状态失败', 500);
            }
            
            return $this->success(null, '状态更新成功');
            
        } catch (ValidateException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->error('更新状态失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 更新名片排序
     * @param int $id
     * @return \think\response\Json
     */
    public function updateSort($id)
    {
        try {
            $data = $this->request->put();
            
            // 验证输入参数
            $validate = Validate::rule([
                'sort_order' => 'require|number'
            ])->message([
                'sort_order.require' => '排序值不能为空',
                'sort_order.number' => '排序值必须是数字'
            ]);
            
            if (!$validate->check($data)) {
                throw new ValidateException($validate->getError());
            }
            
            $card = BusinessCard::findById((int) $id);
            if (!$card) {
                return $this->error('名片不存在', 404);
            }
            
            $result = BusinessCard::updateSort((int) $id, $data['sort_order']);
            
            if (!$result) {
                return $this->error('更新排序失败', 500);
            }
            
            return $this->success(null, '排序更新成功');
            
        } catch (ValidateException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->error('更新排序失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 更新名片浏览次数
     * @param int $id
     * @return \think\response\Json
     */
    public function view($id)
    {
        try {
            $card = BusinessCard::findById((int) $id);
            if (!$card) {
                return $this->error('名片不存在', 404);
            }
            
            // 增加浏览次数
            BusinessCard::incrementViewCount((int) $id);
            
            return $this->success(null, '浏览次数更新成功');
            
        } catch (\Exception $e) {
            return $this->error('更新浏览次数失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 获取当前用户信息
     * @return array|null
     */
    protected function getCurrentUser(): ?array
    {
        try {
            $token = $this->request->header('Authorization');
            
            if (!$token || !preg_match('/Bearer\s+(.*)$/i', $token, $matches)) {
                return null;
            }
            
            $token = $matches[1];
            
            if (!JwtUtil::verify($token) || JwtUtil::isBlacklisted($token)) {
                return null;
            }
            
            return JwtUtil::getUserInfo($token);
            
        } catch (\Exception $e) {
            return null;
        }
    }
}