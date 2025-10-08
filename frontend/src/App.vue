<template>
  <div id="app" class="app-wrapper">
    <nav class="navbar">
      <div class="nav-container">
        <router-link to="/" class="nav-title">Pokédex</router-link>
        
        <div class="search-container">
          <input 
            v-model="searchQuery" 
            @keyup.enter="searchPokemon"
            placeholder="Busca por nombre o número en la Pokédex"
            class="search-input"
          >
          <button @click="searchPokemon" class="search-btn">Buscar</button>
          <button @click="searchRandom" class="random-btn">Aleatorio</button>
        </div>
        
        <div class="nav-right">
          <template v-if="isAuthenticated">
            <span class="trainer-label">Entrenador: <span class="user-name">{{ userName }}</span></span>
            <router-link to="/favorites" class="nav-link">Favoritos</router-link>
            <button @click="logout" class="logout-btn">Cerrar Sesión</button>
          </template>
          <template v-else>
            <router-link to="/login" class="nav-link">Iniciar Sesión</router-link>
            <router-link to="/register" class="nav-link">Registrarse</router-link>
          </template>
        </div>
      </div>
    </nav>
    <router-view :key="$route.fullPath" />
    <footer class="footer">
      <div class="footer-container">
        <p class="footer-text">
          Desarrollado por <strong><a href="https://689f8d330b54b40008d1d849--emanuel-gomez-diaz.netlify.app/" target="_blank" rel="noopener" class="dev-link">Emanuel Gómez Díaz</a></strong> | Prueba Técnica para <strong>NetGrid</strong>
        </p>
        <p class="footer-subtext">
          Datos obtenidos de <a href="https://pokeapi.co/" target="_blank" rel="noopener">PokéAPI</a>
        </p>
      </div>
    </footer>
  </div>
</template>

<script>
import axios from './axios'

export default {
  name: 'App',
  data() {
    return {
      searchQuery: '',
      isAuthenticated: false,
      userName: ''
    }
  },
  mounted() {
    this.checkAuth()
  },
  watch: {
    '$route'() {
      this.checkAuth()
    }
  },
  methods: {
    checkAuth() {
      this.isAuthenticated = !!localStorage.getItem('token')
      const user = localStorage.getItem('user')
      this.userName = user ? JSON.parse(user).name : ''
    },
    searchPokemon() {
      if (this.searchQuery.trim()) {
        this.$router.push(`/search?q=${this.searchQuery.trim()}`)
      }
    },
    searchRandom() {
      const randomId = Math.floor(Math.random() * 898) + 1
      this.$router.push(`/pokemon/${randomId}`)
    },
    async logout() {
      try {
        await axios.post('/logout')
      } catch (error) {
        console.error('Error al cerrar sesión:', error)
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        this.isAuthenticated = false
        this.userName = ''
        this.$router.push('/login')
      }
    }
  }
}
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #FFF8F0;
  min-height: 100vh;
}

.app-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.app-wrapper > .navbar {
  flex-shrink: 0;
}

.app-wrapper > * {
  flex-shrink: 0;
}

.app-wrapper > .footer {
  margin-top: auto;
}

.navbar {
  background: #DC0A2D;
  padding: 1rem 0;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  border-bottom: 5px solid #FFCB05;
}

.nav-container {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 2rem;
  gap: 2rem;
}

.nav-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.nav-title {
  color: #FFCB05;
  font-size: 2.5rem;
  font-weight: 800;
  text-decoration: none;
  text-shadow: 3px 3px 0px #003A70, -1px -1px 0 #003A70, 1px -1px 0 #003A70, -1px 1px 0 #003A70, 1px 1px 0 #003A70, 2px 2px 0 #003A70;
  transition: transform 0.3s;
  letter-spacing: 2px;
  font-family: 'Poppins', sans-serif;
  white-space: nowrap;
}

.nav-title:hover {
  transform: scale(1.05);
}

.nav-link {
  color: #333;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  background: white;
  border: none;
  transition: all 0.3s;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
}

.nav-link:hover {
  background: #FFCB05;
  color: #333;
  transform: translateY(-2px);
}

.trainer-label {
  color: white;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  font-size: 0.9rem;
}

.user-name {
  color: #FFCB05;
  font-weight: 800;
}

.logout-btn {
  padding: 0.5rem 1rem;
  background: white;
  color: #333;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  transition: all 0.3s;
}

.logout-btn:hover {
  background: #FFCB05;
  color: #333;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.search-container {
  display: flex;
  gap: 0.5rem;
  flex: 1;
  justify-content: center;
}

.search-input {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 25px;
  outline: none;
  width: 450px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  background: white;
  color: #333;
}

.search-input:focus {
  box-shadow: 0 0 10px rgba(255, 203, 5, 0.5);
}

.search-input::placeholder {
  color: #999;
}

.search-btn {
  padding: 0.5rem 1rem;
  background: white;
  color: #333;
  border: none;
  border-radius: 25px;
  cursor: pointer;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  transition: all 0.3s;
}

.search-btn:hover {
  background: #FFCB05;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.random-btn {
  padding: 0.5rem 1rem;
  background: white;
  color: #333;
  border: none;
  border-radius: 25px;
  cursor: pointer;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  transition: all 0.3s;
  white-space: nowrap;
}

.random-btn:hover {
  background: #FFCB05;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.footer {
  background: #DC0A2D;
  padding: 1.5rem 0;
  border-top: 5px solid #FFCB05;
}

.footer-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
  text-align: center;
}

.footer-text {
  color: white;
  font-family: 'Poppins', sans-serif;
  font-size: 1rem;
  margin-bottom: 0.5rem;
}

.footer-text strong {
  color: #FFCB05;
}

.footer-subtext {
  color: white;
  font-family: 'Poppins', sans-serif;
  font-size: 0.9rem;
  opacity: 0.9;
}

.footer-subtext a {
  color: #FFCB05;
  text-decoration: none;
  font-weight: 600;
  transition: opacity 0.3s;
}

.footer-subtext a:hover {
  opacity: 0.8;
  text-decoration: underline;
}

.dev-link {
  color: #FFCB05;
  text-decoration: none;
  transition: opacity 0.3s;
}

.dev-link:hover {
  opacity: 0.8;
  text-decoration: underline;
}
</style>