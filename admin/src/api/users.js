import request from '@/utils/request'

// 获取用户列表
export const getUsersList = (params) => {
  return request({
    url: '/api/users',
    method: 'get',
    params
  })
}

// 获取用户详情
export const getUserDetail = (id) => {
  return request({
    url: `/api/users/${id}`,
    method: 'get'
  })
}

// 创建用户
export const createUser = (data) => {
  return request({
    url: '/api/users',
    method: 'post',
    data
  })
}

// 更新用户
export const updateUser = (id, data) => {
  return request({
    url: `/api/users/${id}`,
    method: 'put',
    data
  })
}

// 删除用户
export const deleteUser = (id) => {
  return request({
    url: `/api/users/${id}`,
    method: 'delete'
  })
}

// 更新用户状态
export const updateUserStatus = (id, status) => {
  return request({
    url: `/api/users/${id}/status`,
    method: 'put',
    data: { status }
  })
}