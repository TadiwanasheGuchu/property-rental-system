export function homePathFor(user) {
  if (user.roles.includes('tenant')) return '/portal'
  if (user.roles.includes('maintenance_staff')) return '/maintenance'
  return '/dashboard' // super_admin, property_manager, landlord
}