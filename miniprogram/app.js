// app.js
App({
  onLaunch() {
    // 展示本地存储能力
    const logs = wx.getStorageSync('logs') || []
    logs.unshift(Date.now())
    wx.setStorageSync('logs', logs)

    // 登录
    wx.login({
      success: res => {
        // 发送 res.code 到后台换取 openId, sessionKey, unionId
      }
    })
  },
  globalData: {
    userInfo: null,
    apiBaseUrl: 'http://localhost:8000', // API基础地址，根据实际情况修改
    appName: '智能名片系统'
  },

  // 全局请求方法
  request(options) {
    const defaultOptions = {
      header: {
        'Content-Type': 'application/json'
      }
    }
    
    // 如果有token，添加到header
    const token = wx.getStorageSync('token')
    if (token) {
      defaultOptions.header['Authorization'] = `Bearer ${token}`
    }

    return new Promise((resolve, reject) => {
      wx.request({
        ...defaultOptions,
        ...options,
        success: (res) => {
          if (res.statusCode >= 200 && res.statusCode < 300) {
            if (res.data.code === 200) {
              resolve(res.data)
            } else {
              // 统一错误处理
              wx.showToast({
                title: res.data.message || '请求失败',
                icon: 'none'
              })
              reject(res.data)
            }
          } else {
            wx.showToast({
              title: '网络错误',
              icon: 'none'
            })
            reject(res)
          }
        },
        fail: (err) => {
          wx.showToast({
            title: '网络连接失败',
            icon: 'none'
          })
          reject(err)
        }
      })
    })
  },

  // 获取用户信息
  getUserInfo() {
    return new Promise((resolve, reject) => {
      if (this.globalData.userInfo) {
        resolve(this.globalData.userInfo)
      } else {
        wx.getSetting({
          success: res => {
            if (res.authSetting['scope.userInfo']) {
              wx.getUserInfo({
                success: res => {
                  this.globalData.userInfo = res.userInfo
                  resolve(res.userInfo)
                },
                fail: reject
              })
            } else {
              reject(new Error('未授权获取用户信息'))
            }
          },
          fail: reject
        })
      }
    })
  },

  // 显示加载提示
  showLoading(title = '加载中...') {
    wx.showLoading({
      title: title,
      mask: true
    })
  },

  // 隐藏加载提示
  hideLoading() {
    wx.hideLoading()
  },

  // 显示成功提示
  showSuccess(message = '操作成功') {
    wx.showToast({
      title: message,
      icon: 'success',
      duration: 2000
    })
  },

  // 显示错误提示
  showError(message = '操作失败') {
    wx.showToast({
      title: message,
      icon: 'none',
      duration: 2000
    })
  }
})