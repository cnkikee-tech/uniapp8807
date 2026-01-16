# 智能名片系统部署文档

## 1. 部署概述

本文档详细描述了智能名片系统的部署流程，包括服务器环境配置、应用程序部署、数据库配置、安全设置等各个方面。

## 2. 系统架构

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   微信小程序    │    │   管理后台      │    │   用户访问      │
│   (前端)        │    │   (Vue.js)      │    │   (浏览器)      │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                    ┌─────────────────┐
                    │   负载均衡器    │
                    │   (Nginx)       │
                    └─────────────────┘
                                 │
                    ┌─────────────────┐
                    │   应用服务器    │
                    │   (ThinkPHP)    │
                    └─────────────────┘
                                 │
                    ┌─────────────────┐
                    │   数据库服务器  │
                    │   (MySQL)       │
                    └─────────────────┘
```

## 3. 服务器环境要求

### 3.1 硬件要求
- **最低配置**：
  - CPU：2核
  - 内存：4GB
  - 硬盘：50GB SSD
  - 带宽：5Mbps

- **推荐配置**：
  - CPU：4核
  - 内存：8GB
  - 硬盘：100GB SSD
  - 带宽：10Mbps

### 3.2 软件环境
- **操作系统**：CentOS 7.9+ 或 Ubuntu 20.04+
- **Web服务器**：Nginx 1.20+
- **PHP版本**：7.4+
- **数据库**：MySQL 5.7+ 或 MariaDB 10.3+
- **缓存**：Redis 6.0+
- **队列**：Redis 或 RabbitMQ

## 4. 环境准备

### 4.1 系统初始化

```bash
# 更新系统包
sudo yum update -y  # CentOS
sudo apt update && sudo apt upgrade -y  # Ubuntu

# 安装必要工具
sudo yum install -y git vim wget curl unzip  # CentOS
sudo apt install -y git vim wget curl unzip  # Ubuntu

# 配置防火墙
sudo systemctl start firewalld
sudo systemctl enable firewalld
sudo firewall-cmd --permanent --add-port=80/tcp
sudo firewall-cmd --permanent --add-port=443/tcp
sudo firewall-cmd --permanent --add-port=3306/tcp
sudo firewall-cmd --reload
```

### 4.2 安装Nginx

```bash
# CentOS
sudo yum install -y epel-release
sudo yum install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# Ubuntu
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### 4.3 安装PHP及扩展

```bash
# CentOS
sudo yum install -y https://rpms.remirepo.net/enterprise/remi-release-7.rpm
sudo yum install -y yum-utils
sudo yum-config-manager --disable 'remi-php*'
sudo yum-config-manager --enable remi-php74
sudo yum install -y php php-fpm php-mysql php-redis php-json php-mbstring php-xml php-curl php-gd php-zip

# Ubuntu
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php7.4 php7.4-fpm php7.4-mysql php7.4-redis php7.4-json php7.4-mbstring php7.4-xml php7.4-curl php7.4-gd php7.4-zip
```

### 4.4 安装MySQL

```bash
# CentOS
wget https://dev.mysql.com/get/mysql80-community-release-el7-5.noarch.rpm
sudo rpm -ivh mysql80-community-release-el7-5.noarch.rpm
sudo yum install -y mysql-community-server
sudo systemctl start mysqld
sudo systemctl enable mysqld

# Ubuntu
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql
```

### 4.5 安装Redis

```bash
# CentOS
sudo yum install -y redis
sudo systemctl start redis
sudo systemctl enable redis

# Ubuntu
sudo apt install -y redis-server
sudo systemctl start redis-server
sudo systemctl enable redis-server
```

## 5. 数据库配置

### 5.1 创建数据库和用户

```sql
-- 登录MySQL
mysql -u root -p

-- 创建数据库
CREATE DATABASE business_cards CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 创建用户并授权
CREATE USER 'card_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON business_cards.* TO 'card_user'@'localhost';
FLUSH PRIVILEGES;

-- 退出MySQL
EXIT;
```

### 5.2 导入数据库结构

```bash
# 从项目目录导入数据库结构
cd /path/to/your/project
mysql -u card_user -p business_cards < database/business_cards.sql
```

## 6. 应用程序部署

### 6.1 克隆项目代码

```bash
# 创建项目目录
sudo mkdir -p /var/www/business-cards
cd /var/www/business-cards

# 克隆项目代码（假设使用Git）
sudo git clone https://github.com/your-repo/business-cards.git .

# 设置权限
sudo chown -R nginx:nginx /var/www/business-cards
sudo chmod -R 755 /var/www/business-cards
sudo chmod -R 777 /var/www/business-cards/runtime
sudo chmod -R 777 /var/www/business-cards/public/uploads
```

### 6.2 配置ThinkPHP

```bash
# 复制配置文件
cd /var/www/business-cards
cp .env.example .env

# 编辑配置文件
vim .env
```

配置文件内容：
```env
APP_DEBUG=false
APP_TRACE=false

# 数据库配置
DB_TYPE=mysql
DB_HOST=127.0.0.1
DB_NAME=business_cards
DB_USER=card_user
DB_PASS=your_secure_password
DB_PORT=3306
DB_PREFIX=bc_

# Redis配置
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=

# JWT配置
JWT_SECRET=your_jwt_secret_key
JWT_EXPIRE=7200

# 上传配置
UPLOAD_MAX_SIZE=10485760
UPLOAD_ALLOW_EXT=jpg,jpeg,png,gif,bmp,webp

# 邮件配置
MAIL_SMTP_HOST=smtp.your-domain.com
MAIL_SMTP_PORT=587
MAIL_SMTP_USER=noreply@your-domain.com
MAIL_SMTP_PASS=your_email_password
```

### 6.3 安装Composer依赖

```bash
# 安装Composer（如果未安装）
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 安装项目依赖
cd /var/www/business-cards
sudo -u nginx composer install --no-dev --optimize-autoloader
```

## 7. Nginx配置

### 7.1 创建站点配置文件

```bash
sudo vim /etc/nginx/conf.d/business-cards.conf
```

配置内容：
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/business-cards/public;
    index index.php index.html;

    # 日志配置
    access_log /var/log/nginx/business-cards-access.log;
    error_log /var/log/nginx/business-cards-error.log;

    # 静态资源缓存
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    # ThinkPHP路由
    location / {
        if (!-e $request_filename) {
            rewrite ^(.*)$ /index.php?s=/$1 last;
            break;
        }
    }

    # PHP处理
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/www.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # 超时设置
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
        
        # 缓冲区设置
        fastcgi_buffer_size 64k;
        fastcgi_buffers 4 64k;
        fastcgi_busy_buffers_size 128k;
    }

    # 禁止访问敏感文件
    location ~ /\.(env|git|gitignore|gitattributes|lock)$ {
        deny all;
    }

    # 安全头设置
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
}
```

### 7.2 HTTPS配置（可选但推荐）

```bash
# 安装Certbot
sudo yum install -y certbot python3-certbot-nginx  # CentOS
sudo apt install -y certbot python3-certbot-nginx  # Ubuntu

# 获取SSL证书
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# 自动续期
sudo crontab -e
# 添加以下内容
0 2 * * * /usr/bin/certbot renew --quiet
```

### 7.3 重启Nginx

```bash
# 检查配置
sudo nginx -t

# 重启服务
sudo systemctl restart nginx
```

## 8. PHP-FPM配置

### 8.1 优化PHP-FPM配置

```bash
sudo vim /etc/php-fpm.d/www.conf  # CentOS
sudo vim /etc/php/7.4/fpm/pool.d/www.conf  # Ubuntu
```

优化配置：
```ini
; 用户和组
user = nginx
group = nginx

; 进程管理
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

; 内存限制
php_admin_value[memory_limit] = 256M

; 上传限制
php_admin_value[upload_max_filesize] = 10M
php_admin_value[post_max_size] = 10M

; 执行时间
php_admin_value[max_execution_time] = 300
php_admin_value[max_input_time] = 300

; 时区设置
php_admin_value[date.timezone] = Asia/Shanghai
```

### 8.2 重启PHP-FPM

```bash
sudo systemctl restart php-fpm  # CentOS
sudo systemctl restart php7.4-fpm  # Ubuntu
```

## 9. 微信小程序部署

### 9.1 小程序代码上传

1. 打开微信开发者工具
2. 导入小程序项目（miniprogram目录）
3. 配置小程序后台服务器域名
4. 上传代码到微信服务器
5. 提交审核并发布

### 9.2 小程序配置

在 `miniprogram/app.js` 中配置：
```javascript
// 配置API服务器地址
const config = {
  apiBaseUrl: 'https://your-domain.com/api',
  timeout: 10000
}
```

## 10. 管理后台部署

### 10.1 构建Vue.js应用

```bash
cd /var/www/business-cards/admin

# 安装依赖
npm install

# 构建生产版本
npm run build

# 复制构建文件到public目录
cp -r dist/* ../public/admin/
```

### 10.2 配置后台访问

在Nginx配置中添加：
```nginx
# 管理后台
location /admin {
    alias /var/www/business-cards/public/admin;
    try_files $uri $uri/ /admin/index.html;
    
    # 安全设置
    auth_basic "Admin Area";
    auth_basic_user_file /etc/nginx/.htpasswd;
}
```

## 11. 性能优化

### 11.1 数据库优化

```sql
-- 添加索引
ALTER TABLE `bc_business_cards` ADD INDEX `idx_name` (`name`);
ALTER TABLE `bc_business_cards` ADD INDEX `idx_company` (`company`);
ALTER TABLE `bc_business_cards` ADD INDEX `idx_created_at` (`created_at`);

-- 优化表
OPTIMIZE TABLE `bc_business_cards`;
```

### 11.2 Redis缓存配置

```bash
sudo vim /etc/redis.conf
```

优化配置：
```conf
# 内存限制
maxmemory 512mb
maxmemory-policy allkeys-lru

# 持久化配置
save 900 1
save 300 10
save 60 10000

# 网络配置
tcp-keepalive 300
timeout 300
```

### 11.3 ThinkPHP缓存配置

在 `config/cache.php` 中配置：
```php
return [
    // 默认缓存驱动
    'default' => env('cache.driver', 'redis'),
    
    // 缓存连接配置
    'stores' => [
        'redis' => [
            'type' => 'redis',
            'host' => env('redis.host', '127.0.0.1'),
            'port' => env('redis.port', 6379),
            'password' => env('redis.password', ''),
            'select' => 1,
            'timeout' => 0,
            'persistent' => false,
            'prefix' => 'bc_',
        ],
    ],
];
```

## 12. 安全配置

### 12.1 文件权限设置

```bash
# 设置安全的文件权限
sudo chown -R nginx:nginx /var/www/business-cards
sudo chmod -R 755 /var/www/business-cards
sudo chmod -R 777 /var/www/business-cards/runtime
sudo chmod -R 777 /var/www/business-cards/public/uploads
sudo chmod 600 /var/www/business-cards/.env
```

### 12.2 数据库安全

```sql
-- 删除测试用户
DELETE FROM mysql.user WHERE User='test';

-- 限制远程访问
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');

-- 刷新权限
FLUSH PRIVILEGES;
```

### 12.3 应用安全

在 `.env` 文件中配置：
```env
# 关闭调试模式
APP_DEBUG=false

# 设置JWT密钥
JWT_SECRET=your_very_secure_random_string

# 设置安全的Session配置
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAMESITE=strict
```

## 13. 监控和日志

### 13.1 应用日志

```bash
# 创建日志目录
sudo mkdir -p /var/log/business-cards
sudo chown nginx:nginx /var/log/business-cards

# 配置日志轮转
sudo vim /etc/logrotate.d/business-cards
```

日志轮转配置：
```
/var/log/business-cards/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 640 nginx nginx
    sharedscripts
    postrotate
        systemctl reload nginx
    endscript
}
```

### 13.2 性能监控

安装监控工具：
```bash
# 安装htop
sudo yum install -y htop  # CentOS
sudo apt install -y htop  # Ubuntu

# 安装iotop
sudo yum install -y iotop  # CentOS
sudo apt install -y iotop  # Ubuntu
```

## 14. 备份策略

### 14.1 数据库备份

```bash
# 创建备份脚本
sudo vim /opt/backup-database.sh
```

备份脚本：
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/database"
DB_NAME="business_cards"
DB_USER="card_user"
DB_PASS="your_secure_password"

# 创建备份目录
mkdir -p $BACKUP_DIR

# 备份数据库
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/${DB_NAME}_${DATE}.sql.gz

# 删除7天前的备份
find $BACKUP_DIR -name "*.sql.gz" -mtime +7 -delete
```

### 14.2 文件备份

```bash
# 创建文件备份脚本
sudo vim /opt/backup-files.sh
```

文件备份脚本：
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/files"
SOURCE_DIR="/var/www/business-cards"

# 创建备份目录
mkdir -p $BACKUP_DIR

# 备份文件（排除缓存和日志）
tar -czf $BACKUP_DIR/business-cards_${DATE}.tar.gz \
    --exclude='runtime/cache' \
    --exclude='runtime/log' \
    --exclude='public/uploads' \
    -C $(dirname $SOURCE_DIR) $(basename $SOURCE_DIR)

# 删除30天前的备份
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
```

### 14.3 设置定时任务

```bash
# 编辑crontab
sudo crontab -e

# 添加以下内容
# 每天凌晨2点备份数据库
0 2 * * * /opt/backup-database.sh

# 每周日凌晨3点备份文件
0 3 * * 0 /opt/backup-files.sh
```

## 15. 故障排查

### 15.1 常见问题

1. **500 Internal Server Error**
   - 检查PHP错误日志
   - 检查文件权限
   - 检查.htaccess文件

2. **数据库连接失败**
   - 检查MySQL服务状态
   - 检查数据库配置
   - 检查用户权限

3. **文件上传失败**
   - 检查上传目录权限
   - 检查PHP上传限制
   - 检查Nginx上传限制

### 15.2 日志查看

```bash
# Nginx错误日志
tail -f /var/log/nginx/business-cards-error.log

# PHP错误日志
tail -f /var/log/php-fpm/error.log

# 应用日志
tail -f /var/www/business-cards/runtime/log/*.log
```

## 16. 升级维护

### 16.1 应用升级

```bash
# 备份当前版本
cp -r /var/www/business-cards /var/www/business-cards-backup-$(date +%Y%m%d)

# 拉取新代码
cd /var/www/business-cards
git pull origin main

# 更新依赖
sudo -u nginx composer install --no-dev --optimize-autoloader

# 清理缓存
sudo -u nginx php think cache:clear

# 重启服务
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

### 16.2 数据库升级

```bash
# 备份数据库
mysqldump -u card_user -p business_cards > backup-$(date +%Y%m%d).sql

# 执行数据库升级脚本
mysql -u card_user -p business_cards < upgrade.sql
```

---

**文档版本**：v1.0  
**创建日期**：2026-01-16  
**最后更新**：2026-01-16  
**维护团队**：技术运维团队