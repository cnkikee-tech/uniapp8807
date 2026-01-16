<?php

use think\facade\Route;

// 跨域支持
Route::options(':all', function () {
    return json([]);
})->pattern(['all' => '.*']);

// API接口版本v1
Route::group('api/v1', function () {
    
    // 认证相关接口
    Route::group('auth', function () {
        Route::post('login', 'Auth/login'); // 用户登录
        Route::post('logout', 'Auth/logout'); // 用户登出
        Route::get('user', 'Auth/user'); // 获取用户信息
        Route::post('refresh', 'Auth/refresh'); // 刷新Token
    });
    
    // 名片相关接口
    Route::group('cards', function () {
        Route::get('', 'Cards/index'); // 获取名片列表
        Route::get(':id', 'Cards/read'); // 获取名片详情
        Route::post('', 'Cards/save'); // 创建名片
        Route::put(':id', 'Cards/update'); // 更新名片
        Route::delete(':id', 'Cards/delete'); // 删除名片
        Route::delete('batch', 'Cards/batchDelete'); // 批量删除名片
        Route::put(':id/status', 'Cards/updateStatus'); // 更新名片状态
        Route::put(':id/sort', 'Cards/updateSort'); // 更新名片排序
        Route::put(':id/view', 'Cards/view'); // 更新名片浏览次数
    });
    
    // 文件上传接口
    Route::group('upload', function () {
        Route::post('avatar', 'Upload/avatar'); // 上传头像
    });
    
    // 系统配置接口
    Route::group('settings', function () {
        Route::get('', 'Settings/index'); // 获取系统配置
        Route::put('', 'Settings/update'); // 更新系统配置
    });
    
    // 统计接口
    Route::group('stats', function () {
        Route::get('cards', 'Stats/cards'); // 获取名片统计
        Route::get('users', 'Stats/users'); // 获取用户统计
    });
    
})->middleware([\app\api\middleware\Cors::class]);

// 默认路由
Route::get('', function () {
    return json([
        'code' => 200,
        'message' => 'Business Card System API',
        'version' => 'v1.0.0',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

// 404处理
Route::miss(function () {
    return json([
        'code' => 404,
        'message' => '接口不存在',
        'timestamp' => date('Y-m-d H:i:s')
    ], 404);
});