<?php
declare(strict_types=1);

namespace app\api\middleware;

use think\Response;

/**
 * 跨域请求中间件
 */
class Cors
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Accept-Language, Content-Language');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
        
        if ($request->isOptions()) {
            return Response::create('', 'html', 200);
        }
        
        return $next($request);
    }
}