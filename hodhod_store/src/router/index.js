/* eslint-disable */

export const routes = [

    { path: '*', component: () => import('../views/PageNotFound.vue') , name: '404'},
    { path: '/', component: () => import('../views/index.vue') , name: 'index'},
    { path: '/branch/:branch_id', component: () => import('../views/view_branch.vue') , name: 'view_branch'},
    { path: '/buy/:item_id', component: () => import('../views/buy_item.vue') , name: 'buy'},

];
 