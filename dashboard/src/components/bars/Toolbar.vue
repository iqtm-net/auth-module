<template>
  <div class="vps-toolbar">
    <div class="vps-toolbar-notifications" @click="toggleDropdown('notifications')">
      <div class="vps-icon-badge" @click.prevent="hideC()">
        <icon name="bell"></icon>
        <badge v-if="Count.notifications !== 0" color="#ffff11" :text="Count.notifications" :circle="true"/>
      </div>
      <transition name="drop">
        <div
          class="vps-dropdown"
          :class="{'vps-dropdown-top':!isIntop,'vps-dropdown-bottom':isIntop}"
          v-if="toggle.notifications"
        >
          <card>
            <template #header>
              <div class="vps-icon-text">
                <icon name="bell" fill="#222"/>
                <span>Notifications</span>
              </div>
            </template>
            <template #body>
              <list>
                
                <list-item v-for="(order) in events.data" :key="order.id"
                  v-bind:title="order.eventer"
                  :icon-bg-color="getContrastColor('white')"
                  v-bind:content="order.event"
                  v-bind:date="Date_Formating(order.created_at)"
                >
                <icon name="info" fill="#22e" height="24px" width="24px"></icon>
                </list-item>
                 
                 
              </list>
            </template>
            <template #footer>
              <a>See more</a>
            </template>
          </card>
        </div>
      </transition>
    </div>
    <div class="vps-toolbar-messages" @click="toggleDropdown('messages')">
      <div class="vps-icon-badge">
        <icon name="message"></icon>

        <badge color="#11ff11" text="7" :circle="true"/>
      </div>
      <transition name="drop">
        <div
          class="vps-dropdown"
          :class="{'vps-dropdown-top':!isIntop,'vps-dropdown-bottom':isIntop}"
          v-if="toggle.messages"
        >
          <card>
            <template #header>
              <div class="vps-icon-text">
                <icon name="message" fill="#222"/>
                <span>Messages</span>
              </div>
            </template>
            <template #body>
              <list>
                <list-item title="John Don" content="Lorem ipsum dolor sit amet...">
                  <h1>D</h1>
                </list-item>
                <list-item
                  title="John Smith"
                  content="consectetur, adipisicing elit. Facilis, distinctio."
                >
                  <h1>J</h1>
                </list-item>
                <list-item
                  title="Ahmed Naji"
                  content=" Vitae fuga ex dicta nam, molestiae similique."
                >
                  <h1>A</h1>
                </list-item>
              </list>
            </template>
             <template #footer>
              <a>See more</a>
            </template>
          </card>
        </div>
      </transition>
    </div>
    <div class="vps-toolbar-settings" @click="toggleDropdown('settings')">
      <div class="vps-icon-badge">
        <icon name="settings"></icon>
        <badge color="#ff5511" :circle="true"/>
      </div>
      <transition name="drop">
        <div
          class="vps-dropdown"
          :class="{'vps-dropdown-top':!isIntop,'vps-dropdown-bottom':isIntop}"
          v-if="toggle.settings"
        >
          <div class="vps-card">
            <list>
              <list-item title="Profile">
                <icon name="person" fill="#444" height="24px" width="24px"></icon>
              </list-item>
              <list-item title="help">
                <icon name="info" fill="#444" height="24px" width="24px"></icon>
              </list-item>
              <list-item title="Settings">
                <icon name="settings" fill="#444" height="24px" width="24px"></icon>
              </list-item>
            </list>
          </div>
        </div>
      </transition>
    </div>
    <div class="vps-toolbar-logout">
      <a href="/"> <icon name="logout"/> </a>
    </div>


  </div>
</template>

<script>
import Icon from "../icons";

import Badge from "../Badge";
import { List, ListItem } from "../lists";
import Card from "../containers/Card";
import getContrastColor from "../../utils/getContrastColor.js";

export default {
  name: "toolbar",
  props: {
    isIntop: {
      type: Boolean,
      default: false
    }
  },

  data() {
    return {
      toggle: {
        messages: false,
        notifications: false,
        settings: false
      },
      lastToggled: "",
      events:{},
      Count:{},
      PagIsLoaded:false,
    };
  },
  
  created(){
    this.notifications(1);
    this.axios.get(process.env.VUE_APP_URL+`/api/Notifications/counter`).then(res => {
        this.Count = res.data;
    });
  },

  methods: {
    getContrastColor,

    toggleDropdown(name) {
      for (let f in this.toggle) {
        this.toggle[f] = false;
      }

      if (this.lastToggled === name) {
        this.toggle[name] = false;
        this.lastToggled = '';

      } else {
        this.toggle[name] = true;
        this.lastToggled = name;
      }
    },

    
    notifications(Page){
        this.PagIsLoaded = false;
        this.axios.get(process.env.VUE_APP_URL+`/api/Notifications/events?page=${Page}`).then(res => {
            this.PaginationPage = Page;
            this.events = res.data;
        });

        
    },

    playSound (sound) {
      var audio = new Audio(sound);
      audio.play();
    },

    Counter(){
        this.axios.get(process.env.VUE_APP_URL+`/api/Notifications/counter`).then(res => {
          this.Count = res.data; 
        });
    },

    Date_Formating(Date_val){
        return new Date(Date_val).toLocaleDateString(['en-iq'], {month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'});
    },

    hideC(){
        this.Count.notifications = 0;
        let formData = new FormData();
        formData.append('object', 'notifications');
        this.axios.post(process.env.VUE_APP_URL+`/api/Notifications/counter`, formData);
    },

    TurnOffBell() {
        this.TurnBell = false;
    },

    

  },

  mounted(){ 

      Echo.channel(`RTNotify-channel`).listen('.RTNotify-Event', (e) => {

          this.Counter();
          
          //Role Matching
          if(e.message.role.includes(this.$session.get('table_type'))){
            let toast = this.$toasted.show(
              "<a href='/Orders' class='uk-text-white'>"+e.message.event+"</a>", { 
              theme: "toasted-primary", 
              position: "bottom-right", 
              duration : 5000
            });

            this.playSound('http://soundbible.com/mp3/Elevator Ding-SoundBible.com-685385892.mp3');
          }
          
          


      });

      // var vm = this;

      // var pusher = new Pusher("2b072c56376c916028f3", { cluster: 'ap2' });
      // Pusher.logToConsole = true;
      // var channel = pusher.subscribe('RTNotify-channel');

      // channel.bind('RTNotify-Event', function(data) {

      //     //update notifications list
      //     vm.notifications(1);

      //     //update notifications counter
      //     vm.Counter();

      // });

  },

  components: {
    Icon,
    Badge,
    List,
    ListItem,
    Card,
    Icon
  }
};
</script>

<style lang="scss">
.drop-enter-active,
.drop-leave-active {
  transition: all 0.5s ease-out;
  max-height: 400px;
  max-width: 280px;
}

.drop-enter,
.drop-leave-to

/* .drop-leave-active below version 2.1.8 */
 {
  max-height: 0;
  opacity: 0;
  max-width: 0;
  transform: translateY(-200px);

  transform: scaleY(0);
}
</style>
