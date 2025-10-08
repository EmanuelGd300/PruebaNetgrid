<template>
  <div class="pokemon-list">
    <div class="container">
      <h1>Pokédex</h1>
      
      <div class="filters">
        <div class="search-bar">
          <input 
            v-model="searchTerm" 
            type="text" 
            placeholder="Buscar Pokémon..."
            class="search-input"
          />
        </div>
        <div class="type-filter">
          <select v-model="selectedType" class="type-select" @change="filterByType">
            <option value="">Todos los tipos</option>
            <option v-for="type in types" :key="type.name" :value="type.name">
              {{ type.name }}
            </option>
          </select>
        </div>
      </div>
      
      <div class="pokemon-grid">
        <PokemonCard
          v-for="pokemon in filteredPokemon"
          :key="pokemon.id"
          :pokemon="pokemon"
          :show-favorite-button="true"
          :show-stats="true"
          @click="goToDetail"
          @favorite-changed="onFavoriteChanged"
        />
      </div>
      
      <div class="pagination">
        <button 
          @click="loadMore" 
          v-if="hasMore && !loading" 
          class="load-more-btn"
        >
          Cargar más
        </button>
        <div v-if="loading" class="loading">
          Cargando más Pokémon...
        </div>
        <div class="page-info">
          Página {{ page }} de {{ totalPages }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/axios'
import PokemonCard from '@/components/PokemonCard.vue'

export default {
  name: 'PokemonList',
  components: {
    PokemonCard
  },
  data() {
    return {
      pokemon: [],
      searchTerm: '',
      selectedType: '',
      types: [],
      loading: false,
      page: 1,
      totalPages: 1,
      hasMore: true
    }
  },
  computed: {
    filteredPokemon() {
      let filtered = this.pokemon
      
      if (this.searchTerm) {
        filtered = filtered.filter(p => 
          p.name.toLowerCase().includes(this.searchTerm.toLowerCase())
        )
      }
      
      if (this.selectedType) {
        filtered = filtered.filter(p => 
          p.types.includes(this.selectedType)
        )
      }
      
      return filtered
    }
  },
  async mounted() {
    await this.fetchPokemon()
  },
  methods: {
    async fetchPokemon() {
      this.loading = true
      try {
        const response = await api.get(`/pokemon?page=${this.page}&limit=20`)
        this.pokemon = [...this.pokemon, ...response.data.results]
      } catch (error) {
        console.error('Error fetching pokemon:', error)
      } finally {
        this.loading = false
      }
    },
    goToDetail(pokemon) {
      this.$router.push(`/pokemon/${pokemon.id}`)
    },
    onFavoriteChanged(data) {
      console.log(`${data.pokemon.name} ${data.isFavorite ? 'agregado a' : 'removido de'} favoritos`)
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

h1 {
  text-align: center;
  color: white;
  font-size: 3rem;
  margin-bottom: 2rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.search-bar {
  display: flex;
  justify-content: center;
  margin-bottom: 2rem;
}

.search-input {
  width: 100%;
  max-width: 400px;
  padding: 1rem;
  border: none;
  border-radius: 25px;
  font-size: 1rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  outline: none;
}

.pokemon-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.loading {
  text-align: center;
  color: white;
  font-size: 1.2rem;
  padding: 2rem;
}

@media (max-width: 768px) {
  .pokemon-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
  }
  
  h1 {
    font-size: 2rem;
  }
}
</style>