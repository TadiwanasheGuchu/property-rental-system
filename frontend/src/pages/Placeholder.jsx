import { LogOut, Construction } from 'lucide-react'
import { useAuth } from '../auth/AuthContext'

export default function Placeholder({ title }) {
  const { user, logout } = useAuth()

  return (
    <div className="min-h-screen bg-slate-50">
      <header className="flex items-center justify-between border-b border-slate-200 bg-white px-6 py-3">
        <span className="font-semibold text-slate-900">Property Manager</span>
        <div className="flex items-center gap-4 text-sm text-slate-600">
          <span>
            {user.name} · {user.roles.join(', ')}
          </span>
          <button onClick={logout} className="flex items-center gap-1 hover:text-slate-900">
            <LogOut className="size-4" /> Sign out
          </button>
        </div>
      </header>

      <main className="p-6">
        <h1 className="text-2xl font-semibold text-slate-900">{title}</h1>
        <div className="mt-4 flex items-center gap-2 rounded-md border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
          <Construction className="size-4" />
          Pending: this module has not been built yet.
        </div>
      </main>
    </div>
  )
}