import Cookies from 'js-cookie'

const TOKEN_KEY = 'business-card-admin-token'
const EXPIRES_IN = 7 // 7天

export function getToken() {
  return Cookies.get(TOKEN_KEY)
}

export function setToken(token) {
  return Cookies.set(TOKEN_KEY, token, { expires: EXPIRES_IN })
}

export function removeToken() {
  return Cookies.remove(TOKEN_KEY)
}

export function getUsername() {
  return Cookies.get('username')
}

export function setUsername(username) {
  return Cookies.set('username', username, { expires: EXPIRES_IN })
}

export function removeUsername() {
  return Cookies.remove('username')
}