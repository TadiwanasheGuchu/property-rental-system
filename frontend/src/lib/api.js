const API_URL = import.meta.env.VITE_API_URL
const TOKEN_KEY = 'auth_token'

export const tokenStore = {
  get: () => localStorage.getItem(TOKEN_KEY),
  set: (token) => localStorage.setItem(TOKEN_KEY, token),
  clear: () => localStorage.removeItem(TOKEN_KEY),
}

export class ApiError extends Error {
  constructor(message, status, errors = {}) {
    super(message)
    this.status = status
    this.errors = errors
  }
}

export async function api(path, { method = 'GET', body } = {}) {
  const token = tokenStore.get()

  const res = await fetch(`${API_URL}${path}`, {
    method,
    headers: {
      Accept: 'application/json',
      ...(body && { 'Content-Type': 'application/json' }),
      ...(token && { Authorization: `Bearer ${token}` }),
    },
    body: body ? JSON.stringify(body) : undefined,
  })

  const data = await res.json().catch(() => null)

  if (!res.ok) {
    throw new ApiError(data?.message ?? 'Request failed', res.status, data?.errors)
  }
  return data
}