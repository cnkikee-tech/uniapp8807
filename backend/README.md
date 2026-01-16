# 智能名片系统 - 后端API

## 项目简介

基于ThinkPHP 6.0框架开发的智能名片系统后端API，提供完整的RESTful接口服务。

## 环境要求

- PHP >= 7.4.0
- MySQL >= 5.7
- Composer
- Redis（可选，用于缓存）

## 安装步骤

### 1. 克隆项目
```bash
git clone [项目地址]
cd backend
```

### 2. 安装依赖
```bash
composer install
```

### 3. 配置环境
复制环境配置文件：
```bash
cp .env.example .env
```

编辑 `.env` 文件，配置数据库连接信息：
```
DATABASE_HOSTNAME = 127.0.0.1
DATABASE_DATABASE = business_card_system
DATABASE_USERNAME = root
DATABASE_PASSWORD = your_password
```

### 4. 创建数据库
在MySQL中创建数据库：
```sql
CREATE DATABASE business_card_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. 运行数据库迁移
```bash
php think migrate:run
```

### 6. 初始化数据
```bash
php think seed:run
```

### 7. 启动服务
开发环境：
```bash
php think run
```

生产环境建议使用Nginx + PHP-FPM。

## API接口文档

详细的API接口文档请参考：[API文档](../.trae/documents/api_document.md)

## 主要功能

- 用户认证（JWT）
- 名片CRUD操作
- 文件上传
- 分页查询
- 搜索功能
- 数据统计

## 项目结构

```
backend/
├── app/                    # 应用目录
│   ├── api/               # API模块
│   │   ├── controller/    # 控制器
│   │   ├── middleware/    # 中间件
│   │   └── route/         # 路由配置
│   └── common/            # 公共模块
│       ├── model/         # 数据模型
│       └── utils/         # 工具类
├── config/                # 配置文件
├── database/              # 数据库相关
│   ├── migrations/        # 迁移文件
│   └── seeds/            # 初始化数据
├── runtime/               # 运行时目录
└── vendor/                # Composer依赖
```

## 安全说明

1. 修改JWT密钥：在`.env`文件中设置`JWT_SECRET`
2. 定期更新依赖包
3. 生产环境关闭调试模式
4. 配置HTTPS
5. 设置适当的文件权限

## 性能优化

1. 开启数据库索引
2. 使用Redis缓存
3. 开启OPcache
4. 配置CDN

## 错误处理

系统采用统一的错误响应格式：
```json
{
    "code": 400,
    "message": "错误信息",
    "data": null,
    "timestamp": "2024-01-16 10:00:00"
}
```

## 日志记录

日志文件位于 `runtime/log/` 目录下，按日期分文件存储。

## 联系方式

如有问题，请通过以下方式联系：
- 邮箱：admin@example.com
- 电话：13800138000