<template>
  <div id="app">
    <Search />
    <Footer />
  </div>
</template>

<script>
import Footer from './components/Footer'
import Search from './components/Search'

export default {
  name: 'App',
  components: {
    Footer, Search
  }
}
</script>