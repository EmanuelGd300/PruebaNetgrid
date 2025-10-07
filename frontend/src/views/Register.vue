<template>
  <div class="auth-container">
    <div class="auth-card">
      <h2>Registro</h2>
      <form @submit.prevent="register">
        <div class="form-group">
          <input
            v-model="form.name"
            type="text"
            placeholder="Nombre completo"
            required
            class="form-input"
          >
        </div>
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
            placeholder="Contraseña (mín. 8 caracteres)"
            required
            minlength="8"
            class="form-input"
          >
        </div>
        <div class="form-group">
          <input
            v-model="form.password_confirmation"
            type="password"
            placeholder="Confirmar contraseña"
            required
            class="form-input"
          >
        </div>
        <div class="recaptcha-container">
          <div ref="recaptcha" class="g-recaptcha"></div>
        </div>
        <button type="submit" :disabled="loading" class="auth-btn">
          {{ loading ? 'Registrando...' : 'Registrarse' }}
        </button>
      </form>
      <p class="auth-link">
        ¿Ya tienes cuenta? <router-link to="/login">Inicia sesión</router-link>
      </p>
      <div v-if="error" class="error-message">{{ error }}</div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'Register',
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
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
        const response = await axios.get('http://localhost:8000/api/recaptcha-site-key')
        this.recaptchaSiteKey = response.data.site_key
        
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
    async register() {
      this.loading = true
      this.error = ''
      
      if (this.form.password !== this.form.password_confirmation) {
        this.error = 'Las contraseñas no coinciden'
        this.loading = false
        return
      }
      
      try {
        const recaptchaToken = window.grecaptcha ? window.grecaptcha.getResponse() : ''
        
        if (!recaptchaToken) {
          this.error = 'Por favor completa la verificación reCAPTCHA'
          this.loading = false
          return
        }

        const response = await axios.post('http://localhost:8000/api/register', {
          ...this.form,
          recaptcha_token: recaptchaToken
        })

        localStorage.setItem('token', response.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.user))
        
        this.$router.push('/')
      } catch (error) {
        this.error = error.response?.data?.message || 'Error al registrarse'
        if (window.grecaptcha) {
          window.grecaptcha.reset()
        }
      } finally {
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
  background: rgba(255, 255, 255, 0.95);
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  width: 100%;
  max-width: 400px;
}

.auth-card h2 {
  text-align: center;
  margin-bottom: 2rem;
  color: #333;
}

.form-group {
  margin-bottom: 1rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-input:focus {
  outline: none;
  border-color: #667eea;
}

.recaptcha-container {
  margin: 1rem 0;
  display: flex;
  justify-content: center;
}

.auth-btn {
  width: 100%;
  padding: 0.75rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  transition: opacity 0.3s;
}

.auth-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.auth-link {
  text-align: center;
  margin-top: 1rem;
  color: #666;
}

.auth-link a {
  color: #667eea;
  text-decoration: none;
}

.error-message {
  background: #ff6b6b;
  color: white;
  padding: 0.75rem;
  border-radius: 8px;
  margin-top: 1rem;
  text-align: center;
}
</style>