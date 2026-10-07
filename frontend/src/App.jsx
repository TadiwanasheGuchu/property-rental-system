import { useEffect, useState } from 'react'
import { CheckCircle2, XCircle, Loader2 } from 'lucide-react'

export default function App() {
  const [state, setState] = useState({ status: 'loading' })

  useEffect(() => {
    fetch(`${import.meta.env.VITE_API_URL}/ping`, {
      headers: { Accept: 'application/json' },
    })
      .then((r) => r.json())
      .then((data) => setState({ status: 'ok', data }))
      .catch((err) => setState({ status: 'error', message: err.message }))
  }, [])

  return (
    <div className="min-h-screen grid place-items-center bg-slate-50">
      <div className="flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-5 py-4">
        {state.status === 'loading' && <Loader2 className="animate-spin text-slate-500" />}
        {state.status === 'ok' && <CheckCircle2 className="text-green-600" />}
        {state.status === 'error' && <XCircle className="text-red-600" />}
        <span className="text-slate-800">
          {state.status === 'loading' && 'Contacting API…'}
          {state.status === 'ok' && `API connected: ${state.data.app}`}
          {state.status === 'error' && `API unreachable: ${state.message}`}
        </span>
      </div>
    </div>
  )
}