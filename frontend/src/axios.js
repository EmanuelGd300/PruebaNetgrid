import axios from 'axios'

// Debug: mostrar qué URL se está usando
const apiURL = import.meta.env.VITE_API_URL || 'https://pruebanetgrid-production.up.railway.app/api'
console.log('API URL:', apiURL)
console.log('Environment:', import.meta.env.MODE)
console.log('VITE_API_URL:', import.meta.env.VITE_API_URL)

const instance = axios.create({
  baseURL: apiURL
})

instance.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  error => {
    return Promise.reject(error)
  }
)

export default instance
