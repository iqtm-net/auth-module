<template>

    <div style="margin-bottom: 0px" class="uk-margin">
        <!-- /////////////////////////////////// LISTING ////////////////////////////////////  -->
        <vcl-table class="uk-padding-large" v-if="!isLoaded" :rows="5" :columns="4"></vcl-table>
        <table v-else class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-right uk-table-responsive">
            <thead>
                <tr>
                    <th>#</th>
                    <th class="text-center">
                        <li class="fa fa-gear"></li>
                    </th>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Expire Date</th>
                    <th>Section</th>
                    <th>Available state</th>
                    <th v-if="Permissions_Val('Discounts_add')">
                        <button class="btn btn-outline-info" style="color: #04bacc;" uk-toggle="target: #modal-example">
                            <span class="fas fa-plus-circle"></span>
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody v-for="(order) in orders.data" :key="order.id">
                <tr>
                    <td>{{ order.id }}</td>
                    <td class="text-center">
                        <button @click.prevent="Pin(order.id)" v-if="Permissions_Val('Discounts_pin')" class="uk-button uk-button-default" v-bind:class="{'uk-button-danger': order.pin}">
                            <span class="fas fa-thumbtack"></span>
                        </button>
                        <button @click.prevent="DeleteArticle(order.id)" v-if="Permissions_Val('Discounts_delete')" class="btn btn-outline-danger del-icon">
                            <span class="fa fa-trash-o"></span>
                        </button>
                    </td>
                    <td>{{ order.Code }}</td>
                    <td>{{ order.discount_percent }}%</td>
                    <td>{{ order.Expire }}</td>
                    <td>{{ order.GifttsOrOrders }}</td>
                    <td>{{ order.available_state }}</td>
                    <td v-if="Permissions_Val('Discounts_add')"></td>
                </tr>
            </tbody>
        </table>

        <br><br>

        <div id="modal-example" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <h2 style="text-align: center; height:48px;  border-radius: 4px;" class="uk-modal-title">New Code</h2>

                <form class="uk-form-horizontal uk-margin-larg">
                    <div>
                        <label class="uk-form-label" for="form-horizontal-text" style="text-align: center;">Code</label>
                        <div class="uk-form-controls">
                            <input id="form-horizontal-text" v-model="new_code.Code" class="uk-input" style="text-align: center; border-radius: 4px;" type="text">
                        </div>
                    </div>
                    <br>
                    <div>
                        <label class="uk-form-label" for="form-horizontal-text" style="text-align: center;">Discount %</label>
                        <div class="uk-form-controls">
                            <input id="form-horizontal-text" v-model="new_code.discount_percent" class="uk-input" style="text-align: center; border-radius: 4px;" type="number" placeholder="EX: 20">
                        </div>
                    </div>
                    <br>
                    <div>
                        <label class="uk-form-label" for="form-horizontal-text" style="text-align: center;">Expire</label>
                        <div class="uk-form-controls">
                            <input id="form-horizontal-text" v-model="new_code.Expire" class="uk-input" style="text-align: center; border-radius: 4px;" type="date">
                        </div>
                    </div>
                    <br>
                    <div>
                        <label class="uk-form-label" for="form-horizontal-text" style="text-align: center;">Usages</label>
                        <div class="uk-form-controls">
                            <input id="form-horizontal-text" v-model="new_code.allowd_usages" class="uk-input" style="text-align: center; border-radius: 4px;" type="number">
                        </div>
                    </div>
                    <br>
                    <div align="center">
                        <label class="uk-form-label" for="form-horizontal-text" style="text-align: center;">Gifts</label>
                        <div class="uk-form-controls">
                            <label style=" margin-top: 7px;"><input v-model="new_code.GifttsOrOrders" value="Gifts" type="radio"></label>
                        </div>
                    </div>
                    <br>
                    <div align="center">
                        <label class="uk-form-label" for="form-horizontal-text" style="text-align: center;">Orders</label>
                        <div class="uk-form-controls">
                            <label style=" margin-top: 7px;"><input v-model="new_code.GifttsOrOrders" value="Orders" type="radio"></label>
                        </div>
                    </div>
                    <br>
                </form>
                <hr class="uk-divider-icon">
                <div>  
                    <div align="center">customize to clients</div>
                </div>
                <br>

                <!--Search Clients To Select-->
                <div class="uk-inline uk-width-1-1">
                    <input class="uk-input uk-text-center uk-background-default" type="text" placeholder="search users or stores" v-model="client_keyword" v-debounce:1000ms="Searching_id" style="height: 54px;"> 
                    <div id="dropped_clients" uk-drop="mode: click" class="uk-width-1-1">
                        <div class="uk-card uk-card-default">
                            <div v-for="(account) in searched_accounts" :key="account.id" class="uk-flex-middle searched_clients" @click.prevent="SelectClient(account)">
                                <div class="uk-width-expand uk-grid-match uk-grid-small uk-padding-small" uk-grid>
                                    <div class="uk-width-auto@m uk-text-left">
                                        <div v-if="account.account_type == 2" class="uk-text-muted">مستخدم</div>
                                        <div v-if="account.account_type == 3" class="uk-text-muted">متجر</div>
                                        <div v-if="account.account_type == 3" class="uk-text-muted">{{account.store_name}}</div>
                                    </div>
                                    <div class="uk-width-expand@m uk-text-right ">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">{{account.first_name}}</h3>
                                        <div class="uk-text-right uk-text-meta">{{account.phone_number}}</div>
                                    </div>
                                </div>
                                <hr style="margin:0px">
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <!--View Selected Clients-->
                <div class="uk-card uk-card-default">
                    <div v-for="(account) in SelectedClients" :key="account.id" class="uk-flex-middle">
                        <div class="uk-width-expand uk-grid-match uk-grid-small uk-padding-small" uk-grid>
                            <div class="uk-width-auto@m uk-text-left">
                                <div v-if="account[1].account_type == 2" class="uk-text-muted">مستخدم</div>
                                <div v-if="account[1].account_type == 3" class="uk-text-muted">متجر</div>
                                <div v-if="account[1].account_type == 3" class="uk-text-muted">{{account[1].store_name}}</div>
                            </div>
                            <div class="uk-width-expand@m uk-text-right ">
                                <h3 class="uk-card-title uk-margin-remove-bottom">{{account[1].first_name}}</h3>
                                <div class="uk-text-right uk-text-meta">{{account[1].phone_number}}</div>
                            </div>
                        </div>
                        <hr style="margin:0px"> 
                    </div>
                </div>

                <hr class="uk-divider-icon">
                <div>  
                    <div align="center">Available states</div>
                    <div class="uk-child-width-1-3@m uk-margin" uk-grid uk-scrollspy="cls:  ">
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Erbil"> أربيل </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="AlAnbar"> الأنبار </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Babil"> بابل </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Baghdad"> بغداد </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Basra"> البصرة </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Dahuk"> دهوك </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="AlDiwaniyah"> الديوانية </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Diyala"> ديالي </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Dhi Qar"> ذي قار </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="AsSulaymaniyah"> السليمانية </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Saladin"> صلاح الدّين </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Kirkuk"> كركوك </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Karbala"> كربلاء </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="AlMuthana"> المثنى </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Maysan"> ميسان </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Najaf"> النجف </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Nineveh"> نينوى </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Wasit"> واسط </div>
                        <div><input  type="checkbox" class="uk-checkbox" v-model="new_code.available_state" value="Zakho"> زاخو </div>
                    </div>  
                </div>

                <p class="uk-text-right">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">ألغاء</button>
                    <button :disabled="!IsValid" @click.prevent="AddP()" class="uk-button uk-button-primary" type="button">
                        <i v-if="miniload" class="fa fa-refresh fa-spin"></i>
                        <span v-else >Add</span>
                    </button>
                </p>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        data(){
          return {
            orders: {},
            searched_accounts: {},
            seccus: false,
            post: {},
            new_code: {
                Code: '',
                Expire: '',
                allowd_usages: '',
                discount_percent: '',
                GifttsOrOrders: [],
                available_state: [],
            },
            admin: false,
            receiver: false,
            isHidden: false,
            miniload:false,
            isLoaded_id: false,
            search_data: '', 
            client_keyword: '',
            SelectedIds: [],
            SelectedClients: [],
            isLoaded: false,
            loading: false,
            Counter:0
          }
        },

        created(){
            this.fetchArticles();
        },

        methods: {
            
            SelectClient(account)
            {
                var v = this;

                this.Counter = this.Counter+1;

                //Set To DB Record Map
                let wrongMap = new Map();
                
                wrongMap.set(this.Counter, {
                    id : account.id,
                    type : account.account_type,
                });
                
                wrongMap.forEach(function (value, key, map) {
                    v.SelectedIds.push([key, value]); 
                });

                //Set To View Map
                let SelectedClients = new Map();
                
                SelectedClients.set(this.Counter, account);
                
                SelectedClients.forEach(function (value, key, map) {
                    v.SelectedClients.push([key, value]); 
                });

                UIkit.drop("#dropped_clients").hide(0);

            },

            Searching_id(){
                
                this.isLoaded_id = false;
                
                if(this.client_keyword !== ''){ 
                   this.axios.get(process.env.VUE_APP_URL+`/api/Admin/add_clients_discounts_code/${this.client_keyword}`)
                    .then(res => {
                        this.searched_accounts = res.data;
                        this.isLoaded_id = true;
                    }).catch(res => {
                    }); 
                }
            },

            fetchArticles(){
                this.isLoaded = false;
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/discounts`)
                  .then(res => {
                    console.log(res.data);
                    this.orders = res.data;
                    this.current_page = res.data.current_page;
                    if (res.data.next_page_url !== null) { this.next = true; }
                    if (res.data.prev_page_url !== null) { this.prev = true; }
                    this.isLoaded = true;
                  })
                .catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            DeleteArticle(order_id) {

                this.miniload = true;

                let formData = new FormData();
                formData.append('id', order_id);

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/delete_discounts_code`, formData)
                    .then(res => {

                    if (res.status == 200) {
                        let toast = this.$toasted.show("Deleted", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.miniload = false;
                        this.fetchArticles();
                    }
                    else{
                        let toast = this.$toasted.show("Error", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.miniload = false;
                    }
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },
            
            Pin(order_id) {

                this.miniload = true;

                let formData = new FormData();
                formData.append('id', order_id);

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/pin_discounts_code`, formData)
                    .then(res => {
                        let toast = this.$toasted.show("Deleted", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.miniload = false;
                        this.fetchArticles();
                })
                .catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            AddP(){

                this.miniload = true;
                let formData = new FormData();
                formData.append('Code', this.new_code.Code);
                formData.append('Expire', this.new_code.Expire);
                formData.append('allowd_usages', this.new_code.allowd_usages);
                formData.append('discount_percent', this.new_code.discount_percent);
                formData.append('GifttsOrOrders', this.new_code.GifttsOrOrders);
                formData.append('available_state', this.new_code.available_state);
                (this.SelectedIds.length != 0) ? formData.append('customized_clients',JSON.stringify(this.SelectedIds)) : false;

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/add_discounts_code`, formData)
                    .then(res => {
                    if (res.status == 200) {
                        let toast = this.$toasted.show("Added", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.miniload = false;
                        this.fetchArticles();
                        UIkit.modal('#modal-example').hide();
                    }
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.miniload = false;
                });
            },

            Permissions_Val(rule){
                if(this.$A_Role == 'admins' || this.$Account.premissions.includes(rule)){
                    return true;
                }

                return false;
            },

        }, 

        computed: {
            IsValid() {
                    return this.new_code.Code !== ''
                    && this.new_code.Expire !== ''
                    && this.new_code.allowd_usages !== ''
                    && this.new_code.discount_percent !== ''
                    && this.new_code.available_state !== ''
                    && this.new_code.GifttsOrOrders !== ''
                    && this.new_code.available_state.length !== 0
                    && this.new_code.GifttsOrOrders.length !== 0
            }
        }
    };
</script>

<style scoped>
.searched_clients{
    cursor: pointer;
}
.searched_clients:hover{
    background: #f1f1f1;
}

.uk-table th{
    text-align: center !important;
}
</style>
