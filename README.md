# Pokédex App - Prueba Técnica Fullstack

Una aplicación web completa que permite a los usuarios autenticarse, registrarse y administrar una lista de favoritos de Pokémon obtenidos desde la API pública de PokéAPI.

## 📋 Tabla de Contenidos

- [Requisitos Previos](#requisitos-previos)
- [Cumplimiento de Requerimientos](#cumplimiento-de-requerimientos)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)
- [Instalación y Ejecución](#instalación-y-ejecución)
- [Configuración de Variables de Entorno](#configuración-de-variables-de-entorno)
- [Usuario de Prueba](#usuario-de-prueba)
- [Justificación de Decisiones de Diseño](#justificación-de-decisiones-de-diseño)
- [API Endpoints](#api-endpoints)
- [Arquitectura del Proyecto](#arquitectura-del-proyecto)
- [Características Implementadas](#características-implementadas)

## 📦 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado lo siguiente en tu sistema:

### Backend
- **PHP** >= 8.2
  - Verificar: `php -v`
  - Descargar: https://www.php.net/downloads
- **Composer** >= 2.0
  - Verificar: `composer -V`
  - Descargar: https://getcomposer.org/download/
- **SQLite** (incluido con PHP) o **MySQL** >= 8.0
  - Para MySQL: https://dev.mysql.com/downloads/

### Frontend
- **Node.js** >= 18.0
  - Verificar: `node -v`
  - Descargar: https://nodejs.org/
- **npm** >= 9.0 (incluido con Node.js)
  - Verificar: `npm -v`

### Opcional (para Docker)
- **Docker** >= 20.10
- **Docker Compose** >= 2.0

## ✅ Cumplimiento de Requerimientos

### Backend (Laravel 12)

- ✅ **Autenticación completa**: Registro, login, logout con validaciones
- ✅ **CRUD de favoritos**: Gestión completa con relaciones de usuario
- ✅ **Migrations y Seeders**: Base de datos estructurada
- ✅ **Middleware de autenticación**: Endpoints protegidos con Laravel Sanctum
- ✅ **Código organizado**: Controladores, servicios y validaciones
- ✅ **Respuestas JSON estructuradas**

### Frontend (Vue.js 3)

- ✅ **Formularios de autenticación**: Registro y login
- ✅ **Listado de recursos**: Navegación entre endpoints de PokéAPI
- ✅ **Vista de favoritos**: Gestión personal de favoritos
- ✅ **Paginación**: Navegación por páginas
- ✅ **Filtros y búsquedas**: Por nombre, número y tipo
- ✅ **Componentes reactivos**: Arquitectura modular

### Bonus Implementados

- ✅ **Validación con reCAPTCHA en registro y login** (requiere configuración en .env)
- ✅ **Recuperación de contraseña por correo** (requiere configuración SMTP en .env)
- ✅ **Solo una sesión activa por usuario**: Al iniciar sesión se invalidan todos los tokens anteriores
- ✅ Paginación en listados con navegación intuitiva
- ✅ Filtros por tipo de Pokémon
- ✅ Búsqueda por nombre o número
- ✅ Búsqueda aleatoria de Pokémon
- ✅ Animaciones de carga con Pokébola
- ✅ Diseño temático de Pokédex con colores oficiales
- ✅ Docker para despliegue

## 🛠 Tecnologías Utilizadas

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
- **Google Fonts (Poppins)** - Tipografía moderna

### DevOps

- **Docker & Docker Compose** - Containerización
- **Nginx** - Servidor web y proxy reverso

## 🚀 Instalación y Ejecución

### Opción 1: Desarrollo Local (Recomendado para pruebas)

#### Paso 1: Clonar el Repositorio

```bash
git clone https://github.com/tu-usuario/PruebaNetgrid.git
cd PruebaNetgrid
```

#### Paso 2: Configurar Backend

```bash
# Navegar a la carpeta backend
cd backend

# Instalar dependencias de PHP
composer install

# Copiar archivo de configuración
copy .env.example .env
# En Linux/Mac: cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Crear base de datos SQLite (automático) o configurar MySQL en .env
# Para SQLite, el archivo database.sqlite se crea automáticamente

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (crea usuario de prueba)
php artisan db:seed

# Iniciar servidor de desarrollo
php artisan serve
# El backend estará disponible en http://localhost:8000
```

#### Paso 3: Configurar Frontend

```bash
# Abrir nueva terminal y navegar a la carpeta frontend
cd frontend

# Instalar dependencias de Node.js
npm install

# Iniciar servidor de desarrollo
npm run dev
# El frontend estará disponible en http://localhost:3000
```

#### Paso 4: Acceder a la Aplicación

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000/api

### Opción 2: Con Docker

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/PruebaNetgrid.git
cd PruebaNetgrid

# 2. Configurar variables de entorno del backend para Docker
cd backend
copy .env.docker.example .env
# En Linux/Mac: cp .env.docker.example .env
cd ..

# 3. Levantar servicios
docker-compose up -d

# 4. Instalar dependencias y configurar base de datos
docker-compose exec backend composer install
docker-compose exec backend php artisan key:generate
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan db:seed

# 5. Acceder a la aplicación
# Frontend: http://localhost:3000
# Backend API: http://localhost:8000
```

**Nota sobre Docker**: 
- Para Docker usa `.env.docker.example` (configurado con MySQL)
- Para desarrollo local usa `.env.example` (configurado con SQLite)
- Ambos funcionan inmediatamente sin configuración adicional

## ⚙️ Configuración de Variables de Entorno

### Backend (.env)

**IMPORTANTE**: El archivo `.env.example` ya viene configurado con valores por defecto que funcionan inmediatamente. Solo necesitas copiarlo a `.env` y generar la clave de aplicación.

#### Configuración Mínima (Funciona sin cambios)

```bash
cd backend
copy .env.example .env
php artisan key:generate
```

Esto es suficiente para que la aplicación funcione con:
- Base de datos SQLite (se crea automáticamente)
- Autenticación completa
- CRUD de favoritos
- Todas las funcionalidades excepto reCAPTCHA y recuperación de contraseña

#### Configuración Completa (Opcional)

Si deseas habilitar reCAPTCHA y recuperación de contraseña, ajusta estos valores:

```env
# Aplicación
APP_NAME="Pokemon API"
APP_ENV=local
APP_KEY=                          # Se genera con: php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=es                     # Idioma de la aplicación

# Base de datos (SQLite por defecto)
DB_CONNECTION=sqlite

# Para usar MySQL en lugar de SQLite:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=pokemon_db
# DB_USERNAME=root
# DB_PASSWORD=

# CORS - Dominios permitidos
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000

# reCAPTCHA (Opcional - obtener en https://www.google.com/recaptcha/admin)
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here

# Correo (para recuperación de contraseña)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password    # Contraseña de aplicación de Gmail
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pokemon-app.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Cómo Obtener las Credenciales (Opcional)

#### reCAPTCHA (Opcional - Para prevenir bots)
1. Visita https://www.google.com/recaptcha/admin
2. Registra un nuevo sitio (reCAPTCHA v2 - "No soy un robot")
3. Agrega `localhost` a los dominios
4. Copia las claves al `.env`:
   - `RECAPTCHA_SITE_KEY`: Clave del sitio
   - `RECAPTCHA_SECRET_KEY`: Clave secreta

**Nota**: Si no configuras reCAPTCHA, la aplicación funcionará normalmente pero sin protección contra bots.

#### Gmail para Recuperación de Contraseña (Opcional)

**Opción 1: Gmail (Recomendado para pruebas)**

1. Habilita verificación en 2 pasos en tu cuenta de Gmail:
   - Ve a https://myaccount.google.com/security
   - Activa "Verificación en 2 pasos"

2. Genera una contraseña de aplicación:
   - Ve a https://myaccount.google.com/apppasswords
   - Selecciona "Correo" y "Otro (nombre personalizado)"
   - Escribe "Pokemon App" y genera
   - Copia la contraseña de 16 caracteres

3. Configura en `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx  # Contraseña de aplicación
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pokemon-app.com"
```

**Opción 2: Mailtrap (Recomendado para desarrollo)**

1. Crea cuenta gratuita en https://mailtrap.io
2. Ve a "Email Testing" > "Inboxes" > "My Inbox"
3. Copia las credenciales SMTP
4. Configura en `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pokemon-app.com"
```

**Opción 3: Sin configuración de correo**

Si no configuras el correo, la aplicación funcionará normalmente excepto la recuperación de contraseña. El sistema registrará el token en los logs:

```bash
# Ver el token de recuperación en los logs
tail -f storage/logs/laravel.log
```

Busca una línea como: `Password reset token for email@example.com: abc123...`

### Frontend

No requiere archivo `.env` adicional. La URL del backend está configurada en `src/axios.js`:

```javascript
baseURL: 'http://localhost:8000/api'
```

## 👤 Usuario de Prueba

El seeder crea automáticamente un usuario de prueba:

- **Email**: `test@example.com`
- **Contraseña**: `password123`
- **Nombre**: `Usuario de Prueba`

Puedes iniciar sesión inmediatamente con estas credenciales.

## 🎨 Justificación de Decisiones de Diseño

### Elección de CSS Nativo

**Decisión**: Utilizar CSS3 nativo con Flexbox y Grid en lugar de frameworks como Bootstrap o Tailwind.

**Justificación**:

1. **Control Total**: CSS nativo nos permite tener control absoluto sobre cada aspecto del diseño sin estar limitados por las convenciones de un framework.

2. **Rendimiento Óptimo**: Sin dependencias externas, el bundle final es más ligero y la aplicación carga más rápido.

3. **Personalización Completa**: Para lograr el diseño temático de Pokémon que queríamos, necesitábamos libertad total para implementar colores, sombras y animaciones específicas.

4. **Aprendizaje y Demostración**: Demuestra conocimiento profundo de CSS moderno sin depender de abstracciones.

### Diseño Temático de Pokémon

**Inspiración**: Los videojuegos de Pokémon y el anime que tanto nos apasionan.

**Filosofía de Diseño**:

Como gran fanático de Pokémon desde la infancia, quise plasmar la esencia de esta franquicia que tanto me ha marcado. El diseño busca evocar la nostalgia de los juegos clásicos mientras mantiene una interfaz moderna y funcional.

**Elementos Clave**:

1. **Paleta de Colores Oficial**:
   - **Rojo Pokémon (#DC0A2D)**: Color principal de la marca, usado en navbar y elementos destacados
   - **Amarillo Pokémon (#FFCB05)**: Color secundario, usado en acentos y hover states
   - **Azul Pokémon (#3B4CCA)**: Color terciario, usado en bordes y botones secundarios
   - **Fondo Blanco Hueso (#FFF8F0)**: Cálido y acogedor, evoca las páginas de la Pokédex

2. **Tipografía Poppins**:
   - Fuente moderna y bold que recuerda a los textos de los juegos
   - Excelente legibilidad en todos los tamaños
   - Pesos variables (400-800) para jerarquía visual

3. **Animaciones Temáticas**:
   - **Pokébola Girando**: Loader personalizado que refuerza la temática
   - **Transiciones Suaves**: Hover effects que dan vida a la interfaz
   - **Efectos de Elevación**: Las tarjetas "flotan" al pasar el mouse, como en los juegos

4. **Interfaz Intuitiva**:
   - **Simplicidad**: Navegación clara sin elementos innecesarios
   - **Feedback Visual**: Cada acción tiene una respuesta visual inmediata
   - **Responsive**: Funciona perfectamente en móviles y escritorio

5. **Detalles de Amor al Franchise**:
   - Número de Pokédex en formato #001, #025, etc.
   - Badges de tipos con colores oficiales
   - Botón de búsqueda aleatoria (como "Sorpréndeme" de los juegos)
   - Footer con créditos a PokéAPI

**Resultado**: Una aplicación que no solo cumple con los requisitos técnicos, sino que también transmite la pasión por Pokémon, creando una experiencia memorable y divertida para los usuarios.

### Arquitectura de Componentes

- **Componentes Reutilizables**: Diseñados para ser modulares y fáciles de mantener
- **Estado Reactivo**: Uso de Vue 3 Composition API para mejor organización
- **Separación de Responsabilidades**: Cada componente tiene una función específica

## 📡 API Endpoints

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
    "password": "password123",
    "recaptcha_token": "optional_token"
}

GET /api/pokemon?page=1&limit=20&type=fire
GET /api/pokemon/{id}
GET /api/pokemon/search?q=pikachu
GET /api/types
GET /api/recaptcha-site-key
```

### Protegidos (requieren Bearer Token)

```http
Authorization: Bearer {token}

GET /api/favorites
POST /api/favorites
{
    "pokemon_id": "25",
    "name": "pikachu",
    "image": "https://...",
    "description": "Electric mouse pokemon"
}

DELETE /api/favorites/{pokemonId}
GET /api/favorites/check/{pokemonId}
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
      "types": ["grass", "poison"],
      "description": "A strange seed was planted on its back at birth..."
    }
  ],
  "count": 1302,
  "current_page": 1,
  "per_page": 20,
  "total_pages": 66
}
```

## 📁 Arquitectura del Proyecto

### Backend

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php           # Autenticación
│   │   │   ├── FavoriteController.php       # CRUD favoritos
│   │   │   ├── PokemonController.php        # Proxy PokéAPI
│   │   │   ├── TypeController.php           # Tipos Pokémon
│   │   │   └── PasswordResetController.php  # Recuperación
│   │   └── Middleware/
│   │       └── ApiAuthMiddleware.php        # Protección rutas
│   ├── Models/
│   │   ├── User.php                         # Usuario
│   │   └── Favorite.php                     # Favoritos
│   └── Services/
│       └── RecaptchaService.php             # Validación reCAPTCHA
├── database/
│   ├── migrations/                          # Estructura BD
│   │   ├── create_users_table.php
│   │   ├── create_favorites_table.php
│   │   └── create_password_reset_tokens_table.php
│   └── seeders/
│       └── DatabaseSeeder.php               # Usuario de prueba
├── lang/
│   └── es/
│       └── validation.php                   # Mensajes en español
└── routes/
    └── api.php                              # Rutas API
```

### Frontend

```
frontend/
├── src/
│   ├── components/                          # Componentes reutilizables
│   ├── views/
│   │   ├── Home.vue                         # Listado principal
│   │   ├── Login.vue                        # Autenticación
│   │   ├── Register.vue                     # Registro
│   │   ├── PokemonDetail.vue                # Detalles Pokémon
│   │   ├── Favorites.vue                    # Lista favoritos
│   │   ├── SearchResults.vue                # Resultados búsqueda
│   │   └── ForgotPassword.vue               # Recuperar contraseña
│   ├── router/
│   │   └── index.js                         # Rutas SPA
│   ├── axios.js                             # Configuración HTTP
│   ├── App.vue                              # Componente raíz
│   ├── main.js                              # Punto entrada
│   └── style.css                            # Estilos globales
├── public/
│   └── pokemon-font.css                     # Fuentes personalizadas
├── index.html                               # HTML base
├── package.json                             # Dependencias Node
└── vite.config.js                           # Configuración Vite
```

## ✨ Características Implementadas

### Navegación entre Endpoints de PokéAPI

La aplicación navega inteligentemente entre diferentes endpoints:

1. **Lista inicial**: `/pokemon` - Obtiene nombres y URLs
2. **Detalles básicos**: `/pokemon/{id}` - Sprites, tipos, stats
3. **Información de especie**: `/pokemon-species/{id}` - Descripción en español
4. **Datos de tipos**: `/type/{type}` - Para filtros

### Optimizaciones de Rendimiento

- **Peticiones Concurrentes**: Uso de `Http::pool()` en Laravel para obtener múltiples Pokémon en paralelo
- **Lazy Loading**: Carga bajo demanda de imágenes
- **Cache**: Almacenamiento temporal de tipos de Pokémon
- **Scroll Automático**: Al cambiar de página, scroll al inicio para mejor UX

### Seguridad Implementada

- **Autenticación basada en tokens** (Laravel Sanctum)
- **Validaciones de entrada** en backend y frontend
- **Middleware de autenticación** para rutas protegidas
- **CORS configurado** para dominios específicos
- **Sanitización de datos** antes de almacenar
- **reCAPTCHA** para prevenir bots
- **Rate limiting** en endpoints sensibles
- **Sesión única por usuario** (invalida tokens anteriores)

## ❓ Preguntas Frecuentes

### ¿Necesito configurar reCAPTCHA para que funcione?
**No**. La aplicación funciona perfectamente sin reCAPTCHA. Solo no tendrás protección contra bots en el registro.

### ¿Necesito configurar correo para que funcione?
**No**. La aplicación funciona completamente sin configuración de correo. Solo no podrás usar la recuperación de contraseña.

### ¿Qué base de datos usa por defecto?
**SQLite**. Se crea automáticamente en `backend/database/database.sqlite`. No necesitas instalar MySQL.

### ¿Cómo pruebo la recuperación de contraseña sin configurar correo?
El token se registra en los logs. Puedes verlo con:
```bash
cd backend
tail -f storage/logs/laravel.log
```

### ¿Puedo usar MySQL en lugar de SQLite?
Sí. Cambia en `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pokemon_db
DB_USERNAME=root
DB_PASSWORD=tu_password
```

## 🐛 Solución de Problemas

### Error: "Class 'SQLite3' not found"
- Habilita la extensión SQLite en `php.ini`: `extension=sqlite3`

### Error: "CORS policy"
- Verifica que `SANCTUM_STATEFUL_DOMAINS` en `.env` incluya tu dominio frontend

### Error: "Token inválido"
- Asegúrate de que el backend esté corriendo en el puerto 8000
- Verifica que el token se esté guardando en localStorage

### Frontend no conecta con Backend
- Verifica que ambos servidores estén corriendo
- Revisa la URL en `frontend/src/axios.js`

## 📊 Métricas de Rendimiento

- **Tiempo de carga inicial**: < 2s
- **Carga de listado (20 Pokémon)**: ~2-3s (optimizado con peticiones concurrentes)
- **Navegación entre páginas**: < 500ms
- **Búsquedas**: < 1s
- **Filtros**: Aplicación instantánea con feedback visual

## 📞 Contacto

**Desarrollador**: Emanuel Gómez Díaz  
**Email**: emmanuelgodi22@gmail.com  
**Teléfono**: +57 3136640809  
**Portafolio**: https://689f8d330b54b40008d1d849--emanuel-gomez-diaz.netlify.app/

---

## 📦 Nota para Evaluadores

**¡La aplicación funciona inmediatamente sin configuración adicional!**

1. Copia `.env.example` a `.env`
2. Ejecuta `php artisan key:generate`
3. Ejecuta `php artisan migrate && php artisan db:seed`
4. ¡Listo! Usa el usuario de prueba: `test@example.com` / `password123`

### ⚠️ IMPORTANTE: FUNCIONES BONUS IMPLEMENTADAS

**LAS SIGUIENTES FUNCIONES BONUS ESTÁN COMPLETAMENTE DESARROLLADAS E IMPLEMENTADAS:**

✅ **reCAPTCHA en Registro y Login** - Protección contra bots
✅ **Recuperación de Contraseña por Correo** - Sistema completo de reset de contraseña

**PARA PROBAR ESTAS FUNCIONES BONUS, ES NECESARIO CONFIGURAR LAS VARIABLES DE ENTORNO:**

- **reCAPTCHA**: La configuracion de las claves ya se explica en el  `.env.example` y en este archivo Readme
- **Envío de Correos**: Requiere configurar credenciales SMTP en el archivo `.env`

**Si no configuras el correo:**
- La aplicación funcionará normalmente
- El token de recuperación se registrará en `storage/logs/laravel.log`
- Puedes copiar el token manualmente para probar la funcionalidad

**Para habilitar el envío real de correos**, consulta la sección [Configuración de Variables de Entorno](#configuración-de-variables-de-entorno).

---

**Nota**: Esta aplicación consume la API pública de [PokéAPI](https://pokeapi.co/) para obtener información actualizada de Pokémon.

**Desarrollado con ❤️ y pasión por Pokémon para NetGrid**