<template>

    <div class="uk-margin">
        <div class="uk-padding-small" uk-grid>

            <div class="uk-width-2-3@m">
                <div class="uk-card uk-card-default uk-margin-card uk-padding-small uk-text-right">
                    <div class="uk-child-width-1-2 uk-padding-remove-bottom" uk-grid>
                        <div>
                            <form class="uk-search uk-search-default uk-width-1-1">
                                <a href="#" class="uk-search-icon-flip" uk-search-icon></a>
                                <input class="uk-search-input" v-debounce:2000ms="Searching" v-model="search_data" style="border-radius: 5px; background: white;" type="search" placeholder="رمز الطلب">
                            </form> 
                        </div>
                        <div>
                            <div class="uk-text-large uk-text-right">
                                 سجل الحالات
                            </div>
                        </div>
                    </div>
                    <br>
                    <vcl-table class="uk-padding-large" v-if="!isLoaded" :rows="5" :columns="2"></vcl-table>
                    <table v-else class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-table-responsive">
                        <thead>
                            <tr>
                                <th class="uk-text-right">رمز الطلب</th>
                                <th class="uk-text-right">تاريخ تغيير الحالة</th>
                                <th class="uk-text-right">تاريخ انشاء الطلب</th>
                                <th class="uk-text-right">الحالة</th>
                                <th class="uk-text-right">السعر</th>
                                <th class="uk-text-right">الطلب</th>
                            </tr>
                        </thead>
                        <tbody v-for="(order) in companies.data" :key="order.id">
                            <tr>
                                <td>{{ order.order_data.track_code }}</td>
                                <td>{{ formatDatekyan(order.created_at) }}</td>
                                <td>{{ formatDatekyan(order.order_data.created_at) }}</td>
                                <td>{{ status_arrays(order) }}</td>
                                <td>{{ order.order_data.recieved_price }}</td>
                                <td>{{ order.order_data.product_name }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <empty-result v-show="isLoaded" :Data="companies.data"></empty-result>
                    <paginator class="uk-padding-large" v-on:childToParent="pagination" :data="companies"></paginator>

                </div>
            </div>

            <div class="uk-width-1-3@m">
                <div class="uk-card uk-card-default uk-margin-card uk-padding-small uk-text-right">
                    <div class="uk-text-large uk-text-right">
                         المندوبين
                    </div>
                    <hr>
                    <vcl-table class="uk-padding-large" v-if="orders.data.length <= 0" :rows="5" :columns="1"></vcl-table>
                    <table v-else class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-table-responsive">
                        <thead>
                            <tr>
                                <th class="uk-text-right">العنوان</th>
                                <th class="uk-text-right">الاسم</th>
                            </tr>
                        </thead>
                        <tbody v-for="(order) in orders.data" :key="order.id">
                            <tr @click.prevent="select_deliver(order.id)" :class="{highlight:order.id == selected}" @click="selected = order.id">
                                <td>{{ order.address_region }}</td>
                                <td>{{ order.first_name }} {{ order.last_name }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>


<script>

    import ViewAccount from '../../components/Dashboard/ViewAndManageAccount.vue'

    export default {

        components: {
            'view-account': ViewAccount,
        },

        data(){
          return {
            orders: {},
            admin: false,
            receiver: false,
            miniload: false,
            post: {},
            keyword: null,
            search_result: {},
            ViewAccountData: {},
            search_data: null,
            page: 1,
            searching: false,
            disabled: 1,
            val_errors: {},
            companies:{},
            User_id: "All",
            selected: undefined,
            isLoaded: false,
            Pre_Loaded: false,
            Add: {
                name: '',
                password: '',
                password_confirmation: '',
                phone_number: '',
                label_counter_start: '',
                active: false,
                Premissions: ['Orders_update order status']
            },

          }
        },

        async created(){
            this.fetchArticles(this.page); 
            this.status_tracking_history(); 
        },

        watch: {
            search_data(value){
                this.isLoaded = false;
            }
        },

        methods: {
            
            pagination(page) {
                this.page = page;
                this.status_tracking_history();
            },
            
            select_deliver(User_id){ 
                this.User_id = User_id;
                this.$nextTick(function () {
                    this.status_tracking_history();
                })
                
            },

            status_tracking_history(){
                this.isLoaded = false;
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/status_tracking_history/${this.User_id}/delivers?page=${this.page}`)
                .then(res => {
                    this.isLoaded = true;
                    this.companies = res.data;
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            fetchArticles(){
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/modify_account_get/delivers?page=${this.page}`)
                .then(res => {
                    this.isLoaded = true;
                    this.orders = res.data;
                    this.page = res.data.page;
                }).catch(res => {
                    this.isLoaded = true;
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            }, 

            ViewAccount(Infos){
                this.ViewAccountData = Infos;
                UIkit.modal('#ViewAccount').show();
            },

            formatDatekyan(time) {
                return new Date(time).toLocaleDateString(['en-iq'], {month: '2-digit', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'});
            },

            Searching(){
                this.isLoaded = false;
                if(this.search_data == ''){ this.fetchArticles(); }
                else{
                    this.axios.get(process.env.VUE_APP_URL+`/api/Admin/search_status_history_by_id/${this.search_data}`)
                    .then(res => {
                        this.companies = res;
                        this.isLoaded = true;
                    }).catch(res => { 
                        this.companies.data = {};
                        this.isLoaded = true;
                    });
                }
            },

            status_arrays(order){
                if(order.status == 'waiting'){ return 'في الانتضار'; }
                if(order.status == 'pending'){ return 'في الطريق'; }
                if(order.status == 'delivered'){ return 'تم التسليم'; }
                if(order.status == 'ReturnedToDeliver' && order.order_data.delayed){ return 'مؤجل'; }
                if(order.status == 'ReturnedToDeliver' &&  !order.order_data.delayed){ return 'راجع الى هدهد'; }
            }

        },

        computed:{
            AddVal(){
                return this.Add.name !== ''
                && this.Add.password !== ''
                && this.Add.password_confirmation !== ''
                && this.Add.label_counter_start !== ''
                && this.Add.phone_number !== ''
            }
        }

    };
</script>

<style scoped>

tr.highlight {
    background: #dadada94;
}

.uk-table-hover tbody tr:hover, .uk-table-hover>tr:hover{
    background: #dadada94;
    cursor: pointer;
}
</style>