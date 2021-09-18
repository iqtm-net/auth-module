<template>

    <div class="uk-margin">
        <!-- /////////////////////////////////// LISTING ////////////////////////////////////  -->
        <table border="0" class="table cust-table uk-card uk-card-default">
            <thead>
                <tr>
                    <th style="width:80px;">#</th>
                    <th style="width:300px;">State</th>
                    <th style="width:300px;">Available</th>
                </tr>
            </thead>
            <tbody v-if="!isLoaded" class="tbdy">
                <tr>
                    <td style="width:80px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:300px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:300px;"><i class="fa fa-refresh fa-spin"></i></td>
                </tr>
            </tbody>
            <tbody v-for="(order) in orders.data" :key="order.id">
                <tr>
                    <th style="width:80px;">{{ order.id }}</th>
                    <td style="width:300px;">{{ order.state }}</td>
                    <td style="width:300px;">
                        <div v-if="miniload">
                            <span class="fa fa-refresh fa-spin"></span> 
                        </div>
                        <div v-else>
                            <button v-if="order.available == 0" style="padding: 9px;" @click.prevent="createPost(order.id,1)" class="btn btn-outline-success del-icon"> 
                                <span class="fas fa-check-circle"></span> 
                            </button>
                            <button v-else style="padding: 9px;" @click.prevent="createPost(order.id,0)" class="btn btn-outline-danger del-icon"> 
                                <span class="fas fa-times-circle"></span> 
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>


<script>
    export default {
        data(){
          return {
            orders: {},
            post: {},
            admin: false,
            miniload: false,
            receiver: false
          }
        },

        created(){
            this.fetchArticles();
            if (this.$session.get('table_type') == "admins") { this.admin = true; }
            if (this.$session.get('table_type') == "receivers") { this.receiver = true; }

        },

        methods: {
            fetchArticles(){
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/options`) .then(res => { this.isLoaded = true; this.orders = res.data; })
                .catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },
            createPost(post,bool) {
                this.miniload = true;

                let formData = new FormData();
                formData.append('id', post);
                formData.append('bool', bool);

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/options`, formData)
                  .then(res => {

                    let toast = this.$toasted.show("Updated", { type : 'success', theme: "bubble",  position: "bottom-center", duration : 2000 });

                    this.axios.get(process.env.VUE_APP_URL+`/api/Admin/options`) .then(res => { this.isLoaded = true; this.orders = res.data; this.miniload = false; })
                    .catch(err => console.log(err));

                  })
                .catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },
            
        },

        /*mounted() {
            console.log('Component mounted.')
        }*/
    };
</script>