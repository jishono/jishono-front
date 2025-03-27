<template>
  <div id="app">
    <div class='container-fluid'>
      <Header />
      <keep-alive>
        <router-view />
      </keep-alive>
    </div>
    <Footer />
  </div>
</template>

<script>
import { defineComponent } from 'vue';

import Footer from './components/Footer.vue';
import Header from './components/Header.vue';

export default defineComponent({
  name: 'App',

  components: {
    Header, Footer
  },

  mounted () {
    if (localStorage.getItem('locale') === null) {
      localStorage.setItem('locale', 'ja')
    }
    window.$(document).bind('focus', function (e) {
      e.preventDefault();
    });
  },
});
</script>