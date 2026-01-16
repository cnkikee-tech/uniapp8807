import request from '@/utils/request'

// 获取名片列表
export const getCardsList = (params) => {
  return request({
    url: '/api/cards',
    method: 'get',
    params
  })
}

// 获取名片详情
export const getCardDetail = (id) => {
  return request({
    url: `/api/cards/${id}`,
    method: 'get'
  })
}

// 创建名片
export const createCard = (data) => {
  return request({
    url: '/api/cards',
    method: 'post',
    data
  })
}

// 更新名片
export const updateCard = (id, data) => {
  return request({
    url: `/api/cards/${id}`,
    method: 'put',
    data
  })
}

// 删除名片
export const deleteCard = (id) => {
  return request({
    url: `/api/cards/${id}`,
    method: 'delete'
  })
}