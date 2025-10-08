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
  min-height: 60vh;
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
  font-family: 'Poppins', sans-serif;
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
  font-family: 'Poppins', sans-serif;
  transition: all 0.3s;
  font-weight: 500;
}

.form-input:focus {
  outline: none;
  border-color: #3B4CCA;
  box-shadow: 0 0 8px rgba(59, 76, 202, 0.3);
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
  font-family: 'Poppins', sans-serif;
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
  font-family: 'Poppins', sans-serif;
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
  font-family: 'Poppins', sans-serif;
}

.success-message {
  background: #e8f5e9;
  color: #2e7d32;
  padding: 0.75rem;
  border-radius: 10px;
  border: 2px solid #4caf50;
  margin-top: 1rem;
  text-align: center;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
}
</style>