<template>
  <div class="home">
    <div class="container">
      <div class="pokemon-grid">
        <div 
          v-for="pokemon in pokemonList" 
          :key="pokemon.id"
          class="pokemon-card"
          @click="goToPokemon(pokemon.id)"
        >
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
        </div>
      </div>
      
      <div class="pagination" v-if="totalCount > 0">
        <button 
          @click="previousPage" 
          :disabled="!hasPrevious"
          class="page-btn"
        >
          Anterior
        </button>
        <span class="page-info">
          {{ Math.floor(offset / limit) + 1 }} de {{ Math.ceil(totalCount / limit) }}
        </span>
        <button 
          @click="nextPage" 
          :disabled="!hasNext"
          class="page-btn"
        >
          Siguiente
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'Home',
  data() {
    return {
      pokemonList: [],
      loading: false,
      offset: 0,
      limit: 20,
      totalCount: 0,
      hasNext: false,
      hasPrevious: false
    }
  },
  mounted() {
    this.fetchPokemon()
  },
  methods: {
    async fetchPokemon() {
      this.loading = true
      try {
        const response = await axios.get(`http://localhost:8000/api/pokemon?limit=${this.limit}&offset=${this.offset}`)
        this.pokemonList = response.data.results
        this.totalCount = response.data.count
        this.hasNext = !!response.data.next
        this.hasPrevious = !!response.data.previous
      } catch (error) {
        console.error('Error fetching pokemon:', error)
      } finally {
        this.loading = false
      }
    },
    goToPokemon(id) {
      this.$router.push(`/pokemon/${id}`)
    },
    nextPage() {
      if (this.hasNext) {
        this.offset += this.limit
        this.fetchPokemon()
      }
    },
    previousPage() {
      if (this.hasPrevious) {
        this.offset -= this.limit
        this.fetchPokemon()
      }
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

.pokemon-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.pokemon-card {
  background: rgba(255, 255, 255, 0.9);
  border-radius: 15px;
  padding: 1.5rem;
  text-align: center;
  cursor: pointer;
  transition: transform 0.3s, box-shadow 0.3s;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
}

.page-btn {
  padding: 0.5rem 1rem;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s;
}

.page-btn:hover:not(:disabled) {
  background: white;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  color: white;
  font-weight: bold;
}
</style>