<template>
  <div id="app">

      <div v-if="$ShowPanel" >
          <wrapper />
      </div>
      <div v-else>
          <public />
      </div>
      
  </div>
</template>
 
<script>

import Wrapper from './layout/Wrapper.vue'
import Public from './layout/Public.vue'

export default { 
  name: 'App',
  components: {
    Wrapper,
    Public
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

</style>
