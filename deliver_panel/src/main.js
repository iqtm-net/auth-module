import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import vuetify from './plugins/vuetify'
import axios from 'axios'
import VueAxios from 'vue-axios'
import vueDebounce from 'vue-debounce'
import StoragePlugin from 'vue-web-storage';  
import Toasted from 'vue-toasted' 

Vue.use(StoragePlugin); 
Vue.use(vueDebounce)
Vue.use(VueAxios, axios)
Vue.use(Toasted)

Vue.config.productionTip = false

window.Vue = Vue;

(Vue.$localStorage.hasKey('token')) ? axios.defaults.headers.common['Authorization'] = 'Bearer ' + Vue.$localStorage.get('token') : false; 

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
