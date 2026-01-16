# API接口文档

## 1. 接口规范

### 1.1 基础信息
- 接口版本：v1
- 请求格式：JSON
- 响应格式：JSON
- 编码格式：UTF-8
- 时间格式：YYYY-MM-DD HH:MM:SS

### 1.2 通用响应格式
```json
{
  "code": 200,
  "message": "success",
  "data": {},
  "timestamp": "2024-01-16 10:00:00"
}
```

### 1.3 状态码定义
| 状态码 | 说明 |
|--------|------|
| 200 | 请求成功 |
| 400 | 请求参数错误 |
| 401 | 未授权 |
| 403 | 无权限 |
| 404 | 资源不存在 |
| 422 | 数据验证失败 |
| 500 | 服务器内部错误 |

### 1.4 分页参数
```json
{
  "page": 1,        // 当前页码，默认1
  "page_size": 10,  // 每页数量，默认10
  "total": 100,     // 总记录数
  "total_pages": 10 // 总页数
}
```

## 2. 认证接口

### 2.1 用户登录
**接口地址**：`POST /api/v1/auth/login`

**请求参数**：
```json
{
  "username": "admin",
  "password": "123456"
}
```

**响应参数**：
```json
{
  "code": 200,
  "message": "登录成功",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
      "id": 1,
      "username": "admin",
      "real_name": "管理员",
      "email": "admin@example.com",
      "avatar": ""
    },
    "expires_in": 7200
  }
}
```

### 2.2 用户登出
**接口地址**：`POST /api/v1/auth/logout`

**请求头**：
```
Authorization: Bearer {token}
```

**响应参数**：
```json
{
  "code": 200,
  "message": "登出成功"
}
```

### 2.3 获取用户信息
**接口地址**：`GET /api/v1/auth/user`

**请求头**：
```
Authorization: Bearer {token}
```

**响应参数**：
```json
{
  "code": 200,
  "message": "success",
  "data": {
    "id": 1,
    "username": "admin",
    "real_name": "管理员",
    "email": "admin@example.com",
    "avatar": "",
    "last_login_time": "2024-01-16 09:30:00"
  }
}
```

## 3. 名片管理接口

### 3.1 获取名片列表
**接口地址**：`GET /api/v1/cards`

**请求参数**：
```
?page=1&page_size=10&keyword=张三&status=1
```

**响应参数**：
```json
{
  "code": 200,
  "message": "success",
  "data": {
    "items": [
      {
        "id": 1,
        "name": "张三",
        "position": "技术总监",
        "company": "科技有限公司",
        "phone": "13800138000",
        "email": "zhangsan@example.com",
        "avatar": "",
        "view_count": 100,
        "created_at": "2024-01-15 10:00:00"
      }
    ],
    "pagination": {
      "page": 1,
      "page_size": 10,
      "total": 100,
      "total_pages": 10
    }
  }
}
```

### 3.2 获取名片详情
**接口地址**：`GET /api/v1/cards/{id}`

**响应参数**：
```json
{
  "code": 200,
  "message": "success",
  "data": {
    "id": 1,
    "name": "张三",
    "position": "技术总监",
    "company": "科技有限公司",
    "company_address": "北京市朝阳区xxx路xxx号",
    "phone": "13800138000",
    "email": "zhangsan@example.com",
    "avatar": "",
    "wechat": "zhangsan123",
    "website": "https://www.example.com",
    "description": "资深技术专家，10年开发经验",
    "view_count": 100,
    "status": 1,
    "created_at": "2024-01-15 10:00:00",
    "updated_at": "2024-01-15 10:00:00"
  }
}
```

### 3.3 创建名片
**接口地址**：`POST /api/v1/cards`

**请求参数**：
```json
{
  "name": "李四",
  "position": "产品经理",
  "company": "创新科技有限公司",
  "company_address": "上海市浦东新区xxx路xxx号",
  "phone": "13900139000",
  "email": "lisi@example.com",
  "avatar": "",
  "wechat": "lisi123",
  "website": "https://www.innovation.com",
  "description": "专注产品设计5年",
  "status": 1,
  "sort_order": 0
}
```

**响应参数**：
```json
{
  "code": 200,
  "message": "名片创建成功",
  "data": {
    "id": 2,
    "name": "李四",
    "position": "产品经理",
    "company": "创新科技有限公司",
    "phone": "13900139000",
    "email": "lisi@example.com",
    "created_at": "2024-01-16 10:00:00"
  }
}
```

### 3.4 更新名片
**接口地址**：`PUT /api/v1/cards/{id}`

**请求参数**：
```json
{
  "name": "李四",
  "position": "高级产品经理",
  "company": "创新科技有限公司",
  "company_address": "上海市浦东新区xxx路xxx号",
  "phone": "13900139000",
  "email": "lisi@example.com",
  "avatar": "",
  "wechat": "lisi123",
  "website": "https://www.innovation.com",
  "description": "专注产品设计6年",
  "status": 1,
  "sort_order": 1
}
```

**响应参数**：
```json
{
  "code": 200,
  "message": "名片更新成功",
  "data": {
    "id": 2,
    "name": "李四",
    "position": "高级产品经理",
    "company": "创新科技有限公司",
    "updated_at": "2024-01-16 11:00:00"
  }
}
```

### 3.5 删除名片
**接口地址**：`DELETE /api/v1/cards/{id}`

**响应参数**：
```json
{
  "code": 200,
  "message": "名片删除成功"
}
```

### 3.6 批量删除名片
**接口地址**：`DELETE /api/v1/cards/batch`

**请求参数**：
```json
{
  "ids": [1, 2, 3]
}
```

**响应参数**：
```json
{
  "code": 200,
  "message": "批量删除成功",
  "data": {
    "deleted_count": 3
  }
}
```

### 3.7 更新名片状态
**接口地址**：`PUT /api/v1/cards/{id}/status`

**请求参数**：
```json
{
  "status": 0
}
```

**响应参数**：
```json
{
  "code": 200,
  "message": "状态更新成功"
}
```

### 3.8 更新名片排序
**接口地址**：`PUT /api/v1/cards/{id}/sort`

**请求参数**：
```json
{
  "sort_order": 10
}
```

**响应参数**：
```json
{
  "code": 200,
  "message": "排序更新成功"
}
```

### 3.9 增加名片查看次数
**接口地址**：`POST /api/v1/cards/{id}/view`

**响应参数**：
```json
{
  "code": 200,
  "message": "查看次数已更新",
  "data": {
    "view_count": 101
  }
}
```

## 4. 文件上传接口

### 4.1 上传头像
**接口地址**：`POST /api/v1/upload/avatar`

**请求方式**：`multipart/form-data`

**请求参数**：
- `file`：图片文件（支持jpg、png、gif，最大2MB）

**响应参数**：
```json
{
  "code": 200,
  "message": "上传成功",
  "data": {
    "url": "/uploads/avatar/20240116/abc123.jpg",
    "filename": "abc123.jpg",
    "size": 102400
  }
}
```

## 5. 系统配置接口

### 5.1 获取系统配置
**接口地址**：`GET /api/v1/settings`

**请求参数**：
```
?group=site
```

**响应参数**：
```json
{
  "code": 200,
  "message": "success",
  "data": {
    "site_name": "智能名片系统",
    "site_logo": "/static/logo.png",
    "cards_per_page": "10",
    "max_upload_size": "2048"
  }
}
```

### 5.2 更新系统配置
**接口地址**：`PUT /api/v1/settings`

**请求参数**：
```json
{
  "site_name": "我的名片系统",
  "cards_per_page": "20"
}
```

**响应参数**：
```json
{
  "code": 200,
  "message": "配置更新成功"
}
```

## 6. 统计接口

### 6.1 获取名片统计
**接口地址**：`GET /api/v1/stats/cards`

**响应参数**：
```json
{
  "code": 200,
  "message": "success",
  "data": {
    "total_cards": 100,
    "active_cards": 95,
    "inactive_cards": 5,
    "today_new_cards": 3,
    "total_views": 5000
  }
}
```

### 6.2 获取用户统计
**接口地址**：`GET /api/v1/stats/users`

**响应参数**：
```json
{
  "code": 200,
  "message": "success",
  "data": {
    "total_users": 10,
    "active_users": 8,
    "inactive_users": 2,
    "today_new_users": 1
  }
}
```

## 7. 错误响应格式

### 7.1 参数错误
```json
{
  "code": 400,
  "message": "请求参数错误",
  "errors": {
    "name": ["姓名不能为空"],
    "phone": ["手机号格式不正确"]
  }
}
```

### 7.2 认证失败
```json
{
  "code": 401,
  "message": "未授权访问"
}
```

### 7.3 资源不存在
```json
{
  "code": 404,
  "message": "名片不存在"
}
```

### 7.4 服务器错误
```json
{
  "code": 500,
  "message": "服务器内部错误"
}
```

## 8. 接口限流

- 登录接口：每分钟最多5次
- 普通接口：每分钟最多100次
- 上传接口：每分钟最多10次

## 9. 接口版本控制

- 当前版本：v1
- 版本升级通过URL路径控制
- 向下兼容旧版本接口