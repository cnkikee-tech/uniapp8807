import request from '@/utils/request'

// 获取基本设置
export const getBasicSettings = () => {
  return request({
    url: '/api/settings/basic',
    method: 'get'
  })
}

// 更新基本设置
export const updateBasicSettings = (data) => {
  return request({
    url: '/api/settings/basic',
    method: 'put',
    data
  })
}

// 获取上传设置
export const getUploadSettings = () => {
  return request({
    url: '/api/settings/upload',
    method: 'get'
  })
}

// 更新上传设置
export const updateUploadSettings = (data) => {
  return request({
    url: '/api/settings/upload',
    method: 'put',
    data
  })
}

// 获取API设置
export const getApiSettings = () => {
  return request({
    url: '/api/settings/api',
    method: 'get'
  })
}

// 更新API设置
export const updateApiSettings = (data) => {
  return request({
    url: '/api/settings/api',
    method: 'put',
    data
  })
}

// 获取邮件设置
export const getEmailSettings = () => {
  return request({
    url: '/api/settings/email',
    method: 'get'
  })
}

// 更新邮件设置
export const updateEmailSettings = (data) => {
  return request({
    url: '/api/settings/email',
    method: 'put',
    data
  })
}

// 测试邮件设置
export const testEmailSettings = () => {
  return request({
    url: '/api/settings/email/test',
    method: 'post'
  })
}

// 生成API密钥
export const generateApiKey = () => {
  return request({
    url: '/api/settings/api/generate-key',
    method: 'post'
  })
}