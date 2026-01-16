# 智能名片系统 - 微信小程序

## 🚀 项目概述

这是一个基于微信原生开发的智能名片小程序，提供名片展示、搜索、详情查看等功能。

## 📋 功能特性

- ✅ 名片列表展示（卡片形式）
- ✅ 下拉刷新功能
- ✅ 上拉加载更多
- ✅ 名片搜索功能
- ✅ 名片详情展示
- ✅ 点击拨打电话
- ✅ 点击发送邮件
- ✅ 地图定位功能
- ✅ 用户个人中心

## 🛠️ 开发环境要求

- 微信开发者工具：最新版本
- 微信小程序基础库：2.30.0+
- 后端API：需要配置正确的API地址

## 📱 如何运行项目

### 1. 打开微信开发者工具

### 2. 导入项目
- 点击「添加项目」
- **重要**：选择 `miniprogram` 目录，而不是项目根目录
- 项目路径：`/Users/gongqi/Documents/trae_projects/uniapp7/miniprogram`

### 3. 配置AppID
- 在 `project.config.json` 中配置你的微信小程序AppID
- 或者使用测试号进行开发

### 4. 配置API地址
在 `app.js` 中配置后端API地址：
```javascript
apiBaseUrl: 'https://your-domain.com/api'
```

### 5. 配置服务器域名
在微信小程序后台配置服务器域名：
- request合法域名：你的API服务器域名
- downloadFile合法域名：图片等资源域名

## 📁 项目结构

```
miniprogram/
├── app.js                 # 小程序应用逻辑
├── app.json              # 小程序全局配置
├── app.wxss              # 小程序全局样式
├── project.config.json   # 项目配置文件
├── sitemap.json          # 小程序站点地图
├── pages/                # 页面目录
│   ├── index/            # 首页
│   │   ├── index.js      # 首页逻辑
│   │   ├── index.wxml    # 首页结构
│   │   └── index.wxss    # 首页样式
│   ├── card-detail/      # 名片详情页
│   │   ├── card-detail.js
│   │   ├── card-detail.wxml
│   │   └── card-detail.wxss
│   └── profile/          # 个人中心
│       ├── profile.js
│       ├── profile.wxml
│       └── profile.wxss
├── images/               # 图片资源
│   ├── home.png          # 首页图标
│   ├── home-active.png   # 首页激活图标
│   ├── profile.png       # 个人中心图标
│   ├── profile-active.png # 个人中心激活图标
│   └── default-avatar.png # 默认头像
└── utils/                # 工具函数
    └── util.js           # 通用工具函数
```

## 🔧 常见问题解决

### 1. app.json 文件未找到错误
**错误信息**：`getAppConfig error with backend: app.json: 在项目根目录未找到 app.json`

**解决方法**：
- 确保在微信开发者工具中选择的是 `miniprogram` 目录，而不是项目根目录
- 检查 `miniprogram/app.json` 文件是否存在

### 2. API请求失败
**可能原因**：
- API地址配置错误
- 服务器域名未在微信小程序后台配置
- 网络连接问题

**解决方法**：
- 检查 `app.js` 中的 `apiBaseUrl` 配置
- 在微信小程序后台配置正确的服务器域名
- 检查网络连接和API服务状态

### 3. 图片无法显示
**可能原因**：
- 图片路径错误
- 图片资源不存在
- 图片域名未配置

**解决方法**：
- 检查图片路径是否正确
- 确保图片文件存在于 `images` 目录
- 配置图片域名到微信小程序后台

## 📝 开发说明

### 数据缓存
小程序使用本地缓存存储用户信息：
- `token`: 用户认证令牌
- `userInfo`: 用户信息

### API请求
所有API请求都通过 `app.globalData.apiBaseUrl` 配置的基础地址发送，请求头包含认证信息。

### 页面跳转
- 使用 `wx.navigateTo` 进行页面跳转
- 使用 `wx.reLaunch` 重新加载页面

## 🚀 部署上线

1. **代码上传**：在微信开发者工具中点击「上传」
2. **版本管理**：在微信小程序后台进行版本管理
3. **提交审核**：提交小程序审核
4. **发布上线**：审核通过后发布上线

## 📞 技术支持

如遇到问题，请检查：
1. 微信开发者工具是否为最新版本
2. 小程序基础库版本是否符合要求
3. API服务是否正常运行
4. 网络连接是否正常

## 📄 相关文档

- [微信小程序开发文档](https://developers.weixin.qq.com/miniprogram/dev/framework/)
- [微信小程序API文档](https://developers.weixin.qq.com/miniprogram/dev/api/)
- [微信小程序组件文档](https://developers.weixin.qq.com/miniprogram/dev/component/)

---

**项目版本**：v1.0  
**最后更新**：2026-01-16  
**开发团队**：智能名片系统开发团队