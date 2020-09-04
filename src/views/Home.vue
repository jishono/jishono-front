<template>
  <div>
    <Search />
    <Footer />
  </div>
</template>

<script>
import Footer from '../components/Footer'
import Search from '../components/Search'

export default {
  name: 'Home',
  components: {
    Footer, Search
  }
}
</script>