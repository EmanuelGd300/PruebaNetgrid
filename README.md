# Pokedex App - Prueba Técnica Fullstack

Una aplicación web completa que permite a los usuarios autenticarse, registrarse y administrar una lista de favoritos de Pokémon obtenidos desde la API pública de PokéAPI.

## Cumplimiento de Requerimientos

### Backend (Laravel 12)

- **Autenticación completa**: Registro, login, logout con validaciones
- **CRUD de favoritos**: Gestión completa con relaciones de usuario
- **Migrations y Seeders**: Base de datos estructurada
- **Middleware de autenticación**: Endpoints protegidos
- **Código organizado**: Controladores, servicios y validaciones
- **Respuestas JSON estructuradas**

### Frontend (Vue.js 3)

- **Formularios de autenticación**: Registro y login
- **Listado de recursos**: Navegación entre endpoints de PokéAPI
- **Vista de favoritos**: Gestión personal de favoritos
- **Paginación**: Navegación por páginas
- **Filtros y búsquedas**: Por nombre y tipo
- **Componentes reactivos**: Arquitectura modular

### Bonus Implementados

- ✅ Validación con reCAPTCHA en registro y login
- ✅ Recuperación de contraseña por correo
- ✅ **Solo una sesión activa por usuario**: Al iniciar sesión se invalidan todos los tokens anteriores
- ✅ Paginación en listados con navegación intuitiva
- ✅ Filtros por tipo de Pokémon
- ✅ Búsqueda por nombre o número
- ✅ Búsqueda aleatoria de Pokémon
- ✅ Animaciones de carga con Pokébola
- ✅ Diseño temático de Pokédex con colores oficiales
- ✅ Docker para despliegue

## Tecnologías Utilizadas

### Backend

- **Laravel 12** - Framework PHP moderno
- **PHP 8.2** - Última versión estable
- **Laravel Sanctum** - Autenticación API con tokens
- **SQLite/MySQL** - Base de datos flexible
- **Guzzle HTTP** - Cliente para consumir APIs externas

### Frontend

- **Vue.js 3** - Framework reactivo con Composition API
- **Vue Router 4** - Navegación SPA
- **Axios** - Cliente HTTP para APIs
- **CSS3 nativo** - Diseño responsive sin frameworks

### DevOps

- **Docker & Docker Compose** - Containerización
- **Nginx** - Servidor web y proxy reverso

## Instalación y Ejecución

### Opción 1: Con Docker (Recomendado)

```bash
# 1. Clonar repositorio
git clone <repository-url>
cd PruebaTecnicaNetgrid

# 2. Levantar servicios
docker-compose up -d

# 3. Configurar base de datos
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan db:seed

# 4. Acceder a la aplicación
# Frontend: http://localhost:5173
# Backend API: http://localhost:8000
```

### Opción 2: Desarrollo Local

#### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

#### Frontend

```bash
cd frontend
npm install
npm run dev
```

## Variables de Entorno

### Backend (.env)

```env
# Aplicación
APP_NAME="Pokemon API"
APP_ENV=production
APP_KEY=base64:generated_key
APP_DEBUG=false
APP_URL=http://localhost:8000

# Base de datos
DB_CONNECTION=sqlite
# Para MySQL: DB_CONNECTION=mysql, DB_HOST=mysql, DB_PORT=3306, DB_DATABASE=pokemon_db

# CORS
SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173

# reCAPTCHA (Opcional)
RECAPTCHA_SITE_KEY=your_site_key
RECAPTCHA_SECRET_KEY=your_secret_key

# Correo (para recuperación de contraseña)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pokemon-app.com"
```

## Usuario de Prueba

**Credenciales predefinidas:**

- **Email**: `test@example.com`
- **Contraseña**: `password123`
- **Nombre**: `Usuario de Prueba`

_Este usuario se crea automáticamente con el seeder_

## API Endpoints

### Públicos

```http
POST /api/register
Content-Type: application/json
{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "recaptcha_token": "optional_token"
}

POST /api/login
Content-Type: application/json
{
    "email": "test@example.com",
    "password": "password123"
}

GET /api/pokemon?page=1&limit=20&type=fire
GET /api/pokemon/{id}
GET /api/pokemon/search?q=pikachu
GET /api/types
```

### Protegidos (requieren Bearer Token)

```http
Authorization: Bearer {token}

GET /api/favorites
POST /api/favorites
{
    "pokemon_id": 25,
    "name": "pikachu",
    "image": "https://...",
    "description": "Electric mouse pokemon"
}

DELETE /api/favorites/{id}
POST /api/logout
GET /api/user
```

### Respuestas de Ejemplo

#### Login exitoso

```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "user": {
      "id": 1,
      "name": "Usuario de Prueba",
      "email": "test@example.com"
    },
    "token": "1|abc123..."
  }
}
```

#### Lista de Pokémon

```json
{
  "results": [
    {
      "id": 1,
      "name": "bulbasaur",
      "image": "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/1.png",
      "types": ["grass", "poison"]
    }
  ],
  "count": 1302,
  "current_page": 1,
  "per_page": 20,
  "total_pages": 66
}
```

## Arquitectura del Proyecto

### Backend

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php      # Autenticación
│   │   │   ├── FavoriteController.php  # CRUD favoritos
│   │   │   ├── PokemonController.php   # Proxy PokéAPI
│   │   │   └── TypeController.php      # Tipos Pokémon
│   │   ├── Middleware/
│   │   │   └── Authenticate.php        # Protección rutas
│   │   └── Requests/                   # Validaciones
│   ├── Models/
│   │   ├── User.php                    # Usuario
│   │   └── Favorite.php                # Favoritos
│   └── Services/
│       └── RecaptchaService.php        # Validación reCAPTCHA
├── database/
│   ├── migrations/                     # Estructura BD
│   └── seeders/                        # Datos iniciales
└── routes/
    └── api.php                         # Rutas API
```

### Frontend

```
frontend/
├── src/
│   ├── components/
│   │   ├── PokemonCard.vue            # Tarjeta Pokémon
│   │   └── Toast.vue                  # Notificaciones
│   ├── views/
│   │   ├── Home.vue                   # Listado principal
│   │   ├── Login.vue                  # Autenticación
│   │   ├── Register.vue               # Registro
│   │   ├── PokemonDetail.vue          # Detalles Pokémon
│   │   ├── Favorites.vue              # Lista favoritos
│   │   └── ForgotPassword.vue         # Recuperar contraseña
│   ├── router/
│   │   └── index.js                   # Rutas SPA
│   ├── App.vue                        # Componente raíz
│   └── main.js                        # Punto entrada
└── package.json
```

## Seguridad Implementada

- **Autenticación basada en tokens** (Laravel Sanctum)
- **Validaciones de entrada** en backend y frontend
- **Middleware de autenticación** para rutas protegidas
- **CORS configurado** para dominios específicos
- **Sanitización de datos** antes de almacenar
- **reCAPTCHA** para prevenir bots en registro
- **Rate limiting** en endpoints sensibles

## Decisiones de Diseño

### CSS Nativo vs Frameworks

**Elección**: CSS3 nativo con Flexbox/Grid
**Justificación**:

- Mayor control sobre el diseño
- Menor tamaño del bundle final
- Mejor rendimiento sin dependencias externas
- Flexibilidad total para personalización

### Arquitectura de Componentes

- **Componentes reutilizables**: PokemonCard, Toast
- **Vistas especializadas**: Una por funcionalidad principal
- **Estado reactivo**: Uso de ref() y reactive() de Vue 3
- **Composición API**: Mejor organización del código

### Gestión de Estado

- **Local state**: Para componentes individuales
- **Props/Events**: Comunicación padre-hijo
- **LocalStorage**: Persistencia de token de autenticación

## Funcionalidades Detalladas

### Navegación entre Endpoints

La aplicación navega inteligentemente entre diferentes endpoints de PokéAPI:

1. **Lista inicial**: `/pokemon` - Obtiene nombres y URLs
2. **Detalles básicos**: `/pokemon/{id}` - Sprites, tipos, stats
3. **Información de especie**: `/pokemon-species/{id}` - Descripción
4. **Datos de tipos**: `/type/{type}` - Para filtros

### Manejo de Estado en Vue.js

- **Reactividad**: Uso de `ref()` y `reactive()`
- **Computed properties**: Para datos derivados
- **Watchers**: Para efectos secundarios
- **Lifecycle hooks**: Para inicialización de datos

### Paginación y Filtros

- **Paginación**: Navegación por páginas con límites configurables
- **Búsqueda**: Por nombre con coincidencias parciales
- **Filtros**: Por tipo de Pokémon
- **Combinación**: Filtros + paginación simultáneos

## Características Avanzadas

### Bonus Implementados

1. **reCAPTCHA**: Protección contra bots en registro y login
2. **Recuperación de contraseña**: Sistema completo por email
3. **Sesión única por usuario**:
   - Al iniciar sesión se ejecuta `$user->tokens()->delete()` eliminando todas las sesiones previas
   - Se genera un nuevo `session_token` único para el usuario
   - Garantiza que solo haya una sesión activa por usuario en cualquier momento
   - Si el usuario inicia sesión desde otro dispositivo, la sesión anterior se invalida automáticamente
4. **Paginación**: Navegación eficiente por grandes datasets con indicadores visuales
5. **Búsqueda avanzada**: 
   - Por nombre con coincidencias parciales y exactas
   - Por número de Pokédex
   - Búsqueda aleatoria para descubrir nuevos Pokémon
6. **Filtros por tipo**: Selector desplegable con 18 tipos de Pokémon
7. **Animaciones de carga**: Pokébola girando con mensajes contextuales
8. **Diseño temático**: Colores oficiales de Pokémon (Rojo #DC0A2D, Amarillo #FFCB05, Azul #3B4CCA)

### Optimizaciones

- **Peticiones concurrentes**: Uso de `Http::pool()` en Laravel para obtener múltiples Pokémon en paralelo
- **Lazy loading**: Carga bajo demanda de imágenes
- **Debounce**: En búsquedas para reducir requests
- **Cache**: Almacenamiento temporal de tipos de Pokémon
- **Error handling**: Manejo robusto de errores de API
- **Scroll automático**: Al cambiar de página, scroll al inicio para mejor UX

## Docker Configuration

```yaml
# docker-compose.yml
version: "3.8"
services:
  backend:
    build: ./backend
    ports:
      - "8000:8000"
    environment:
      - DB_CONNECTION=mysql
      - DB_HOST=mysql
    depends_on:
      - mysql

  frontend:
    build: ./frontend
    ports:
      - "5173:5173"

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: pokemon_db
      MYSQL_ROOT_PASSWORD: root
```

## Base de Datos

### Migrations

```sql
-- users table
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    email_verified_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- favorites table
CREATE TABLE favorites (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    pokemon_id INTEGER,
    name VARCHAR(255),
    image VARCHAR(500),
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## Testing

### Backend

```bash
cd backend
php artisan test
```

### Frontend

```bash
cd frontend
npm run test
```

## Métricas de Rendimiento

- **Tiempo de carga inicial**: < 2s
- **Carga de listado (20 Pokémon)**: ~2-3s (optimizado con peticiones concurrentes)
- **Navegación entre páginas**: < 500ms
- **Búsquedas**: < 1s
- **Carga de imágenes**: Lazy loading implementado
- **Filtros**: Aplicación instantánea con feedback visual

## Contribución

1. Fork del proyecto
2. Crear rama feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

## Contacto

Para preguntas sobre la implementación o dudas técnicas, contactar al desarrollador: Emanuel Gómez Díaz - +57 3136640809 - emmanuelgodi22@gmail.com .

---

**Nota**: Esta aplicación consume la API pública de [PokéAPI](https://pokeapi.co/) para obtener información actualizada de Pokémon.
