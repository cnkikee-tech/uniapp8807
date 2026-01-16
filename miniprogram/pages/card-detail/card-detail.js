// pages/card-detail/card-detail.js
const app = getApp()

Page({
  data: {
    card: {},
    isLoading: true,
    cardId: null
  },

  onLoad(options) {
    if (options.id) {
      this.setData({
        cardId: options.id
      })
      this.loadCardDetail(options.id)
    } else {
      app.showError('名片ID不存在')
      wx.navigateBack()
    }
  },

  // 加载名片详情
  loadCardDetail(cardId) {
    this.setData({
      isLoading: true
    })

    app.request({
      url: `/api/v1/cards/${cardId}`,
      method: 'GET'
    }).then(res => {
      this.setData({
        card: res.data,
        isLoading: false
      })
    }).catch(err => {
      console.error('加载名片详情失败:', err)
      app.showError('加载失败，请重试')
      this.setData({
        isLoading: false
      })
      wx.navigateBack()
    })
  },

  // 点击电话
  onPhoneTap(e) {
    const phone = e.currentTarget.dataset.phone
    if (!phone) {
      app.showError('电话号码不存在')
      return
    }

    wx.showActionSheet({
      itemList: ['拨打电话', '复制号码'],
      success: (res) => {
        if (res.tapIndex === 0) {
          // 拨打电话
          wx.makePhoneCall({
            phoneNumber: phone,
            success: () => {
              console.log('拨打电话成功')
            },
            fail: (err) => {
              console.error('拨打电话失败:', err)
              app.showError('拨打电话失败')
            }
          })
        } else if (res.tapIndex === 1) {
          // 复制号码
          wx.setClipboardData({
            data: phone,
            success: () => {
              app.showSuccess('号码已复制')
            },
            fail: () => {
              app.showError('复制失败')
            }
          })
        }
      }
    })
  },

  // 点击邮箱
  onEmailTap(e) {
    const email = e.currentTarget.dataset.email
    if (!email) {
      app.showError('邮箱地址不存在')
      return
    }

    wx.showActionSheet({
      itemList: ['发送邮件', '复制邮箱'],
      success: (res) => {
        if (res.tapIndex === 0) {
          // 发送邮件
          wx.openEmailClient({
            to: email,
            success: () => {
              console.log('打开邮件客户端成功')
            },
            fail: (err) => {
              console.error('打开邮件客户端失败:', err)
              app.showError('无法打开邮件客户端')
            }
          })
        } else if (res.tapIndex === 1) {
          // 复制邮箱
          wx.setClipboardData({
            data: email,
            success: () => {
              app.showSuccess('邮箱已复制')
            },
            fail: () => {
              app.showError('复制失败')
            }
          })
        }
      }
    })
  },

  // 点击网站
  onWebsiteTap(e) {
    const url = e.currentTarget.dataset.url
    if (!url) {
      app.showError('网站地址不存在')
      return
    }

    wx.showModal({
      title: '提示',
      content: `是否打开网站：${url}`,
      success: (res) => {
        if (res.confirm) {
          wx.setClipboardData({
            data: url,
            success: () => {
              app.showSuccess('网址已复制，请在浏览器中打开')
            },
            fail: () => {
              app.showError('复制失败')
            }
          })
        }
      }
    })
  },

  // 点击地址
  onAddressTap(e) {
    const address = e.currentTarget.dataset.address
    if (!address) {
      app.showError('地址不存在')
      return
    }

    wx.showActionSheet({
      itemList: ['查看地图', '复制地址'],
      success: (res) => {
        if (res.tapIndex === 0) {
          // 查看地图
          wx.getLocation({
            type: 'gcj02',
            success: (locationRes) => {
              wx.openLocation({
                latitude: locationRes.latitude,
                longitude: locationRes.longitude,
                name: this.data.card.company || '公司地址',
                address: address,
                scale: 18,
                success: () => {
                  console.log('打开地图成功')
                },
                fail: (err) => {
                  console.error('打开地图失败:', err)
                  app.showError('无法打开地图')
                }
              })
            },
            fail: () => {
              // 如果无法获取当前位置，直接搜索地址
              wx.setClipboardData({
                data: address,
                success: () => {
                  app.showSuccess('地址已复制，请在地图应用中搜索')
                }
              })
            }
          })
        } else if (res.tapIndex === 1) {
          // 复制地址
          wx.setClipboardData({
            data: address,
            success: () => {
              app.showSuccess('地址已复制')
            },
            fail: () => {
              app.showError('复制失败')
            }
          })
        }
      }
    })
  },

  // 保存联系人
  onSaveContact() {
    const card = this.data.card
    
    wx.showModal({
      title: '保存联系人',
      content: `是否将 ${card.name} 保存到手机通讯录？`,
      success: (res) => {
        if (res.confirm) {
          wx.addPhoneContact({
            firstName: card.name,
            mobilePhoneNumber: card.phone,
            email: card.email || '',
            organization: card.company || '',
            title: card.position || '',
            url: card.website || '',
            success: () => {
              app.showSuccess('联系人保存成功')
            },
            fail: (err) => {
              console.error('保存联系人失败:', err)
              app.showError('保存联系人失败')
            }
          })
        }
      }
    })
  },

  // 分享名片
  onShareCard() {
    const card = this.data.card
    
    wx.showShareMenu({
      withShareTicket: true,
      menus: ['shareAppMessage', 'shareTimeline']
    })

    // 设置分享数据
    this.onShareAppMessage = () => {
      return {
        title: `${card.name}的名片 - ${card.position || ''}`,
        path: `/pages/card-detail/card-detail?id=${card.id}`,
        imageUrl: card.avatar || '/images/share-image.png',
        success: () => {
          app.showSuccess('分享成功')
        },
        fail: () => {
          app.showError('分享失败')
        }
      }
    }

    this.onShareTimeline = () => {
      return {
        title: `${card.name}的名片 - ${card.company || ''}`,
        query: `id=${card.id}`,
        imageUrl: card.avatar || '/images/share-image.png'
      }
    }
  },

  // 页面分享配置
  onShareAppMessage(res) {
    const card = this.data.card
    
    if (res.from === 'button') {
      // 来自页面内转发按钮
      console.log(res.target)
    }
    
    return {
      title: `${card.name}的名片 - ${card.position || ''}`,
      path: `/pages/card-detail/card-detail?id=${card.id}`,
      imageUrl: card.avatar || '/images/share-image.png'
    }
  },

  onShareTimeline() {
    const card = this.data.card
    
    return {
      title: `${card.name}的名片 - ${card.company || ''}`,
      query: `id=${card.id}`,
      imageUrl: card.avatar || '/images/share-image.png'
    }
  },

  // 页面卸载时增加浏览次数
  onUnload() {
    // 这里可以调用API增加浏览次数
    if (this.data.cardId) {
      app.request({
        url: `/api/v1/cards/${this.data.cardId}/view`,
        method: 'POST'
      }).catch(err => {
        console.error('更新浏览次数失败:', err)
      })
    }
  }
})