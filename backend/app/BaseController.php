<?php
declare(strict_types=1);

namespace app;

use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {}

    /**
     * 验证数据
     * @access protected
     * @param array $data
     * @param string|array $validate
     * @param array $message
     * @param bool $batch
     * @return array|bool
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    /**
     * 成功响应
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return \think\response\Json
     */
    protected function success($data = null, string $message = 'success', int $code = 200)
    {
        return json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * 错误响应
     * @param string $message
     * @param int $code
     * @param mixed $data
     * @return \think\response\Json
     */
    protected function error(string $message = 'error', int $code = 400, $data = null)
    {
        return json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * 分页响应
     * @param mixed $items
     * @param array $pagination
     * @param string $message
     * @return \think\response\Json
     */
    protected function paginate($items, array $pagination, string $message = 'success')
    {
        return json([
            'code' => 200,
            'message' => $message,
            'data' => [
                'items' => $items,
                'pagination' => $pagination
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}