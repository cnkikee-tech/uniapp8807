// pages/index/index.js
const app = getApp()

Page({
  data: {
    cards: [],
    searchKeyword: '',
    isLoading: false,
    isRefreshing: false,
    isLoadingMore: false,
    hasMore: true,
    page: 1,
    pageSize: 10
  },

  onLoad() {
    this.loadCards()
  },

  onShow() {
    // 页面显示时刷新数据
    if (this.data.cards.length === 0) {
      this.loadCards()
    }
  },

  // 搜索输入
  onSearchInput(e) {
    this.setData({
      searchKeyword: e.detail.value
    })
  },

  // 搜索确认
  onSearchConfirm() {
    this.searchCards()
  },

  // 搜索按钮点击
  onSearch() {
    this.searchCards()
  },

  // 搜索名片
  searchCards() {
    this.setData({
      page: 1,
      cards: [],
      hasMore: true
    })
    this.loadCards()
  },

  // 下拉刷新
  onRefresh() {
    this.setData({
      isRefreshing: true,
      page: 1,
      cards: [],
      hasMore: true
    })
    this.loadCards(() => {
      this.setData({
        isRefreshing: false
      })
      wx.stopPullDownRefresh()
    })
  },

  // 上拉加载更多
  onLoadMore() {
    if (this.data.isLoadingMore || !this.data.hasMore) {
      return
    }
    
    this.setData({
      isLoadingMore: true,
      page: this.data.page + 1
    })
    
    this.loadCards(() => {
      this.setData({
        isLoadingMore: false
      })
    })
  },

  // 加载名片数据
  loadCards(callback) {
    if (this.data.isLoading) {
      return
    }
    
    this.setData({
      isLoading: true
    })

    const params = {
      page: this.data.page,
      page_size: this.data.pageSize
    }

    if (this.data.searchKeyword) {
      params.keyword = this.data.searchKeyword
    }

    app.request({
      url: '/api/v1/cards',
      method: 'GET',
      data: params
    }).then(res => {
      const newCards = res.data.items || []
      const total = res.data.pagination.total
      const currentTotal = this.data.cards.length + newCards.length
      
      this.setData({
        cards: this.data.page === 1 ? newCards : [...this.data.cards, ...newCards],
        hasMore: currentTotal < total
      })
      
      if (callback) {
        callback()
      }
    }).catch(err => {
      console.error('加载名片失败:', err)
      app.showError('加载失败，请重试')
      
      if (callback) {
        callback()
      }
    }).finally(() => {
      this.setData({
        isLoading: false
      })
    })
  },

  // 点击名片进入详情
  onCardTap(e) {
    const cardId = e.currentTarget.dataset.id
    wx.navigateTo({
      url: `/pages/card-detail/card-detail?id=${cardId}`
    })
  },

  // 页面滚动到顶部
  scrollToTop() {
    wx.pageScrollTo({
      scrollTop: 0,
      duration: 300
    })
  },

  // 分享功能
  onShareAppMessage(res) {
    if (res.from === 'button') {
      // 来自页面内转发按钮
      console.log(res.target)
    }
    return {
      title: '智能名片系统',
      path: '/pages/index/index',
      imageUrl: '/images/share-image.png'
    }
  },

  // 分享到朋友圈
  onShareTimeline() {
    return {
      title: '智能名片系统',
      query: '',
      imageUrl: '/images/share-image.png'
    }
  },

  // 下拉刷新配置
  onPullDownRefresh() {
    this.onRefresh()
  },

  // 上拉触底配置
  onReachBottom() {
    this.onLoadMore()
  }
})