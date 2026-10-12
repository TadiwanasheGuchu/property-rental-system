import { createContext, useCallback, useContext, useEffect, useState } from 'react'
import { api, tokenStore } from '../lib/api'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null)
  // If a token exists we must verify it before deciding anything.
  const [loading, setLoading] = useState(() => !!tokenStore.get())

  useEffect(() => {
    if (!tokenStore.get()) return
    api('/me')
      .then((res) => setUser(res.data))
      .catch(() => tokenStore.clear()) // expired or revoked token
      .finally(() => setLoading(false))
  }, [])

  const login = useCallback(async (email, password) => {
    const res = await api('/login', { method: 'POST', body: { email, password } })
    tokenStore.set(res.data.token)
    setUser(res.data.user)
    return res.data.user
  }, [])

  const logout = useCallback(async () => {
    try {
      await api('/logout', { method: 'POST' })
    } catch {
      // token may already be invalid; clear locally regardless
    }
    tokenStore.clear()
    setUser(null)
  }, [])

  const hasRole = useCallback(
    (...roles) => !!user && user.roles.some((r) => roles.includes(r)),
    [user]
  )

  return (
    <AuthContext.Provider value={{ user, loading, login, logout, hasRole }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  return useContext(AuthContext)
}