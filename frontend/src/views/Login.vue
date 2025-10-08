<template>
  <div class="auth-container">
    <div class="auth-card">
      <h2>Iniciar Sesión</h2>
      <form @submit.prevent="login">
        <div class="form-group">
          <input
            v-model="form.email"
            type="email"
            placeholder="Correo electrónico"
            required
            class="form-input"
          >
        </div>
        <div class="form-group">
          <input
            v-model="form.password"
            type="password"
            placeholder="Contraseña"
            required
            class="form-input"
          >
        </div>
        <div class="recaptcha-container">
          <div ref="recaptcha" class="g-recaptcha"></div>
        </div>
        <button type="submit" :disabled="loading" class="auth-btn">
          {{ loading ? 'Iniciando...' : 'Iniciar Sesión' }}
        </button>
      </form>
      <p class="auth-link">
        ¿No tienes cuenta? <router-link to="/register">Regístrate</router-link><br>
        <router-link to="/forgot-password">¿Olvidaste tu contraseña?</router-link>
      </p>
      <div v-if="error" class="error-message">{{ error }}</div>
    </div>
  </div>
</template>

<script>
import axios from '../axios'

export default {
  name: 'Login',
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      loading: false,
      error: '',
      recaptchaSiteKey: ''
    }
  },
  async mounted() {
    await this.loadRecaptcha()
  },
  methods: {
    async loadRecaptcha() {
      try {
        this.recaptchaSiteKey = import.meta.env.VITE_RECAPTCHA_SITE_KEY
        
        if (this.recaptchaSiteKey) {
          const script = document.createElement('script')
          script.src = 'https://www.google.com/recaptcha/api.js'
          script.async = true
          script.defer = true
          document.head.appendChild(script)
          
          script.onload = () => {
            window.grecaptcha.render(this.$refs.recaptcha, {
              sitekey: this.recaptchaSiteKey
            })
          }
        }
      } catch (error) {
        console.error('Error loading reCAPTCHA:', error)
      }
    },
    async login() {
      this.loading = true
      this.error = ''
      
      try {
        const recaptchaToken = window.grecaptcha ? window.grecaptcha.getResponse() : ''

        const response = await axios.post('/login', {
          ...this.form,
          recaptcha_token: recaptchaToken
        })

        localStorage.setItem('token', response.data.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.data.user))
        
        window.location.href = '/'
      } catch (error) {
        const errors = error.response?.data?.errors
        if (errors?.email) {
          this.error = errors.email[0]
        } else if (errors?.password) {
          this.error = errors.password[0]
        } else {
          this.error = error.response?.data?.message || 'Error al iniciar sesión'
        }
        if (window.grecaptcha) {
          window.grecaptcha.reset()
        }
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 80px);
  padding: 2rem;
}

.auth-card {
  background: white;
  padding: 2rem;
  border-radius: 25px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  border: 4px solid #3B4CCA;
  width: 100%;
  max-width: 400px;
}

.auth-card h2 {
  text-align: center;
  margin-bottom: 2rem;
  color: #DC0A2D;
  font-weight: bold;
  font-size: 2rem;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}

.form-group {
  margin-bottom: 1rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s;
  font-weight: 500;
}

.form-input:focus {
  outline: none;
  border-color: #3B4CCA;
  box-shadow: 0 0 8px rgba(59, 76, 202, 0.3);
}

.recaptcha-container {
  margin: 1rem 0;
  display: flex;
  justify-content: center;
}

.auth-btn {
  width: 100%;
  padding: 0.75rem;
  background: #FFCB05;
  color: #333;
  border: none;
  border-radius: 25px;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.auth-btn:hover:not(:disabled) {
  background: #FFD700;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.auth-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.auth-link {
  text-align: center;
  margin-top: 1rem;
  color: #666;
}

.auth-link a {
  color: #DC0A2D;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s;
}

.auth-link a:hover {
  color: #B00020;
  text-decoration: underline;
}

.error-message {
  background: #ffebee;
  color: #DC0A2D;
  padding: 0.75rem;
  border-radius: 10px;
  border: 2px solid #DC0A2D;
  margin-top: 1rem;
  text-align: center;
  font-weight: 600;
}
</style>