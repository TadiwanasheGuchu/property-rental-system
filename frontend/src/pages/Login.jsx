import { useState } from 'react'
import { Navigate, useNavigate } from 'react-router-dom'
import { Building2, Loader2, AlertCircle } from 'lucide-react'
import { useAuth } from '../auth/AuthContext'
import { homePathFor } from '../auth/paths'

export default function Login() {
  const { user, login } = useAuth()
  const navigate = useNavigate()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')
  const [submitting, setSubmitting] = useState(false)

  if (user) return <Navigate to={homePathFor(user)} replace />

  async function handleSubmit(e) {
    e.preventDefault()
    setError('')
    setSubmitting(true)
    try {
      const loggedIn = await login(email, password)
      navigate(homePathFor(loggedIn), { replace: true })
    } catch (err) {
      setError(
        err.status === 429
          ? 'Too many attempts. Please wait a minute and try again.'
          : err.errors?.email?.[0] ?? err.message
      )
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <div className="min-h-screen grid place-items-center bg-slate-50 px-4">
      <div className="w-full max-w-sm">
        <div className="flex items-center gap-2 mb-6 text-slate-900">
          <Building2 className="size-6" />
          <span className="text-lg font-semibold">Property Manager</span>
        </div>

        <form onSubmit={handleSubmit} className="rounded-lg border border-slate-200 bg-white p-6 space-y-4">
          <div>
            <h1 className="text-xl font-semibold text-slate-900">Sign in</h1>
            <p className="text-sm text-slate-500">Use your account email and password.</p>
          </div>

          {error && (
            <div role="alert" className="flex items-start gap-2 rounded-md bg-red-50 p-3 text-sm text-red-700">
              <AlertCircle className="size-4 mt-0.5 shrink-0" />
              <span>{error}</span>
            </div>
          )}

          <label className="block text-sm">
            <span className="text-slate-700">Email</span>
            <input
              type="email"
              required
              autoComplete="username"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
            />
          </label>

          <label className="block text-sm">
            <span className="text-slate-700">Password</span>
            <input
              type="password"
              required
              autoComplete="current-password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
            />
          </label>

          <button
            type="submit"
            disabled={submitting}
            className="flex w-full items-center justify-center gap-2 rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 disabled:opacity-60"
          >
            {submitting && <Loader2 className="size-4 animate-spin" />}
            {submitting ? 'Signing in…' : 'Sign in'}
          </button>
        </form>
      </div>
    </div>
  )
}