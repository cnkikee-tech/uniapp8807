import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { login, logout, getUserInfo } from '@/api/auth'
import { getToken, setToken, removeToken } from '@/utils/auth'

export const useUserStore = defineStore('user', () => {
  const token = ref(getToken())
  const userInfo = ref({})
  const roles = ref([])

  const isLoggedIn = computed(() => !!token.value)

  // 登录
  const loginAction = async (loginForm) => {
    try {
      const response = await login(loginForm)
      const { token: userToken, user } = response.data
      
      token.value = userToken
      userInfo.value = user
      setToken(userToken)
      
      return response
    } catch (error) {
      throw error
    }
  }

  // 登出
  const logoutAction = async () => {
    try {
      await logout()
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      token.value = ''
      userInfo.value = {}
      roles.value = []
      removeToken()
    }
  }

  // 获取用户信息
  const getUserInfoAction = async () => {
    try {
      const response = await getUserInfo()
      userInfo.value = response.data
      return response.data
    } catch (error) {
      throw error
    }
  }

  // 检查登录状态
  const checkLoginStatus = async () => {
    const currentToken = getToken()
    if (currentToken) {
      try {
        await getUserInfoAction()
      } catch (error) {
        // 如果获取用户信息失败，清除token
        removeToken()
        token.value = ''
        throw error
      }
    }
  }

  // 重置状态
  const resetState = () => {
    token.value = ''
    userInfo.value = {}
    roles.value = []
    removeToken()
  }

  return {
    token,
    userInfo,
    roles,
    isLoggedIn,
    loginAction,
    logoutAction,
    getUserInfoAction,
    checkLoginStatus,
    resetState
  }
})