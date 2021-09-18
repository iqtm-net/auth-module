<template>
  <aside class="vps-sidebar" :class="{'vps-sidebar-closed':!isOpen}">
    <div class="vps-logo">
      <img src="hodhod.png" width="40" style="margin-right:5px">
      DASHBOARD
    </div>
    <div class="vps-sidebar-user">
      <div class="vps-sidebar-user--details">
        <div class="vps-sidebar-user-avatar">
          <avatar/>
        </div>
        <div class="vps-sidebar-user-name">
          Welcome
        </div>
        <div class="vps-sidebar-user-role">
          <span v-if="$A_Role == 'companies'">{{$Account.name}}</span>
          <span v-else>{{$Account.first_name}} {{$Account.last_name}}</span>
        </div>
        <!-- bind the CSS variable to the user status depending on vuex store state or any other property
              like '--status-color':user.isOnline?'#06ef61':'#fb0508'
        -->
        <div
          class="vps-sidebar-user-status"
          :style="{'--status-color':true?'#06ef61':'#fb0508'}"
        >Online</div>
      </div>
    </div> 

    <div class="vps-sidebar-search">
      <slot name="search"></slot>
    </div>
 
    <ul class="vps-sidebar-menu">
      
      <!-- <li class="vps-sidebar-menu-header">
        <h4>General</h4>
      </li> -->
    
      <li v-for="(item,index) in SingleItems" :key="item.id"  @click="expand(index)" >
        <div>

          <div class="vps-sidebar-menu-item-content" :class="{'vps-sidebar-menu-item-content-expanded':expandedIndex===index}">
            <i :class="item[0].icon" height="16px" width="16px"></i>
            <div class="vps-sidebar-menu-item-content-label">
              <div v-if="item[0].children.length > 0">{{item[0].label}}</div>
              <router-link v-else :to="item[0].to">{{item[0].label}}</router-link>
            </div>
            <icon v-if="item[0].children.length > 0" name="ArrowRight" class="vps-sidebar-arrow" height="12px" width="12px" :class="{'vps-sidebar-rotate-arrow':expandedIndex===index}" />
          </div>

          <ul v-if="item[0].children.length > 0" class="vps-sidebar-sub-menu expand" v-expand="index===expandedIndex">
            <li v-for="(child,i) in item[0].children" :key="i" class="vps-sidebar-sub-menu-item">
              <div class="vps-sidebar-sub-menu-item-label" v-if="!child.permission || child.permission.includes($A_Role)">
                <router-link :to="child.to?child.to:'/coming-soon'">{{child.label}}</router-link>
              </div>
            </li>
          </ul>
        </div>
      </li>

    </ul>

    <slot name="toolbar"></slot>
  </aside>
</template>

<script>
import expand from "../directives/expand";

import Icon from "../components/icons";

import Avatar from "../components/Avatar";
import Badge from "../components/Badge";
import EventBus from "../utils/EventBus.js";

export default {
  name: "side-bar",
  data() {
    return {
      isOpen: true,

      SingleItems: [
        [1, {
          label: "Center",
          icon: "fa fa-th",
          to: "/center",
          children:[],
          permission: ['admins']
        }],
        [16, {
          label: "Accounts",
          icon: "fa fa-user",
          children: [
            {
              label: "Delivers",
              to: "/delivers",
              val: "Accounts_Delivers",
              permission: ['admins','receivers']
            },
            {
              label: "Users",
              to: "/users",
              val: "Accounts_Users",
              permission: ['admins','receivers']
            },
            {
              label: "Stores",
              to: "/stores",
              val: "Accounts_Stores",
              permission: ['admins','receivers']
            },
            {
              label: "Companies",
              to: "/Companies",
              val: "Accounts_Companies",
              permission: ['admins']
            },
            {
              label: "Support",
              to: "/support",
              val: "Accounts_Support",
              permission: ['admins']
            },
            
          ],
          permission: ['admins','receivers']
        }],
        [2, {
          label: "Delivers Tracking",
          icon: "fas fa-truck-moving",
          to: "/Delivers_Tracking",
          children:[],
          permission: ['admins','receivers','companies']
        }],
        [22, {
          label: "Orders",
          icon: "fas fa-boxes",
          to: "/Orders",
          children:[],
          permission: ['admins','receivers','companies']
        }],
        [15, {
          label: "Prices", 
          icon: "fas fa-calendar-plus",
          to: "/AdminPrices",
          children:[],
          permission: ['admins','receivers']
        }],
        [3, {
          label: "Learn With HodHod",
          icon: "fas fa-chalkboard-teacher",
          to: "/Articles",
          children:[],
          permission: ['admins','receivers']
        }],
        [33, {
          label: "Actions History",
          icon: "fas fa-history",
          to: "/Action_History",
          children:[],
          permission: ['admins','receivers']
        }],
        [5, {
          label: "Reports",
          icon: "fa fa-file",
          to: "/reports/1",
          children:[],
          permission: ['admins','receivers']
        }],
        [6, {
          label: "Policies",
          icon: "fas fa-scroll",
          to: "/policies",
          children:[],
          permission: ['admins','receivers']
        }],
        [7, {
          label: "Discounts",
          icon: "fas fa-percent",
          to: "/discounts",
          children:[],
          permission: ['admins','receivers']
        }],
        [12, {
          label: "Withdraw Orders",
          icon: "fas fa-file-invoice-dollar",
          to: "/withdraw_orders",
          children:[],
          permission: ['admins','receivers']
        }],
        [9, {
          label: "Center",
          icon: "fas fa-th",
          to: "/Gd_Panel",
          children:[],
          permission: ['delivers']
        }],
        [10, {
          label: "Delivers",
          icon: "fas fa-users",
          to: "/Gd_delivers",
          children:[],
          permission: ['delivers']
        }],
        [11, {
          label: "Orders",
          icon: "fas fa-boxes",
          to: "/Gd_orders",
          children:[],
          permission: ['delivers']
        }],
        [13, {
          label: "Center",
          icon: "fas fa-th",
          to: "/client/center",
          children:[],
          permission: ['stores', 'users']
        }],
        [14, {
          label: "Orders",
          icon: "fas fa-boxes",
          to: "/client/orders",
          children:[],
          permission: ['stores', 'users']
        }],
        [4, {
          label: "Options",
          icon: "fa fa-gear",
          children: [
            {
              label: "Main",
              to: "/Main",
              permission: ['admins','receivers']
            },
            {
              label: "Governorates",
              to: "/Options/governorates",
              permission: ['admins','receivers']
            },
            {
              label: "Stores specialty",
              to: "/Options/stores_specialties",
              permission: ['admins','receivers']
            },
            {
              label: "Stores themes",
              to: "/Options/stores_themes",
              permission: ['admins','receivers']
            },

            {
              label: "Notifications",
              to: "/Notifications",
              permission: ['admins','receivers']
            },

          ],
          permission: ['admins','receivers']
        }],
      ],

      expandedIndex: -1,
      get_premissions_arr: ['all']
      
    };
  },

  async created(){    
      this.my_premissions(); 
  },

  methods: {
    expand(index) {
      this.expandedIndex = this.expandedIndex === index ? -1 : index;
    },

    my_premissions(){
        var v = this;
        let SingleItemsMap = new Map(this.SingleItems);
        this.SingleItems = []; 
        SingleItemsMap.forEach(function(value, key, map){

            if(v.$A_Role.includes('receivers', 'companies')){
              if(value.permission.includes(v.$A_Role) && v.$Account.premissions.includes(value.label) ){
                v.SingleItems.push([ value ]);
                
              }
            }

            else{
              if(value.permission.includes(v.$A_Role)){
                v.SingleItems.push([ value ]);
              }
            }

        });

        this.Pre_Loaded = true;
    },

    set_map(){
      
    }
  },

  components: {
    Icon,
    Avatar,
    Badge
  },
  directives: {
    expand
  },
  mounted() {
    EventBus.$on("toggle-sidebar", isOpen => {
      this.isOpen = isOpen;
    });
  }
};
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: all 1s;
  max-height: 100%;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  transform: translateY(-100%);

  height: 0;
}

.flip-list-move {
  transition: all 1s;
}
.vps-sidebar-sub-menu{
  margin: 2px !important;
}
</style>
