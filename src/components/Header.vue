<script setup>
import logo from '../assets/jisho_logo_full.png';
</script>

<template>
  <div>
    <div class="row" ref="header">
      <div class="col">
        <router-link to="/search/" >
          <img class="logo mt-4 mb-3 pointer" :src="logo" alt="Logo">
        </router-link>
      </div>
    </div>
  </div>
</template>

