<template>
  <div id="app">
    <nav class="navbar">
      <div class="nav-container">
        <div class="nav-left">
          <router-link to="/" class="nav-title">Pokedex</router-link>
        </div>
        <div class="search-container">
          <input 
            v-model="searchQuery" 
            @keyup.enter="searchPokemon"
            placeholder="Buscar Pokémon..."
            class="search-input"
          >
          <button @click="searchPokemon" class="search-btn">Buscar</button>
        </div>
        <div class="nav-right">
          <template v-if="isAuthenticated">
            <router-link to="/favorites" class="nav-link">Favoritos</router-link>
            <span class="user-name">{{ userName }}</span>
            <button @click="logout" class="logout-btn">Cerrar Sesión</button>
          </template>
          <template v-else>
            <router-link to="/login" class="nav-link">Iniciar Sesión</router-link>
            <router-link to="/register" class="nav-link">Registrarse</router-link>
          </template>
        </div>
      </div>
    </nav>
    <router-view />
  </div>
</template>

<script>
export default {
  name: 'App',
  data() {
    return {
      searchQuery: ''
    }
  },
  computed: {
    isAuthenticated() {
      return !!localStorage.getItem('token')
    },
    userName() {
      const user = localStorage.getItem('user')
      return user ? JSON.parse(user).name : ''
    }
  },
  methods: {
    searchPokemon() {
      if (this.searchQuery.trim()) {
        this.$router.push(`/search?q=${this.searchQuery.trim()}`)
      }
    },
    logout() {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      this.$router.push('/')
      this.$forceUpdate()
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
  font-family: 'Arial', sans-serif;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
}

.navbar {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  padding: 1rem 0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 2rem;
  gap: 2rem;
}

.nav-left, .nav-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.nav-title {
  color: white;
  font-size: 2rem;
  font-weight: bold;
  text-decoration: none;
  transition: opacity 0.3s;
}

.nav-title:hover {
  opacity: 0.8;
}

.nav-link {
  color: white;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  transition: background 0.3s;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.1);
}

.user-name {
  color: white;
  font-weight: bold;
}

.logout-btn {
  padding: 0.5rem 1rem;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  transition: background 0.3s;
}

.logout-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.search-container {
  display: flex;
  gap: 0.5rem;
}

.search-input {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 25px;
  outline: none;
  width: 250px;
}

.search-btn {
  padding: 0.5rem 1rem;
  background: #ff6b6b;
  color: white;
  border: none;
  border-radius: 25px;
  cursor: pointer;
  transition: background 0.3s;
}

.search-btn:hover {
  background: #ff5252;
}
</style>