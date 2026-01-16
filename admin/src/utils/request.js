import axios from 'axios'
import { ElMessage } from 'element-plus'
import { getToken } from '@/utils/auth'
import router from '@/router'

// 创建axios实例
const service = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  timeout: 10000 // 请求超时时间
})

// 请求拦截器
service.interceptors.request.use(
  config => {
    // 在请求发送之前做一些处理
    const token = getToken()
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`
    }
    
    // 设置请求头
    config.headers['Content-Type'] = 'application/json;charset=UTF-8'
    
    return config
  },
  error => {
    // 处理请求错误
    console.error('Request error:', error)
    return Promise.reject(error)
  }
)

// 响应拦截器
service.interceptors.response.use(
  response => {
    const { data } = response
    
    // 根据后端返回的code进行处理
    if (data.code === 200) {
      return data
    } else if (data.code === 401) {
      // 未授权，跳转到登录页
      ElMessage.error(data.message || '未授权，请重新登录')
      router.push('/login')
      return Promise.reject(new Error(data.message || '未授权'))
    } else if (data.code === 403) {
      // 无权限
      ElMessage.error(data.message || '无权限访问')
      return Promise.reject(new Error(data.message || '无权限'))
    } else if (data.code === 404) {
      // 资源不存在
      ElMessage.error(data.message || '资源不存在')
      return Promise.reject(new Error(data.message || '资源不存在'))
    } else if (data.code === 422) {
      // 数据验证失败
      ElMessage.error(data.message || '数据验证失败')
      return Promise.reject(new Error(data.message || '数据验证失败'))
    } else {
      // 其他错误
      ElMessage.error(data.message || '请求失败')
      return Promise.reject(new Error(data.message || '请求失败'))
    }
  },
  error => {
    // 处理响应错误
    console.error('Response error:', error)
    
    let message = '网络错误'
    
    if (error.response) {
      // 请求已发出，但服务器响应的状态码不在 2xx 范围内
      const { status, data } = error.response
      
      switch (status) {
        case 400:
          message = data.message || '请求参数错误'
          break
        case 401:
          message = data.message || '未授权，请重新登录'
          router.push('/login')
          break
        case 403:
          message = data.message || '无权限访问'
          break
        case 404:
          message = data.message || '请求的资源不存在'
          break
        case 500:
          message = data.message || '服务器内部错误'
          break
        case 502:
          message = '网关错误'
          break
        case 503:
          message = '服务不可用'
          break
        case 504:
          message = '网关超时'
          break
        default:
          message = data.message || `未知错误 (${status})`
      }
    } else if (error.request) {
      // 请求已经发出，但没有收到响应
      message = '网络连接失败，请检查网络连接'
    } else {
      // 发送请求时出了点问题
      message = error.message || '请求配置错误'
    }
    
    ElMessage.error(message)
    return Promise.reject(error)
  }
)

export default service