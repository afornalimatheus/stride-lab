<script lang="ts" setup>
import { Button, FloatLabel, InputText } from 'primevue';
import { ref } from 'vue';

const email = ref('');
const password = ref('');
</script>

<template>
  <div class="flex flex-col h-screen items-center justify-center bg-gray-100">
    <div class="flex flex-col mb-4">
      <h1 class="mb-4 text-2xl font-bold">LOGO</h1>
    </div>

    <div class="flex flex-col mb-6">
      <FloatLabel>
        <InputText id="email" type="text" v-model="email" />
        <label for="email">Email</label>
      </FloatLabel>
    </div>

    <div class="flex flex-col mb-4">
      <FloatLabel>
        <InputText id="password" type="password" v-model="password" />
        <label for="password">Password</label>
      </FloatLabel>
    </div>

    <div class="flex flex-col">
      <Button label="Login" />
    </div>

    <div class="flex flex-col mt-4">
      <p class="text-sm text-gray-600">Esqueceu sua senha? <a href="/forgot-password">Recupere-a</a></p>
      <p class="text-sm text-gray-600">Ainda não tem uma conta? <a href="/register">Cadastre-se</a></p>
    </div>
  </div>
</template>

<style lang="scss" scoped>
</style>