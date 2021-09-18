<template>

    <div class="uk-margin">
        <nav style="margin: 20px 0px; background:none;" class="uk-navbar-container" uk-navbar>
            <div class="uk-navbar-right">
                <ul class="uk-navbar-nav">
                    <li>
                        <div v-if="searching" uk-spinner style="padding: 4px 6px;"></div>
                    </li>
                    <li>
                        <form class="uk-search uk-search-default">
                            <a href="#" class="uk-search-icon-flip" uk-search-icon></a>
                            <input class="uk-search-input" v-debounce:2000ms="Searching" v-model="search_data" style="border-radius: 5px;background: white;" type="search" placeholder="search">
                        </form> 
                    </li>
                </ul>
            </div>
        </nav>
        <!-- /////////////////////////////////// LISTING ////////////////////////////////////  -->
        <vcl-table v-if="!isLoaded" :rows="9" :columns="5"></vcl-table>

        <div v-else>
            <table border="0" class="table cust-table uk-card uk-card-default"> 
                <thead>
                    <tr>
                        <th style="width:80px;">#</th>
                        <th style="width:300px;" class="text-center">
                            <li class="fa fa-gear"></li>
                        </th>
                        <th style="width:200px;">first name</th>
                        <th style="width:200px;">last name</th>
                        <th style="width:100px;">country</th>
                        <th style="width:100px;">state</th>
                        <th style="width:200px;">phone number</th>
                    </tr>
                </thead> 
                <tbody v-for="(order) in orders" :key="order.id">
                    <tr>
                        <th style="width:80px;">{{ order.id }}</th>
                        <td style="width:300px;" class="text-center">
                            <button class="btn btn-outline-info" type="button" @click.prevent="ViewAccount(order)" uk-toggle="target: #ViewAccount">
                                <span class="fa fa-eye"></span>
                            </button> 
                            <button v-if="$A_Role == 'admins'" class="btn btn-outline-success" type="button" @click.prevent="ViewAccount(order)" uk-toggle="target: #NotifyAccount">
                                <span class="fas fa-comment-dots"></span>
                            </button>
                            <button class="btn btn-outline-warning" type="button" @click.prevent="ViewAccount(order)" uk-toggle="target: #ViewOrders">
                                <span class="fas fa-boxes"></span>
                            </button> 
                            <button class="btn btn-outline-success" type="button" @click.prevent="ViewAccount(order)" uk-toggle="target: #ViewWithdrawHistory">
                                <span class="fas fa-file-invoice-dollar"></span>
                            </button> 
                        </td>
                        <td style="width:200px;">{{ order.first_name }}</td>
                        <td style="width:200px;">{{ order.last_name }}</td>
                        <td style="width:100px;">{{ order.address_country }}</td>
                        <td style="width:100px;">{{ order.address_state }}</td>
                        <td style="width:200px;">{{ order.phone_number }}</td>
                    </tr>
                </tbody>
            </table>
            <br><br>
            <div class="row Departmain">
                <div class="col-sm-6 Previous" align="center">
                    <button v-if="orders.prev_page_url !== null" class="btn btn-secondary tab-nav-btn" type="button" @click.prevent="Previous()">
                        <i _ngcontent-udn-32="" class="fa fa-chevron-left"></i>
                    </button>
                </div>

                <div class="col-sm-6 Next" align="center">
                    <button v-if="orders.next_page_url !== null " _ngcontent-udn-32="" class="btn btn-secondary tab-nav-btn" type="button" @click.prevent="Next()">
                        <i _ngcontent-udn-32="" class="fa fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
        

        <!-- VIEW -->
        <div id="ViewAccount" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <view-account :Account="ViewAccountData" :Account_Role="'users'" :Account_Role_Number="2"></view-account>
            </div>
        </div>
        
        <!-- Notify -->
        <div id="NotifyAccount" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <notify-account :Account="ViewAccountData" :Account_Role="'users'"></notify-account>
            </div>
        </div>
        
        <!-- View Orders -->
        <div id="ViewOrders" uk-modal class="uk-child-width-expand">
            <div class="uk-modal-dialog uk-modal-body">
                <view-orders :Account_Id="ViewAccountData.id" :Account_Role_Number="2"></view-orders>
            </div>
        </div>

        <!-- View Withdraw History -->
        <div id="ViewWithdrawHistory" uk-modal class="uk-child-width-expand">
            <div class="uk-modal-dialog uk-modal-body">
                <view-withdraws :Account_Id="ViewAccountData.id" :Account_Role="'stores'"></view-withdraws>
            </div>
        </div>
        
    </div>
</template>


<script>

    import ViewAccount from '../../components/Dashboard/ViewAndManageAccount.vue'
    import NotifyAccount from '../../components/Dashboard/Members_Notificating.vue'
    import ViewOrders from './AdminShowOrder.vue'
    import ViewWithdrawHistory from './withdraw_orders.vue'

    export default {

        components: {
            'view-account': ViewAccount,
            'notify-account': NotifyAccount,
            'view-orders': ViewOrders,
            'view-withdraws': ViewWithdrawHistory,
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
            ViewAccountData: {
                id:null
            }, 
            search_data: null,
            current_page: 1,
            searching: false,
            disabled: 1,
            val_errors: {},
            isLoaded: false,

          }
        },

        created(){
            this.fetchArticles(this.current_page); 
        },

        watch: {
            search_data(value){
                this.isLoaded = false;
            }
        },

        methods: {

            fetchArticles(Page){
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/modify_account_get/users?page=${Page}`)
                .then(res => {
                    this.isLoaded = true;
                    this.orders = res.data.data;
                    this.current_page = res.data.current_page;
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            }, 

            Next() { this.fetchArticles(this.current_page+1); },
                
            Previous() { this.fetchArticles(this.current_page-1); },

            ViewAccount(Infos){
                this.ViewAccountData = Infos;
                UIkit.modal('#ViewAccount').show();
            },

            Searching(){
                if(this.search_data == ''){ this.fetchArticles(1); }
                else{
                    this.axios.get(process.env.VUE_APP_URL+`/api/Admin/search_for_account/${this.search_data}/users`)
                    .then(res => {
                        if (res.status == 200) {
                            this.orders = res.data.customers;
                            this.isLoaded = true;
                        }
                    }).catch(res => { v.isLoaded = true;});
                }
            },

        },

    };
</script>
