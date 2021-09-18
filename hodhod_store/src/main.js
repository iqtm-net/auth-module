import Vue from 'vue'
import App from './App.vue'
import {routes} from './router'
import Toasted from 'vue-toasted' 
import VueSession from 'vue-session'
import VueRouter from 'vue-router/dist/vue-router.min.js' 
import {VclTable} from 'vue-content-loading'
import vueDebounce from 'vue-debounce'
import VueSimpleAlert from "vue-simple-alert"
import myUpload from 'vue-image-crop-upload'
import axios from 'axios'
import VueAxios from 'vue-axios'

Vue.use(vueDebounce)
Vue.use(Toasted)
Vue.use(VueRouter)
Vue.use(VueSession)
Vue.use(VclTable)
Vue.use(VueAxios, axios)
Vue.component('vcl-table', VclTable)
Vue.use(VueSimpleAlert)
Vue.component('my-upload', myUpload)

Vue.config.productionTip = true;

window.Vue = Vue;

// Vue.prototype.$theme = "asas";


const router = new VueRouter({
  routes,
  mode: 'history'
});

router.beforeEach((to, from, next) => {

  var store_name = window.location.host.split(".");
  axios.get(process.env.VUE_APP_URL+`/api/check_shared_link_code/${store_name[0]}`)
  .then(res => {  
      Vue.prototype.$theme = res.data.data.store_infos.theme;
      next();
  })
  .catch(res => {
      res
      this.$router.push({ path: '/404' })
  });

})

new Vue({
  router,
  render: h => h(App),
}).$mount('#app')
