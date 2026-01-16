<?php

return [
    // 应用名称
    'app_name'               => 'Business Card System',
    // 应用地址
    'app_host'               => env('app.host', ''),
    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式
    'app_status'             => '',
    // 是否HTTPS
    'is_https'               => false,
    // 域名绑定
    'domain_bind'            => [],
    // 入口自动绑定
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 默认应用
    'default_app'            => 'api',
    // 禁止访问的应用
    'deny_app_list'          => [],
    // 默认时区
    'default_timezone'       => 'Asia/Shanghai',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,
    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问的模块
    'deny_module_list'       => [],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search'   => false,
    // 操作方法绑定到类
    'action_bind_class'      => false,
    // 默认的路由变量规则
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 空操作方法名
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 请求缓存
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],
    // 默认应用调度器
    'dispatch_name'          => 'app\\common\\dispatch\\ApiDispatch',
];