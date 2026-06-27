FROM node:22-alpine

WORKDIR /usr/src/app

COPY package*.json ./
RUN npm install

COPY . .

EXPOSE 3000

CMD npx nuxt dev --host 0.0.0.0
