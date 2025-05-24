import { getApiBaseUrl } from './getApiBaseUrl';

/**
 * Returns the profile photo path for a user based on their role and custom photo path.
 * If a custom photo path is provided, it will be returned. Otherwise, returns the default
 * avatar based on the user's role.
 * 
 * @param role - The role of the user ('admin' or 'user')
 * @param photoPath - Optional custom photo path for the user
 * @returns The path to either the custom photo or the default role-based avatar
 */
export const getDefaultProfilePhoto = (role?: string | null, photoPath?: string | null) => {
  if (photoPath) {
    const fullPath = `${getApiBaseUrl()}/${photoPath}`;
    console.log('Photo Path Details:', {
      apiBase: getApiBaseUrl(),
      relativePath: photoPath,
      fullPath: fullPath
    });
    return fullPath;
  }

  // Return default avatar based on role
  if (role === 'admin') {
    return '/avatars/admin.png';
  }
  
  return '/avatars/user.png';
};