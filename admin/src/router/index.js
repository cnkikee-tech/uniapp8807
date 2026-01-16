import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { ElMessage } from 'element-plus'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'

// 配置NProgress
NProgress.configure({ showSpinner: false })

// 路由配置
const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/Login.vue'),
    meta: { title: '登录', requiresAuth: false }
  },
  {
    path: '/',
    name: 'Layout',
    component: () => import('@/components/Layout.vue'),
    redirect: '/dashboard',
    meta: { title: '首页', requiresAuth: true },
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import('@/views/Dashboard.vue'),
        meta: { title: '数据统计', icon: 'DataAnalysis' }
      },
      {
        path: 'cards',
        name: 'Cards',
        component: () => import('@/views/Cards.vue'),
        meta: { title: '名片管理', icon: 'CreditCard' }
      },
      {
        path: 'users',
        name: 'Users',
        component: () => import('@/views/Users.vue'),
        meta: { title: '用户管理', icon: 'User' }
      },
      {
        path: 'settings',
        name: 'Settings',
        component: () => import('@/views/Settings.vue'),
        meta: { title: '系统设置', icon: 'Setting' }
      }
    ]
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/404.vue'),
    meta: { title: '404', requiresAuth: false }
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// 路由守卫
router.beforeEach(async (to, from, next) => {
  // 开始进度条
  NProgress.start()
  
  // 设置页面标题
  document.title = to.meta.title ? `${to.meta.title} - 智能名片系统` : '智能名片系统'
  
  const userStore = useUserStore()
  
  // 检查是否需要认证
  if (to.meta.requiresAuth !== false) {
    if (!userStore.isLoggedIn) {
      // 如果没有登录，跳转到登录页
      next('/login')
      ElMessage.warning('请先登录')
      NProgress.done()
      return
    }
    
    // 如果已登录但用户信息为空，获取用户信息
    if (Object.keys(userStore.userInfo).length === 0) {
      try {
        await userStore.getUserInfoAction()
      } catch (error) {
        // 获取用户信息失败，清除token并跳转到登录页
        userStore.resetState()
        next('/login')
        ElMessage.error('获取用户信息失败，请重新登录')
        NProgress.done()
        return
      }
    }
  }
  
  // 如果已登录，不允许访问登录页
  if (to.path === '/login' && userStore.isLoggedIn) {
    next('/')
    NProgress.done()
    return
  }
  
  next()
})

// 路由后置守卫
router.afterEach(() => {
  // 结束进度条
  NProgress.done()
})

export default router