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
import Header from './components/Header'
import Footer from './components/Footer'

export default {
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
  }
}
</script>