<template>
    <div>
        <div v-for="(order,index) in TopDelivers" :key="order.id" class="uk-card uk-card-default uk-margin-card">
            <div class="uk-card-header" style="padding: 5px 30px;">
                <div class="uk-grid-small uk-flex-middle" uk-grid>
                    <div class="uk-width-auto" style="width: 81px; justify-content: center; display: flex;">
                        <span style="font-size:3.1rem;color: rgb(111, 218, 118);">#{{index+1}}</span>
                    </div>
                    <div class="uk-width-expand">
                        <h3 class="uk-card-title uk-margin-remove-bottom">
                            <router-link style="color: black;" :to="`/viewdelivers/${order.id}`">
                                {{order.Deliver}}
                            </router-link>
                        </h3>
                        <p style="font-size: 17px; color: #2075a7;" class="uk-text-meta uk-margin-remove-top">{{order.Total_Orders}} Orders</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ShowAllTopDelivers",
        data(){
            return {
                messages: [],
                TopDelivers: {},
                post: {},
                admin: false,
                miniload: false,
                receiver: false
            }
        },

        created(){
            this.axios.get(process.env.VUE_APP_URL+`/api/Admin/ShowAllTopDelivers`)
                .then(res => {
                    this.isLoaded = true;
                    this.TopDelivers = res.data;
                }).catch(res => {
                let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
            });
        },

        /*mounted() {
            console.log('Component mounted.')
        }*/
    };
</script>


<style scoped>

</style>
