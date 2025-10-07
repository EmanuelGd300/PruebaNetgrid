<template>
  <div class="favorites">
    <div class="container">
      <h2 class="page-title">Mis Pokémon Favoritos</h2>
      
      <div v-if="!isAuthenticated" class="auth-required">
        <p>Debes iniciar sesión para ver tus favoritos</p>
        <router-link to="/login" class="auth-btn">Iniciar Sesión</router-link>
      </div>
      
      <div v-else-if="loading" class="loading">
        Cargando favoritos...
      </div>
      
      <div v-else-if="favorites.length === 0" class="no-favorites">
        <p>No tienes Pokémon favoritos aún</p>
        <router-link to="/" class="browse-btn">Explorar Pokémon</router-link>
      </div>
      
      <div v-else class="pokemon-grid">
        <div 
          v-for="pokemon in favorites" 
          :key="pokemon.pokemon_id"
          class="pokemon-card"
        >
          <img :src="pokemon.image" :alt="pokemon.name" class="pokemon-image">
          <h3 class="pokemon-name">{{ pokemon.name }}</h3>
          <div class="card-actions">
            <button 
              @click="goToPokemon(pokemon.pokemon_id)" 
              class="view-btn"
            >
              Ver Detalles
            </button>
            <button 
              @click="removeFavorite(pokemon.pokemon_id)" 
              class="remove-btn"
              :disabled="removing === pokemon.pokemon_id"
            >
              {{ removing === pokemon.pokemon_id ? 'Eliminando...' : 'Quitar' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'Favorites',
  data() {
    return {
      favorites: [],
      loading: false,
      removing: null
    }
  },
  computed: {
    isAuthenticated() {
      return !!localStorage.getItem('token')
    }
  },
  mounted() {
    if (this.isAuthenticated) {
      this.fetchFavorites()
    }
  },
  methods: {
    async fetchFavorites() {
      this.loading = true
      try {
        const token = localStorage.getItem('token')
        const response = await axios.get('http://localhost:8000/api/favorites', {
          headers: { Authorization: `Bearer ${token}` }
        })
        this.favorites = response.data
      } catch (error) {
        console.error('Error fetching favorites:', error)
        if (error.response?.status === 401) {
          localStorage.removeItem('token')
          localStorage.removeItem('user')
          this.$router.push('/login')
        }
      } finally {
        this.loading = false
      }
    },
    async removeFavorite(pokemonId) {
      this.removing = pokemonId
      try {
        const token = localStorage.getItem('token')
        await axios.delete(`http://localhost:8000/api/favorites/${pokemonId}`, {
          headers: { Authorization: `Bearer ${token}` }
        })
        this.favorites = this.favorites.filter(fav => fav.pokemon_id !== pokemonId)
      } catch (error) {
        console.error('Error removing favorite:', error)
      } finally {
        this.removing = null
      }
    },
    goToPokemon(id) {
      this.$router.push(`/pokemon/${id}`)
    }
  }
}
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.page-title {
  color: white;
  text-align: center;
  margin-bottom: 2rem;
  font-size: 2rem;
}

.auth-required, .no-favorites {
  text-align: center;
  color: white;
  margin: 3rem 0;
}

.auth-required p, .no-favorites p {
  font-size: 1.2rem;
  margin-bottom: 1rem;
}

.auth-btn, .browse-btn {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-decoration: none;
  border-radius: 25px;
  transition: transform 0.3s;
}

.auth-btn:hover, .browse-btn:hover {
  transform: translateY(-2px);
}

.loading {
  text-align: center;
  color: white;
  font-size: 1.2rem;
  margin: 3rem 0;
}

.pokemon-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

.pokemon-card {
  background: rgba(255, 255, 255, 0.9);
  border-radius: 15px;
  padding: 1.5rem;
  text-align: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s;
}

.pokemon-card:hover {
  transform: translateY(-5px);
}

.pokemon-image {
  width: 120px;
  height: 120px;
  object-fit: contain;
}

.pokemon-name {
  margin: 1rem 0;
  color: #333;
  text-transform: capitalize;
  font-size: 1.2rem;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  margin-top: 1rem;
}

.view-btn {
  padding: 0.5rem 1rem;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  transition: background 0.3s;
}

.view-btn:hover {
  background: #5a6fd8;
}

.remove-btn {
  padding: 0.5rem 1rem;
  background: #ff6b6b;
  color: white;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  transition: background 0.3s;
}

.remove-btn:hover:not(:disabled) {
  background: #ff5252;
}

.remove-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>