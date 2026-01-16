# 数据库设计文档

## 1. 数据库概述

数据库名称：`business_card_system`
字符集：`utf8mb4`
排序规则：`utf8mb4_unicode_ci`

## 2. 数据表设计

### 2.1 用户表（users）

```sql
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `phone` varchar(20) DEFAULT NULL COMMENT '手机号',
  `real_name` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `status` tinyint(1) DEFAULT 1 COMMENT '状态：1-启用，0-禁用',
  `last_login_time` datetime DEFAULT NULL COMMENT '最后登录时间',
  `last_login_ip` varchar(50) DEFAULT NULL COMMENT '最后登录IP',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';
```

### 2.2 名片表（business_cards）

```sql
CREATE TABLE `business_cards` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '名片ID',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '创建用户ID',
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `position` varchar(100) DEFAULT NULL COMMENT '职务',
  `company` varchar(200) DEFAULT NULL COMMENT '公司名称',
  `company_address` varchar(500) DEFAULT NULL COMMENT '公司地址',
  `phone` varchar(20) NOT NULL COMMENT '电话',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `wechat` varchar(50) DEFAULT NULL COMMENT '微信号',
  `website` varchar(200) DEFAULT NULL COMMENT '公司网站',
  `description` text DEFAULT NULL COMMENT '个人简介',
  `status` tinyint(1) DEFAULT 1 COMMENT '状态：1-启用，0-禁用',
  `sort_order` int(11) DEFAULT 0 COMMENT '排序',
  `view_count` int(11) DEFAULT 0 COMMENT '查看次数',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_name` (`name`),
  KEY `idx_company` (`company`),
  KEY `idx_position` (`position`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='名片表';
```

### 2.3 系统配置表（settings）

```sql
CREATE TABLE `settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `key` varchar(100) NOT NULL COMMENT '配置键',
  `value` text DEFAULT NULL COMMENT '配置值',
  `description` varchar(500) DEFAULT NULL COMMENT '配置描述',
  `type` varchar(20) DEFAULT 'string' COMMENT '配置类型：string、int、bool、json',
  `group` varchar(50) DEFAULT 'default' COMMENT '配置分组',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_key` (`key`),
  KEY `idx_group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表';
```

### 2.4 操作日志表（operation_logs）

```sql
CREATE TABLE `operation_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '用户ID',
  `action` varchar(100) NOT NULL COMMENT '操作动作',
  `table_name` varchar(50) DEFAULT NULL COMMENT '操作表名',
  `record_id` int(11) unsigned DEFAULT NULL COMMENT '记录ID',
  `old_data` text DEFAULT NULL COMMENT '旧数据',
  `new_data` text DEFAULT NULL COMMENT '新数据',
  `ip_address` varchar(50) DEFAULT NULL COMMENT 'IP地址',
  `user_agent` text DEFAULT NULL COMMENT '用户代理',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_table_name` (`table_name`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='操作日志表';
```

## 3. 表关系设计

### 3.1 实体关系图（ER图）

```
users (用户表)
├── id (PK)
├── username
├── password
├── email
├── phone
├── real_name
├── avatar
├── status
├── last_login_time
├── last_login_ip
├── created_at
└── updated_at

business_cards (名片表)
├── id (PK)
├── user_id (FK → users.id)
├── name
├── position
├── company
├── company_address
├── phone
├── email
├── avatar
├── wechat
├── website
├── description
├── status
├── sort_order
├── view_count
├── created_at
└── updated_at

settings (系统配置表)
├── id (PK)
├── key
├── value
├── description
├── type
├── group
├── created_at
└── updated_at

operation_logs (操作日志表)
├── id (PK)
├── user_id (FK → users.id)
├── action
├── table_name
├── record_id
├── old_data
├── new_data
├── ip_address
├── user_agent
└── created_at
```

### 3.2 关系说明

1. **users → business_cards**：一对多关系
   - 一个用户可以创建多个名片
   - 通过 `business_cards.user_id` 关联

2. **users → operation_logs**：一对多关系
   - 一个用户可以有多个操作日志
   - 通过 `operation_logs.user_id` 关联

## 4. 索引设计

### 4.1 主要索引

1. **users表索引**
   - `uk_username`：用户名唯一索引
   - `idx_status`：状态索引，用于筛选启用用户
   - `idx_created_at`：创建时间索引，用于排序

2. **business_cards表索引**
   - `idx_user_id`：用户ID索引，用于查询用户的名片
   - `idx_status`：状态索引，用于筛选启用的名片
   - `idx_name`：姓名字段索引，用于搜索
   - `idx_company`：公司字段索引，用于搜索
   - `idx_position`：职务字段索引，用于搜索
   - `idx_created_at`：创建时间索引，用于排序
   - `idx_sort_order`：排序字段索引

3. **settings表索引**
   - `uk_key`：配置键唯一索引
   - `idx_group`：分组索引，用于按组查询配置

4. **operation_logs表索引**
   - `idx_user_id`：用户ID索引
   - `idx_action`：操作动作索引
   - `idx_table_name`：表名索引
   - `idx_created_at`：创建时间索引

## 5. 数据完整性约束

### 5.1 主键约束
- 所有表都使用自增ID作为主键
- 主键字段名为 `id`，类型为 `int(11) unsigned`

### 5.2 唯一约束
- `users.username`：用户名唯一
- `settings.key`：配置键唯一

### 5.3 外键约束
- `business_cards.user_id` 引用 `users.id`
- `operation_logs.user_id` 引用 `users.id`

## 6. 数据初始化

### 6.1 默认管理员用户
```sql
INSERT INTO `users` (`username`, `password`, `email`, `real_name`, `status`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', '管理员', 1);
```

### 6.2 默认系统配置
```sql
INSERT INTO `settings` (`key`, `value`, `description`, `type`, `group`) VALUES
('site_name', '智能名片系统', '网站名称', 'string', 'site'),
('site_logo', '/static/logo.png', '网站Logo', 'string', 'site'),
('cards_per_page', '10', '每页显示名片数量', 'int', 'site'),
('max_upload_size', '2048', '最大上传文件大小(KB)', 'int', 'upload');
```

## 7. 数据库优化建议

### 7.1 查询优化
- 使用合适的索引覆盖常用查询
- 避免全表扫描
- 使用LIMIT限制返回结果数量

### 7.2 存储优化
- 使用适当的数据类型
- 避免NULL字段过多
- 定期清理无用数据

### 7.3 备份策略
- 定期备份数据库
- 建立主从复制
- 制定灾难恢复计划