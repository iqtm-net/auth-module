<template>


    <div class="uk-margin">
        <!-- /////////////////////////////////// LISTING ////////////////////////////////////  --> 
        <table border="0" class="table cust-table uk-card uk-card-default">
            <thead>
                <tr style="">
                    <th style="width:80px;">#</th>
                    <th style="width:200px;"><i class="fas fa-id-card"></i></th>
                    <th style="width:200px;"><i class="fas fa-phone"></i></th>
                    <th style="width:200px;"><i class="fas fa-truck"></i><i class="fas fa-key"></i></th>
                    <th style="width:400px;"><i class="fas fa-envelope-open-text"></i></th>
                </tr>
            </thead>
            <tbody v-if="!isLoaded" class="tbdy">
                <tr>
                    <td style="width:80px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:200px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:200px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:200px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:400px;"><i class="fa fa-refresh fa-spin"></i></td>
                </tr>
            </tbody>
            <tbody v-for="(order) in orders.data" :key="order.id">
                <tr>
                    <th style="width:80px;">{{ order.id }}</th>
                    <td style="width:200px;">{{ order.name }}</td>
                    <td style="width:200px;">{{ order.phone_number }}</td>
                    <td style="width:200px;">{{ order.track_code }}</td>
                    <td style="width:400px;">{{ order.describtion }}</td>
                </tr>
            </tbody>
        </table>
        <br><br>


        <div class="row Departmain">
            <div class="col-sm-6 Previous" align="center">
                <button v-if="prev" _ngcontent-udn-32="" class="btn btn-secondary tab-nav-btn" type="button" @click.prevent="Previous()">
                    <i _ngcontent-udn-32="" class="fa fa-chevron-left"></i>
                </button>
                <button v-else _ngcontent-udn-32="" class="btn btn-secondary tab-nav-btn disabled-btn" type="button" disabled>
                    <i _ngcontent-udn-32="" class="fa fa-chevron-left"></i>
                </button>
            </div>

            <div class="col-sm-6 Next" align="center">
                <button v-if="next" _ngcontent-udn-32="" class="btn btn-secondary tab-nav-btn" type="button" @click.prevent="Next()">
                    <i _ngcontent-udn-32="" class="fa fa-chevron-right"></i>
                </button>
                <button v-else _ngcontent-udn-32="" class="btn btn-secondary tab-nav-btn disabled-btn" type="button" disabled>
                    <i _ngcontent-udn-32="" class="fa fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        data(){
          return {
            orders: {},
            post: {},
            admin: false,
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
                this.axios.get(process.env.VUE_APP_URL+`/api/Report?page=${this.$route.params.page}`)
                  .then(res => {
                    this.isLoaded = true;
                    console.log(res.data);
                    this.orders = res.data;
                    this.current_page = res.data.current_page;
                    if (res.data.next_page_url !== null) { this.next = true; }
                    if (res.data.prev_page_url !== null) { this.prev = true; }
                  }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },
          Next() { this.$router.push({path: `/reports/${this.current_page+1}` }); },

          Previous() { this.$router.push({path: `/reports/${this.current_page-1}` }); },
        },

        /*mounted() {
            console.log('Component mounted.')
        }*/
    };
</script>