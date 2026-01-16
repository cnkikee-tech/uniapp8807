// pages/profile/profile.js
const app = getApp()

Page({
  data: {
    userInfo: null,
    hasUserInfo: false,
    canIUseGetUserProfile: false
  },

  onLoad() {
    if (wx.getUserProfile) {
      this.setData({
        canIUseGetUserProfile: true
      })
    }
    this.loadUserInfo()
  },

  loadUserInfo() {
    const token = wx.getStorageSync('token')
    if (!token) {
      this.setData({
        hasUserInfo: false
      })
      return
    }

    // 获取用户信息
    wx.request({
      url: `${app.globalData.apiBaseUrl}/auth/user-info`,
      method: 'GET',
      header: {
        'Authorization': `Bearer ${token}`
      },
      success: (res) => {
        if (res.data.code === 200) {
          this.setData({
            userInfo: res.data.data,
            hasUserInfo: true
          })
        } else {
          this.setData({
            hasUserInfo: false
          })
        }
      },
      fail: () => {
        this.setData({
          hasUserInfo: false
        })
      }
    })
  },

  getUserProfile(e) {
    // 推荐使用wx.getUserProfile获取用户信息，开发者每次通过该接口获取用户个人信息均需用户确认
    // 开发者妥善保管用户快速填写的头像昵称，避免重复弹窗
    wx.getUserProfile({
      desc: '用于完善用户资料',
      success: (res) => {
        this.setData({
          userInfo: res.userInfo,
          hasUserInfo: true
        })
      }
    })
  },

  // 退出登录
  logout() {
    wx.showModal({
      title: '提示',
      content: '确定要退出登录吗？',
      success: (res) => {
        if (res.confirm) {
          const token = wx.getStorageSync('token')
          wx.request({
            url: `${app.globalData.apiBaseUrl}/auth/logout`,
            method: 'POST',
            header: {
              'Authorization': `Bearer ${token}`
            },
            success: () => {
              wx.removeStorageSync('token')
              wx.reLaunch({
                url: '/pages/index/index'
              })
            }
          })
        }
      }
    })
  },

  // 查看我的名片
  viewMyCards() {
    wx.navigateTo({
      url: '/pages/my-cards/my-cards'
    })
  },

  // 编辑个人信息
  editProfile() {
    wx.showToast({
      title: '功能开发中',
      icon: 'none'
    })
  }
})