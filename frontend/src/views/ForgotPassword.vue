<template>
  <div class="auth-container">
    <div class="auth-card">
      <h2>Recuperar Contraseña</h2>
      <form @submit.prevent="sendResetLink" v-if="!tokenSent">
        <div class="form-group">
          <input
            v-model="email"
            type="email"
            placeholder="Correo electrónico"
            required
            class="form-input"
          >
        </div>
        <button type="submit" :disabled="loading" class="auth-btn">
          {{ loading ? 'Enviando...' : 'Enviar enlace' }}
        </button>
      </form>
      
      <form @submit.prevent="resetPassword" v-else>
        <div class="form-group">
          <input
            v-model="token"
            type="text"
            placeholder="Token de recuperación"
            required
            class="form-input"
          >
        </div>
        <div class="form-group">
          <input
            v-model="password"
            type="password"
            placeholder="Nueva contraseña"
            required
            minlength="8"
            class="form-input"
          >
        </div>
        <div class="form-group">
          <input
            v-model="password_confirmation"
            type="password"
            placeholder="Confirmar contraseña"
            required
            class="form-input"
          >
        </div>
        <button type="submit" :disabled="loading" class="auth-btn">
          {{ loading ? 'Cambiando...' : 'Cambiar contraseña' }}
        </button>
      </form>
      
      <p class="auth-link">
        <router-link to="/login">Volver al login</router-link>
      </p>
      
      <div v-if="message" class="success-message">{{ message }}</div>
      <div v-if="error" class="error-message">{{ error }}</div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ForgotPassword',
  data() {
    return {
      email: '',
      token: '',
      password: '',
      password_confirmation: '',
      loading: false,
      error: '',
      message: '',
      tokenSent: false
    }
  },
  methods: {
    async sendResetLink() {
      this.loading = true
      this.error = ''
      
      try {
        const response = await axios.post('http://localhost:8000/api/password/email', {
          email: this.email
        })
        
        this.message = response.data.message
        this.tokenSent = true
        // Para pruebas, mostrar el token
        if (response.data.token) {
          this.token = response.data.token
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Error al enviar el enlace'
      } finally {
        this.loading = false
      }
    },
    async resetPassword() {
      this.loading = true
      this.error = ''
      
      if (this.password !== this.password_confirmation) {
        this.error = 'Las contraseñas no coinciden'
        this.loading = false
        return
      }
      
      try {
        const response = await axios.post('http://localhost:8000/api/password/reset', {
          email: this.email,
          token: this.token,
          password: this.password,
          password_confirmation: this.password_confirmation
        })
        
        this.message = response.data.message
        setTimeout(() => {
          this.$router.push('/login')
        }, 2000)
      } catch (error) {
        this.error = error.response?.data?.message || 'Error al cambiar la contraseña'
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

.success-message {
  background: #51cf66;
  color: white;
  padding: 0.75rem;
  border-radius: 8px;
  margin-top: 1rem;
  text-align: center;
}
</style>