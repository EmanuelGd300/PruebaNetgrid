<template>
  <div class="search-results">
    <div class="container">
      <h2 class="search-title">
        Resultados para: "{{ searchQuery }}"
      </h2>
      
      <div v-if="loading" class="loading">
        <div class="pokeball-loader"></div>
        <p>Buscando en la Pokédex...</p>
      </div>
      
      <div v-else-if="pokemonList.length === 0" class="no-results">
        No se encontraron Pokémon con ese nombre.
      </div>
      
      <div v-else class="pokemon-grid">
        <div 
          v-for="pokemon in pokemonList" 
          :key="pokemon.id"
          class="pokemon-card"
          @click="goToPokemon(pokemon.id)"
        >
          <div class="pokemon-number">#{{ String(pokemon.id).padStart(3, '0') }}</div>
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
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'SearchResults',
  data() {
    return {
      pokemonList: [],
      loading: false,
      searchQuery: ''
    }
  },
  mounted() {
    this.searchQuery = this.$route.query.q || ''
    if (this.searchQuery) {
      this.searchPokemon()
    }
  },
  watch: {
    '$route.query.q'(newQuery) {
      this.searchQuery = newQuery || ''
      if (this.searchQuery) {
        this.searchPokemon()
      }
    }
  },
  methods: {
    async searchPokemon() {
      this.loading = true
      try {
        const response = await axios.get(`http://localhost:8000/api/pokemon/search?q=${this.searchQuery}`)
        this.pokemonList = response.data.results || []
      } catch (error) {
        console.error('Error searching pokemon:', error)
        this.pokemonList = []
      } finally {
        this.loading = false
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

.search-title {
  color: #DC0A2D;
  text-align: center;
  margin-bottom: 2rem;
  font-size: 1.8rem;
  font-weight: 800;
  font-family: 'Poppins', sans-serif;
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
  font-family: 'Poppins', sans-serif;
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

.no-results {
  text-align: center;
  color: #DC0A2D;
  font-size: 1.2rem;
  font-weight: 600;
  margin: 3rem 0;
  font-family: 'Poppins', sans-serif;
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
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border: 3px solid #3B4CCA;
  position: relative;
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

.pokemon-number {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: #FFCB05;
  color: #333;
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-weight: bold;
  font-size: 0.9rem;
  font-family: 'Poppins', sans-serif;
}

.pokemon-name {
  margin: 1rem 0;
  color: #DC0A2D;
  text-transform: capitalize;
  font-size: 1.3rem;
  font-weight: bold;
  font-family: 'Poppins', sans-serif;
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
</style>