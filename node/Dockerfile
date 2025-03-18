FROM node:22-alpine

WORKDIR /app

COPY package*.json ./

COPY webpack.mix.js ./

RUN npm install

RUN npx mix --production
