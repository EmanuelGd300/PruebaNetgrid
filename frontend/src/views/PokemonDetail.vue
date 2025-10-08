<template>
  <div class="pokemon-detail" v-if="pokemon">
    <div class="container">
      <div class="detail-card">
        <div class="pokemon-header">
          <img :src="pokemon.image" :alt="pokemon.name" class="pokemon-image">
          <div class="pokemon-info">
            <h1 class="pokemon-name">{{ pokemon.name }}</h1>
            <div class="pokemon-types">
              <span 
                v-for="type in pokemon.types" 
                :key="type"
                class="type-badge"
                :class="`type-${type}`"
              >
                {{ type }}
              </span>
            </div>
            <button 
              @click="toggleFavorite" 
              :class="['favorite-btn', { 'is-favorite': isFavorite }]"
              v-if="isAuthenticated"
            >
              {{ isFavorite ? '❤️ Quitar de favoritos' : '🤍 Agregar a favoritos' }}
            </button>
          </div>
        </div>
        
        <div class="pokemon-description" v-if="pokemon.description">
          <h3>Descripción</h3>
          <p>{{ pokemon.description }}</p>
        </div>
        
        <div class="pokemon-stats">
          <div class="stat-group">
            <h3>Información básica</h3>
            <div class="basic-stats">
              <div class="stat-item">
                <span class="stat-label">Altura:</span>
                <span class="stat-value">{{ pokemon.height / 10 }} m</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Peso:</span>
                <span class="stat-value">{{ pokemon.weight / 10 }} kg</span>
              </div>
            </div>
          </div>
          
          <div class="stat-group">
            <h3>Habilidades</h3>
            <div class="abilities">
              <span 
                v-for="ability in pokemon.abilities" 
                :key="ability"
                class="ability-badge"
              >
                {{ ability }}
              </span>
            </div>
          </div>
          
          <div class="stat-group">
            <h3>Estadísticas</h3>
            <div class="stats-list">
              <div 
                v-for="stat in pokemon.stats" 
                :key="stat.name"
                class="stat-bar"
              >
                <span class="stat-name">{{ formatStatName(stat.name) }}</span>
                <div class="stat-progress">
                  <div 
                    class="stat-fill"
                    :style="{ width: `${(stat.value / 255) * 100}%` }"
                  ></div>
                </div>
                <span class="stat-number">{{ stat.value }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="loading">
    <div class="pokeball-loader"></div>
    <p>Cargando Pokémon...</p>
  </div>
</template>

<script>
import axios from '../axios'

export default {
  name: 'PokemonDetail',
  data() {
    return {
      pokemon: null,
      isFavorite: false,
      loading: false
    }
  },
  computed: {
    isAuthenticated() {
      return !!localStorage.getItem('token')
    }
  },
  async mounted() {
    await this.fetchPokemon()
    if (this.isAuthenticated) {
      await this.checkFavorite()
    }
  },
  methods: {
    async fetchPokemon() {
      try {
        const response = await axios.get(`http://localhost:8000/api/pokemon/${this.$route.params.id}`)
        this.pokemon = response.data
      } catch (error) {
        console.error('Error fetching pokemon:', error)
        this.$router.push('/')
      }
    },
    async checkFavorite() {
      try {
        const response = await axios.get(`/favorites/check/${this.pokemon.id}`)
        this.isFavorite = response.data.is_favorite
      } catch (error) {
        console.error('Error checking favorite:', error)
      }
    },
    async toggleFavorite() {
      if (!this.isAuthenticated) return
      
      this.loading = true
      
      try {
        if (this.isFavorite) {
          await axios.delete(`/favorites/${this.pokemon.id}`)
          this.isFavorite = false
        } else {
          await axios.post('/favorites', {
            pokemon_id: this.pokemon.id.toString(),
            name: this.pokemon.name,
            image: this.pokemon.image,
            description: this.pokemon.description || ''
          })
          this.isFavorite = true
        }
      } catch (error) {
        console.error('Error toggling favorite:', error)
        alert('Error al gestionar favorito. Por favor, inicia sesión nuevamente.')
      } finally {
        this.loading = false
      }
    },
    formatStatName(name) {
      const statNames = {
        'hp': 'HP',
        'attack': 'Ataque',
        'defense': 'Defensa',
        'special-attack': 'At. Especial',
        'special-defense': 'Def. Especial',
        'speed': 'Velocidad'
      }
      return statNames[name] || name
    }
  }
}
</script>

<style scoped>
.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
}

.detail-card {
  background: white;
  border-radius: 25px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  border: 4px solid #DC0A2D;
}

.pokemon-header {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
  align-items: center;
}

.pokemon-image {
  width: 200px;
  height: 200px;
  object-fit: contain;
}

.pokemon-info {
  flex: 1;
}

.pokemon-name {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  text-transform: capitalize;
  color: #DC0A2D;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
  font-weight: bold;
}

.pokemon-types {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}

.type-badge {
  padding: 0.5rem 1rem;
  border-radius: 20px;
  color: white;
  font-weight: bold;
  text-transform: capitalize;
}

.type-normal { background: #A8A878; }
.type-fire { background: #F08030; }
.type-water { background: #6890F0; }
.type-electric { background: #F8D030; }
.type-grass { background: #78C850; }
.type-ice { background: #98D8D8; }
.type-fighting { background: #C03028; }
.type-poison { background: #A040A0; }
.type-ground { background: #E0C068; }
.type-flying { background: #A890F0; }
.type-psychic { background: #F85888; }
.type-bug { background: #A8B820; }
.type-rock { background: #B8A038; }
.type-ghost { background: #705898; }
.type-dragon { background: #7038F8; }
.type-dark { background: #705848; }
.type-steel { background: #B8B8D0; }
.type-fairy { background: #EE99AC; }

.favorite-btn {
  padding: 0.75rem 1.5rem;
  border: 3px solid #FFCB05;
  border-radius: 25px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: bold;
  transition: all 0.3s;
  background: white;
  color: #DC0A2D;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.favorite-btn:hover {
  background: #FFCB05;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.favorite-btn.is-favorite {
  background: #DC0A2D;
  color: white;
  border-color: #DC0A2D;
}

.pokemon-description {
  margin-bottom: 2rem;
}

.pokemon-description h3 {
  margin-bottom: 1rem;
  color: #DC0A2D;
  font-weight: bold;
  font-size: 1.3rem;
}

.stat-group {
  margin-bottom: 2rem;
}

.stat-group h3 {
  margin-bottom: 1rem;
  color: #DC0A2D;
  font-weight: bold;
  font-size: 1.3rem;
}

.basic-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem;
  background: #f8f9fa;
  border-radius: 10px;
  border: 2px solid #e0e0e0;
  font-weight: 600;
}

.abilities {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.ability-badge {
  padding: 0.5rem 1rem;
  background: #3B4CCA;
  color: white;
  border: none;
  border-radius: 20px;
  text-transform: capitalize;
  font-weight: 600;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.stats-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.stat-bar {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-name {
  min-width: 120px;
  font-weight: bold;
}

.stat-progress {
  flex: 1;
  height: 20px;
  background: #e0e0e0;
  border-radius: 10px;
  overflow: hidden;
}

.stat-fill {
  height: 100%;
  background: linear-gradient(90deg, #DC0A2D 0%, #FFCB05 100%);
  transition: width 0.3s;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
}

.stat-number {
  min-width: 40px;
  text-align: right;
  font-weight: bold;
}

.loading {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  height: 50vh;
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

@media (max-width: 768px) {
  .pokemon-header {
    flex-direction: column;
    text-align: center;
  }
  
  .pokemon-image {
    width: 150px;
    height: 150px;
  }
  
  .pokemon-name {
    font-size: 2rem;
  }
}
</style>