import request from '@/utils/request'

// 获取统计数据
export const getStatistics = () => {
  return request({
    url: '/api/statistics',
    method: 'get'
  })
}

// 获取图表数据
export const getChartData = (params) => {
  return request({
    url: '/api/statistics/chart',
    method: 'get',
    params
  })
}

// 获取最新名片
export const getRecentCards = () => {
  return request({
    url: '/api/statistics/recent-cards',
    method: 'get'
  })
}