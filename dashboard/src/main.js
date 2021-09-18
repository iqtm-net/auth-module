import Vue from 'vue'
import App from './App.vue'
import {routes} from './router'
import Toasted from 'vue-toasted' 
import VueSession from 'vue-session'
import VueRouter from 'vue-router/dist/vue-router.min.js' 
import {VclTable} from 'vue-content-loading'
import vueDebounce from 'vue-debounce'
import EmptyResult from './components/Dashboard/EmptyResult.vue'
import Validator from './components/Dashboard/Validator.vue'
import VueSimpleAlert from "vue-simple-alert"
import Paginator from './components/Dashboard/Paginator.vue'
import myUpload from 'vue-image-crop-upload'
import axios from 'axios'
import VueAxios from 'vue-axios'

import Echo from 'laravel-echo'
window.Pusher = require('pusher-js');
window.Echo = new Echo({
   broadcaster: 'pusher',
   key: "2b072c56376c916028f3",
   cluster: "ap2",
   encrypted: true,
});

Vue.use(vueDebounce)
Vue.use(Toasted)
Vue.use(VueRouter)
Vue.use(VueSession)
Vue.use(VclTable)
Vue.use(VueAxios, axios)
Vue.component('vcl-table', VclTable)
Vue.component('empty-result', EmptyResult)
Vue.component('validator', Validator)
Vue.use(VueSimpleAlert)
Vue.component('paginator', Paginator)
Vue.component('my-upload', myUpload)

Vue.config.productionTip = false;

window.Vue = Vue;

const router = new VueRouter({
  routes,
  mode: 'history'
});

router.beforeEach((to, from, next) => {

  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

  const Allow = to.meta.Allow;

  const Public = to.matched.some(record => record.meta.Public);

  const Logout = to.matched.some(record => record.meta.Logout);

  const Session = router.app.$session;

  const Token = Session.get('token');
  
  const Role = Session.get('table_type');

  Vue.prototype.$A_Role = Session.get('table_type');

  Vue.prototype.$Account = Session.get('account_data');

  Vue.prototype.$ShowPanel = (Public) ? false: true;

  if(Logout) {
      Session.destroy();
      Session.remove("token");
      Session.remove("table_type");
      Session.remove("value");
      Session.remove("account_data");
  }

  // if user not Authenticated
  if (requiresAuth){

    if (!Session.exists() || !Allow.includes(Role)) next({ name: 'login' })
    else {
      axios.defaults.headers.common['Authorization'] = 'Bearer ' + Token;
      next();
    }
    
  }

  else next();
  
});

new Vue({
  router,
  render: h => h(App),
}).$mount('#app')

// axios.get(process.env.VUE_APP_URL+`/api/vue_session_get`)
// .then(res => {
//     console.log(res.data)
//     this.isLoaded = true;
//     // this.orders = res.data;
//     // this.page = res.data.page;
// }).catch(res => {
//     // this.isLoaded = true;
//     // let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
// });