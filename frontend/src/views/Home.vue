<template>
  <div class="home">
    <div class="container">
      <div v-if="loading" class="loading">
        <div class="pokeball-loader"></div>
        <p>Cargando Pokédex...</p>
      </div>
      <div v-else class="pokemon-grid">
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
      
      <div class="pagination" v-if="!loading && totalCount > 0">
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
      window.scrollTo(0, 0)
      try {
        const page = Math.floor(this.offset / this.limit) + 1
        const response = await axios.get(`http://localhost:8000/api/pokemon?limit=${this.limit}&page=${page}`)
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
  background: white;
  border-radius: 20px;
  padding: 1.5rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border: 3px solid #3B4CCA;
  position: relative;
  overflow: hidden;
}

.pokemon-card::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255, 203, 5, 0.1) 0%, transparent 70%);
  opacity: 0;
  transition: opacity 0.3s;
}

.pokemon-card:hover::before {
  opacity: 1;
}

.pokemon-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  border-color: #DC0A2D;
}

.pokemon-image {
  width: 120px;
  height: 120px;
  object-fit: contain;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
  transition: transform 0.3s;
}

.pokemon-card:hover .pokemon-image {
  transform: scale(1.1) rotate(5deg);
}

.pokemon-name {
  margin: 1rem 0;
  color: #DC0A2D;
  text-transform: capitalize;
  font-size: 1.3rem;
  font-weight: bold;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
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
  padding: 0.75rem 1.5rem;
  background: white;
  color: #DC0A2D;
  border: 3px solid #DC0A2D;
  border-radius: 25px;
  cursor: pointer;
  font-weight: bold;
  transition: all 0.3s;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.page-btn:hover:not(:disabled) {
  background: #DC0A2D;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.page-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
  transform: none;
}

.page-info {
  color: #333;
  font-weight: bold;
  font-size: 1.1rem;
  background: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  border: 3px solid #FFCB05;
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
</style>