import { Navigate, Outlet, useLocation } from 'react-router-dom'
import { Loader2 } from 'lucide-react'
import { useAuth } from './AuthContext'
import { homePathFor } from './paths'

export default function ProtectedRoute({ roles }) {
  const { user, loading, hasRole } = useAuth()
  const location = useLocation()

  if (loading) {
    return (
      <div className="min-h-screen grid place-items-center">
        <Loader2 className="animate-spin text-slate-400" />
      </div>
    )
  }

  if (!user) return <Navigate to="/login" replace state={{ from: location }} />

  if (roles && !hasRole(...roles)) {
    return <Navigate to={homePathFor(user)} replace />
  }

  return <Outlet />
}