import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom'
import { AuthProvider } from './auth/AuthContext'
import ProtectedRoute from './auth/ProtectedRoute'
import Login from './pages/Login'
import Placeholder from './pages/Placeholder'

export default function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <Routes>
          <Route path="/login" element={<Login />} />

          <Route element={<ProtectedRoute roles={['super_admin', 'property_manager', 'landlord']} />}>
            <Route path="/dashboard" element={<Placeholder title="Dashboard" />} />
          </Route>

          <Route element={<ProtectedRoute roles={['tenant']} />}>
            <Route path="/portal" element={<Placeholder title="Tenant Portal" />} />
          </Route>

          <Route element={<ProtectedRoute roles={['maintenance_staff']} />}>
            <Route path="/maintenance" element={<Placeholder title="Maintenance Jobs" />} />
          </Route>

          <Route path="*" element={<Navigate to="/dashboard" replace />} />
        </Routes>
      </AuthProvider>
    </BrowserRouter>
  )
}