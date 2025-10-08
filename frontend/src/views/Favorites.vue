<template>
  <div class="favorites">
    <div class="container">
      <h2 class="page-title">Mis Pokémon Favoritos</h2>
      
      <div v-if="!isAuthenticated" class="auth-required">
        <p>Debes iniciar sesión para ver tus favoritos</p>
        <router-link to="/login" class="auth-btn">Iniciar Sesión</router-link>
      </div>
      
      <div v-else-if="loading" class="loading">
        <div class="pokeball-loader"></div>
        <p>Cargando favoritos...</p>
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
import axios from '../axios'

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
        const response = await axios.get('/favorites')
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
        await axios.delete(`/favorites/${pokemonId}`)
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
  color: #DC0A2D;
  text-align: center;
  margin-bottom: 2rem;
  font-size: 2.5rem;
  font-weight: 800;
  letter-spacing: 1px;
}

.auth-required, .no-favorites {
  text-align: center;
  color: #333;
  margin: 3rem 0;
}

.auth-required p, .no-favorites p {
  font-size: 1.3rem;
  margin-bottom: 1.5rem;
  font-weight: 600;
}

.auth-btn, .browse-btn {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  background: #3B4CCA;
  color: white;
  border: none;
  text-decoration: none;
  border-radius: 25px;
  font-weight: bold;
  transition: all 0.3s;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.auth-btn:hover, .browse-btn:hover {
  transform: translateY(-2px);
  background: #2A3B9F;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 50vh;
  gap: 1rem;
}

.loading p {
  color: #DC0A2D;
  font-size: 1.5rem;
  font-weight: bold;
}

.pokeball-loader {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(180deg, #DC0A2D 50%, white 50%);
  border: 4px solid #333;
  position: relative;
  animation: spin 1s linear infinite;
}

.pokeball-loader::before {
  content: '';
  position: absolute;
  width: 20px;
  height: 20px;
  background: white;
  border: 4px solid #333;
  border-radius: 50%;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.pokemon-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.pokemon-card {
  background: white;
  border-radius: 20px;
  padding: 1.5rem;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border: 3px solid #FFCB05;
  transition: all 0.3s;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.pokemon-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  border-color: #DC0A2D;
}

.pokemon-image {
  width: 150px;
  height: 150px;
  object-fit: contain;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
  margin: 0 auto;
}

.pokemon-name {
  margin: 1rem 0;
  color: #DC0A2D;
  text-transform: capitalize;
  font-size: 1.3rem;
  font-weight: bold;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  margin-top: 1rem;
}

.view-btn {
  padding: 0.5rem 1rem;
  background: #3B4CCA;
  color: white;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.view-btn:hover {
  background: #2A3B9F;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.remove-btn {
  padding: 0.5rem 1rem;
  background: white;
  color: #DC0A2D;
  border: 2px solid #DC0A2D;
  border-radius: 20px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.remove-btn:hover:not(:disabled) {
  background: #DC0A2D;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.remove-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}
</style>