FROM node:18-alpine

# Establecer directorio de trabajo
WORKDIR /app

# Copiar package.json y package-lock.json
COPY package*.json ./

# Instalar dependencias
RUN npm install

# Copiar el resto de archivos
COPY . .

# Exponer puerto
EXPOSE 3000

# Comando por defecto
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]