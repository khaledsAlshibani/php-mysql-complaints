import { getApiBaseUrl } from '@/utils/getApiBaseUrl';
import axios, { AxiosError, isAxiosError } from 'axios';
import { authService } from '@/services/authService';
import { useAuthStore } from '@/store/useAuthStore';

let isRefreshing = false;
let failedQueue: Array<{
  resolve: (value?: unknown) => void;
  reject: (reason?: any) => void;
}> = [];

const processFailedQueue = (error: any = null) => {
  failedQueue.forEach(promise => {
    if (error) {
      promise.reject(error);
    } else {
      promise.resolve();
    }
  });
  failedQueue = [];
};

const axiosInstance = axios.create({
  baseURL: getApiBaseUrl(),
  headers: { 'Content-Type': 'application/json' },
  withCredentials: true,
});

axiosInstance.interceptors.request.use(
  (config) => {
    console.log(`🌐 Request: ${config.method?.toUpperCase()} ${config.url}`, {
      headers: config.headers,
      data: config.data,
      params: config.params
    });
    return config;
  },
  (error) => {
    console.error('❌ Request Error:', error);
    return Promise.reject(error);
  }
);

axiosInstance.interceptors.response.use(
  (response) => {
    console.log(`✅ Response: ${response.config.method?.toUpperCase()} ${response.config.url}`, {
      status: response.status,
      data: response.data
    });
    return response;
  },
  async (error: AxiosError) => {
    const originalRequest = error.config as any;

    console.error(`❌ Response Error: ${originalRequest?.method?.toUpperCase()} ${originalRequest?.url}`, {
      status: error.response?.status,
      data: error.response?.data,
      error: error.message
    });

    if (
      error.response?.status === 401 &&
      !originalRequest._retry &&
      !originalRequest.url?.includes('/auth/') &&
      useAuthStore.getState().isAuthenticated
    ) {
      if (isRefreshing) {
        try {
          return new Promise((resolve, reject) => {
            failedQueue.push({ resolve, reject });
          }).then(() => {
            return axiosInstance(originalRequest);
          }).catch(err => {
            return Promise.reject(err);
          });
        } catch (err) {
          return Promise.reject(err);
        }
      }

      originalRequest._retry = true;
      isRefreshing = true;

      try {
        console.log('🔄 Token expired, attempting refresh...');
        const response = await authService.refresh();

        if (response.status === 'success' && response.data) {
          console.log('🔑 Token refresh successful');
          useAuthStore.getState().setUser(response.data);
          processFailedQueue();
          return axiosInstance(originalRequest);
        } else {
          processFailedQueue(error);
          useAuthStore.getState().logout();
          window.location.href = '/login';
          return Promise.reject(error);
        }
      } catch (refreshError) {
        console.error('🚫 Token refresh failed:', refreshError);
        processFailedQueue(refreshError);
        useAuthStore.getState().logout();
        window.location.href = '/login';
        return Promise.reject(refreshError);
      } finally {
        isRefreshing = false;
      }
    }

    return Promise.reject(error);
  }
);

export default axiosInstance;
