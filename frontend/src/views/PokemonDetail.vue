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
    Cargando...
  </div>
</template>

<script>
import axios from 'axios'

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
        const token = localStorage.getItem('token')
        const response = await axios.get(
          `http://localhost:8000/api/favorites/check/${this.pokemon.id}`,
          { headers: { Authorization: `Bearer ${token}` } }
        )
        this.isFavorite = response.data.is_favorite
      } catch (error) {
        console.error('Error checking favorite:', error)
      }
    },
    async toggleFavorite() {
      if (!this.isAuthenticated) return
      
      this.loading = true
      const token = localStorage.getItem('token')
      
      try {
        if (this.isFavorite) {
          await axios.delete(
            `http://localhost:8000/api/favorites/${this.pokemon.id}`,
            { headers: { Authorization: `Bearer ${token}` } }
          )
          this.isFavorite = false
        } else {
          await axios.post(
            'http://localhost:8000/api/favorites',
            {
              pokemon_id: this.pokemon.id.toString(),
              name: this.pokemon.name,
              image: this.pokemon.image,
              description: this.pokemon.description || ''
            },
            { headers: { Authorization: `Bearer ${token}` } }
          )
          this.isFavorite = true
        }
      } catch (error) {
        console.error('Error toggling favorite:', error)
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
  background: rgba(255, 255, 255, 0.95);
  border-radius: 15px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
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
  color: #333;
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
  border: none;
  border-radius: 25px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s;
  background: #ddd;
  color: #666;
}

.favorite-btn.is-favorite {
  background: #ff6b6b;
  color: white;
}

.pokemon-description {
  margin-bottom: 2rem;
}

.pokemon-description h3 {
  margin-bottom: 1rem;
  color: #333;
}

.stat-group {
  margin-bottom: 2rem;
}

.stat-group h3 {
  margin-bottom: 1rem;
  color: #333;
}

.basic-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem;
  background: #f5f5f5;
  border-radius: 8px;
}

.abilities {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.ability-badge {
  padding: 0.5rem 1rem;
  background: #667eea;
  color: white;
  border-radius: 15px;
  text-transform: capitalize;
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
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  transition: width 0.3s;
}

.stat-number {
  min-width: 40px;
  text-align: right;
  font-weight: bold;
}

.loading {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 50vh;
  font-size: 1.5rem;
  color: white;
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