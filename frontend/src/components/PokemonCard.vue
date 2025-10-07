<template>
  <div class="pokemon-card" @click="$emit('click', pokemon)">
    <img :src="pokemon.image" :alt="pokemon.name" class="pokemon-image">
    <h3 class="pokemon-name">{{ pokemon.name }}</h3>
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
    <div v-if="showStats && pokemon.stats" class="pokemon-stats">
      <div class="stat">HP: {{ getStatValue('hp') }}</div>
      <div class="stat">ATK: {{ getStatValue('attack') }}</div>
    </div>
    <button 
      v-if="showFavoriteButton && isAuthenticated"
      @click.stop="toggleFavorite"
      :class="['favorite-btn', { 'is-favorite': isFavorite }]"
    >
      {{ isFavorite ? '♥' : '♡' }}
    </button>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'PokemonCard',
  props: {
    pokemon: {
      type: Object,
      required: true
    },
    showFavoriteButton: {
      type: Boolean,
      default: false
    },
    showStats: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      isFavorite: false
    }
  },
  computed: {
    isAuthenticated() {
      return !!localStorage.getItem('token')
    }
  },
  async mounted() {
    if (this.isAuthenticated && this.showFavoriteButton) {
      await this.checkFavorite()
    }
  },
  methods: {
    getStatValue(statName) {
      if (!this.pokemon.stats) return 0
      const stat = this.pokemon.stats.find(s => s.name === statName)
      return stat ? stat.value : 0
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
        this.$emit('favorite-changed', { pokemon: this.pokemon, isFavorite: this.isFavorite })
      } catch (error) {
        console.error('Error toggling favorite:', error)
      }
    }
  }
}
</script>

<style scoped>
.pokemon-card {
  background: rgba(255, 255, 255, 0.9);
  border-radius: 15px;
  padding: 1.5rem;
  text-align: center;
  cursor: pointer;
  transition: transform 0.3s, box-shadow 0.3s;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  position: relative;
}

.pokemon-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
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

.pokemon-types {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-bottom: 1rem;
}

.type-badge {
  padding: 0.3rem 0.8rem;
  border-radius: 15px;
  color: white;
  font-size: 0.8rem;
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

.pokemon-stats {
  display: flex;
  justify-content: space-around;
  margin: 0.5rem 0;
  font-size: 0.9rem;
  color: #666;
}

.favorite-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  transition: background 0.3s;
}

.favorite-btn:hover {
  background: rgba(255, 255, 255, 0.8);
}
</style>