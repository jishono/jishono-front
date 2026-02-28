<template>
  <footer>
    <div class="container">
      <div class="row justify-content-center">
        <div
          class='text-center my-4 no mx-3'
          style="max-width: 400px; width: 100%;"
        >
          <div class="mt-3 oneliner">
            {{ $t('footer.oneliner') }}
          </div>
          <div class="my-2">
            <div class="my-1">
              <router-link
                class="badge badge-pill pill-button mx-1"
                to="/about"
              >{{ $t('footer.about') }}</router-link>
              <a
                href="https://github.com/jishono/"
                target="_blank"
                rel="noopener noreferrer"
                class="badge badge-pill pill-button mx-1"
              >Github</a>
              <a
                href="https://baksida.jisho.no/om"
                target="_blank"
                rel="noopener noreferrer"
                class="badge badge-pill pill-button mx-1"
              >Baksida</a>
            </div>
          </div>
          <div class="mt-3">
            <img
              class="flag-icon mx-1"
              height="30px"
              width="30px"
              :src="japanIcon"
              @click="setLocale('ja')"
            >
            <img
              class="flag-icon mx-1"
              height="30px"
              width="30px"
              :src="norwayIcon"
              @click="setLocale('no')"
            >
          </div>
        </div>
      </div>
    </div>
    <div style="height: 800px">
    </div>
  </footer>
</template>
<script setup>

import japanIcon from '../assets/japan-icon-border.png';
import norwayIcon from '../assets/norway-icon.png';


</script>
<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'Footer',

  methods: {
    setLocale (locale) {
      this.$i18n.locale = locale
      localStorage.setItem('locale', locale)
    }
  },
});
</script>
