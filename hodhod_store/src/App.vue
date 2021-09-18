<template>
  <div id="app">
    <router-view/>
  </div>
</template>

<script>


export default {
  name: 'App',
  components: {

  }
}
</script>

<style>

#app {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

.uk-text-black{
  color: black;
}

.uk-text-cario{
  font-family: Cairo !important;
}

p,h1,h2,h3,h4,h5,h6,div,th,td,button,label{
    font-family: Cairo !important;
}

</style>
