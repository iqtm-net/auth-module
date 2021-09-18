<template>
    <div style="margin-bottom: 0px" class="uk-margin">
        <!-- /////////////////////////////////// LISTING ////////////////////////////////////  -->
        <table border="0" class="table cust-table uk-card uk-card-default">
            <thead>
                <tr style="">
                    <th style="width:80px;">#</th>
                    <th style="width:200px;" class="text-center">
                        <li class="fa fa-gear"></li>
                    </th>
                    <th style="width:200px;">البريد</th>
                    <th style="width:200px;">اسم المرسل</th>
                    <th style="width:200px;">رقم المرسل</th>
                    <th style="width:200px;">عنوان المرسل</th>
                    <th style="width:200px;">اسم المستلم</th>
                    <th style="width:200px;">رقم المستلم</th>
                    <th style="width:200px;">عنوان المستلم</th>
                    <th style="width:150px;">سعر البريد</th>
                    <th style="width:150px;">رمز البريد</th>
                </tr>
            </thead>
            <tbody v-if="!isLoaded" class="tbdy">
                <tr>
                    <td style="width:80px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:200px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:200px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:200px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:200px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:200px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:200px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:200px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:200px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:150px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:150px;"><i class="fa fa-refresh fa-spin"></i></td>
                </tr>
            </tbody>
            <tbody v-for="(order) in orders.data" :key="order.id">
                <tr>
                    <th style="width:80px;">{{ order.id }}</th>
                    <td style="width:200px;" class="text-center">
                        <router-link :to="`/order_details/${order.id}`">
                            <button class="btn btn-outline-info"><span class="fa fa-eye"></span></button>
                        </router-link>

                    </td>
                    <td style="width:200px;">{{ order.product_name }}</td>
                    <td style="width:200px;">{{ order.sender_full_name }}</td>
                    <td style="width:200px;">{{ order.sender_phone_number }}</td>
                    <td style="width:200px;">{{ order.location_from_region }}</td>
                    <td style="width:200px;">{{ order.receiver_full_name }}</td>
                    <td style="width:200px;">{{ order.reciever_phone_number }}</td>
                    <td style="width:200px;">{{ order.location_to_region }}</td>
                    <td style="width:150px;">{{ order.recieved_price }}</td>
                    <td style="width:150px;">{{ order.track_code }}</td>
                </tr>
            </tbody>

        </table>
        <br><br>

        <!-- /////////////////////////////////// PAGINATION //////////////////////////////////// -->

        <!--<div class="roo row Departmain">
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
        </div>-->
        <br>
    </div>
</template>


<script>
    export default {
        data(){
          return {
            orders: {},
            post: {},
            admin: false,
            receiver: false,
          }
        },

        created(){
            this.fetchArticles();
            if (this.$session.get('table_type') == "admins") { this.admin = true; }
            if (this.$session.get('table_type') == "receivers") { this.receiver = true; }
        },

        methods: {
            fetchArticles(){

                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/Account_Cart/${this.$route.params.Cart_id}`)
                  .then(res => {
                    this.isLoaded = true;
                    this.orders = res.data;
                    //console.log(res.data);
                    //this.current_page = res.data.current_page;
                    //if (res.data.next_page_url !== null) { this.next = true; }
                    //if (res.data.prev_page_url !== null) { this.prev = true; }
                  })
                .catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            //Next() { this.$router.push({path: `/Cart///${this.current_page+1}` }); },

            //Previous() { this.$router.push({path: `/Cart///${this.current_page-1}` }); },
        }

    };
</script>
