/* eslint-disable */

export const routes = [

{ path: '*', component: () => import('../views/PageNotFound.vue') , name: '404', meta: { Public: true }},
{ path: '/deliver_auth', component: () => import('../views/public/Deliver_Auth_By_Unique_Code.vue') , name: 'deliver_auth' , meta: { Public: true }},
{ path: '/deliver_panel', component: () => import('../views/public/deliver_panel.vue') , name: 'deliver_panel' , meta: { requiresAuth: true, Allow: ['delivers'], Public: true }},
{ path: '/', component: () => import('../views/Auth/Login.vue') , name: 'login', meta: { Public: true, Logout: true }},

{ path: '/Articles', component: () => import('../views/Admin/Articles.vue') , meta: { requiresAuth: true, Allow: ['admins','receivers'] }},
{ path: '/Articles', component: () => import('../views/Admin/Articles.vue') , meta: { requiresAuth: true, Allow: ['admins','receivers'] }},
{ path: '/Delivers_Tracking', component: () => import('../views/Admin/delivers_tracking.vue') , meta: { requiresAuth: true, Allow: ['admins','receivers','companies'] }},
{ path: '/center', component: () => import('../views/Home.vue') , meta: { requiresAuth: true, Allow: ['admins','receivers']} },
{ path: '/Orders', component: () => import('../views/Admin/AdminShowOrder.vue') , name: 'AdminShowOrder', meta: { requiresAuth: true, Allow: ['admins','receivers','companies'] }},
{ path: '/reports/:page', component: () => import('../views/Admin/reports.vue') , name: 'reports', meta: { requiresAuth: true, Allow: ['admins','receivers'] }},
{ path: '/Options/governorates', component: () => import('../views/Admin/governorates.vue') , name: 'governorates', meta: { requiresAuth: true, Allow: ['admins','receivers']} },
{ path: '/Options/stores_specialties', component: () => import('../views/Admin/stores_specialties.vue') , name: 'specialties', meta: { requiresAuth: true, Allow: ['admins','receivers']} },
{ path: '/Options/stores_themes', component: () => import('../views/Admin/stores_themes.vue') , name: 'specialties', meta: { requiresAuth: true, Allow: ['admins','receivers']} },
{ path: '/policies', component: () => import('../views/Admin/Policies.vue') , name: 'policies', meta: { requiresAuth: true, Allow: ['admins','receivers']} },
{ path: '/discounts', component: () => import('../views/Admin/Discounts.vue') , name: 'discounts', meta: { requiresAuth: true, Allow: ['admins','receivers']} },
{ path: '/order_details/:id', component: () => import('../views/Admin/AdminShowOrderByID.vue') , meta: { requiresAuth: true, Allow: ['admins','receivers','companies'] }},

{ path: '/withdraw_orders', component: () => import('../views/Admin/withdraw_orders.vue') , meta: { requiresAuth: true, Allow: ['admins','receivers']}},
{ path: '/delivers', component: () => import('../views/Admin/delivers.vue') , name: 'delivers' , meta: { requiresAuth: true, Allow: ['admins', 'receivers'] }},
{ path: '/users', component: () => import('../views/Admin/users.vue') , name: 'users' , meta: { requiresAuth: true, Allow: ['admins','receivers'] }},
{ path: '/stores', component: () => import('../views/Admin/stores.vue') , name: 'stores' , meta: { requiresAuth: true, Allow: ['admins','receivers'] }},
{ path: '/support', component: () => import('../views/Admin/Support.vue') , name: 'Support' , meta: { requiresAuth: true, Allow: ['admins','receivers']}},
{ path: '/Companies', component: () => import('../views/Admin/Companies.vue') , name: 'Companies' , meta: { requiresAuth: true, Allow: ['admins','receivers']}},
{ path: '/AddSupport', component: () => import('../views/Admin/AddSupport.vue') , name: 'AddSupport' , meta: { requiresAuth: true, Allow: ['admins','receivers']}}, 
{ path: '/AdminPrices', component: () => import('../views/Admin/AdminPrices.vue') , name: 'AdminPrices' , meta: { requiresAuth: true, Allow: ['admins','receivers']}}, 
{ path: '/Cart/:Cart_id', component: () => import('../views/Admin/Cart.vue')  , meta: { requiresAuth: true, Allow: ['admins','delivers','GD']}},
{ path: '/ViewCart/:type/:user_id', component: () => import('../views/Admin/ViewCart.vue')  , meta: { requiresAuth: true, Allow: ['admins','receivers']}},
{ path: '/Notifications', component: () => import('../views/Admin/Notifications.vue')  , meta: { requiresAuth: true, Allow: ['admins','receivers']}},
{ path: '/Main', component: () => import('../views/Admin/Main.vue')  , meta: { requiresAuth: true, Allow: ['admins','receivers']}},
{ path: '/Action_History', component: () => import('../views/Admin/Action_History.vue')  , meta: { requiresAuth: true, Allow: ['admins','receivers']}},
{ path: '/test', component: () => import('../views/Admin/test.vue')  , meta: { requiresAuth: true, Allow: ['admins','receivers']}},

];
