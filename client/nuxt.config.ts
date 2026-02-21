import Aura from '@primeuix/themes/aura';

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  css: ['~/assets/css/main.css'],
  modules: [
    '@nuxtjs/tailwindcss',
    '@primevue/nuxt-module',
  ],
  primevue: {
    options: {
      theme: {
        preset: Aura,
        options: {
          darkModeSelector: 'none'
        }
      }
    }
  }
})