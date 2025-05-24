import axios, { AxiosError } from 'axios';
import { API_URL } from '@/constants/API_URL';
import { useAuthStore } from '@/store/useAuthStore';

// Create axios instance
const axiosInstance = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
  withCredentials: true,
});

let isRefreshing = false;
let failedQueue: Array<{
  resolve: (token: string) => void;
  reject: (error: any) => void;
}> = [];

const processQueue = (error: any = null) => {
  failedQueue.forEach((prom) => {
    if (error) {
      prom.reject(error);
    } else {
      prom.resolve('');
    }
  });

  failedQueue = [];
};

// Only set loading for non-refresh requests
const shouldSetLoading = (url: string) => !url.includes('/auth/refresh');

axiosInstance.interceptors.request.use(
  (config) => {
    if (shouldSetLoading(config.url || '')) {
      useAuthStore.getState().setLoading(true);
    }
    
    // Log request
    console.info('🌐 Request:', {
      method: config.method?.toUpperCase(),
      url: config.url,
      headers: config.headers,
      data: config.data,
      params: config.params
    });

    return config;
  },
  (error) => {
    if (shouldSetLoading(error.config?.url || '')) {
      useAuthStore.getState().setLoading(false);
    }
    
    // Log request error
    console.error('❌ Request Error:', {
      message: error.message,
      error
    });

    return Promise.reject(error);
  }
);

axiosInstance.interceptors.response.use(
  (response) => {
    if (shouldSetLoading(response.config.url || '')) {
      useAuthStore.getState().setLoading(false);
    }
    
    // Log successful response
    console.info('✅ Response:', {
      status: response.status,
      method: response.config.method?.toUpperCase(),
      url: response.config.url,
      data: response.data
    });

    return response;
  },
  async (error: AxiosError) => {
    const originalRequest = error.config as any;

    // Log response error
    console.error('❌ Response Error:', {
      status: error.response?.status,
      method: originalRequest?.method?.toUpperCase(),
      url: originalRequest?.url,
      data: error.response?.data,
      message: error.message
    });

    if (error.response?.status !== 401 || originalRequest._retry) {
      if (shouldSetLoading(originalRequest?.url || '')) {
        useAuthStore.getState().setLoading(false);
      }
      return Promise.reject(error);
    }

    if (isRefreshing) {
      return new Promise((resolve, reject) => {
        failedQueue.push({ resolve, reject });
      })
        .then(() => {
          return axiosInstance(originalRequest);
        })
        .catch((err) => {
          return Promise.reject(err);
        });
    }

    originalRequest._retry = true;
    isRefreshing = true;

    try {
      console.info('🔄 Token refresh attempt');
      const response = await axiosInstance.post('/auth/refresh');
      if (response.data.status === 'success' && response.data.data) {
        useAuthStore.getState().setUser(response.data.data);
        console.info('🔑 Token refresh successful');
      }
      
      processQueue();
      return axiosInstance(originalRequest);
    } catch (refreshError) {
      console.error('🚫 Token refresh failed:', refreshError);
      processQueue(refreshError);
      useAuthStore.getState().logout();
      window.location.href = '/login';
      return Promise.reject(refreshError);
    } finally {
      isRefreshing = false;
      if (shouldSetLoading(originalRequest?.url || '')) {
        useAuthStore.getState().setLoading(false);
      }
    }
  }
);

export default axiosInstance;
