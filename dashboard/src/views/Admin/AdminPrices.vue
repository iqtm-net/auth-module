<template>
    <div style="margin-bottom: 0px" class="uk-margin">
        <!-- /////////////////////////////////// LISTING ////////////////////////////////////  -->
        <table border="0" class="table cust-table uk-card uk-card-default">
            <thead>
                <tr>
                    <th style="width:80px;">#</th>
                    <th style="width:200px;" v-if="Permissions_Val('Prices_delete')" class="text-center">
                        <li class="fa fa-gear"></li>
                    </th>
                    <th style="width:200px;">type</th>
                    <th style="width:200px;">distance from</th>
                    <th style="width:200px;">distance to</th>
                    <th style="width:200px;">Deliver Fee</th>
                    <th style="width:200px;">App Fee</th>
                    <th style="width:100px;" v-if="Permissions_Val('Prices_add')"> 
                        <button class="btn btn-outline-info" style="color: #04bacc;" @click.prevent="Addprice('Global')" uk-toggle="target: #AddPrice">
                            <span class="fas fa-plus-circle"></span>
                        </button>
                    </th>
                </tr>
            </thead> 
            <tbody v-for="(order) in orders.data" :key="order.id">
                <tr :id="`${order.id}`">
                    <th style="width:80px;">{{ order.id }}</th>
                    <td style="width:200px;" v-if="Permissions_Val('Prices_delete')" class="text-center">
                        <button :onclick="`$('#${order.id}').hide(1000);`" @click.prevent="deleteprice(order)" class="btn btn-outline-danger del-icon">
                            <span class="fa fa-trash-o"></span>
                        </button>
                    </td>
                    <td style="width:200px;">{{ order.type }}</td>
                    <td style="width:200px;">{{ order.distance_range_from }}</td>
                    <td style="width:200px;">{{ order.distance_range_to }}</td>
                    <td style="width:200px;">{{ order.Deliver_Fee }}</td>
                    <td style="width:200px;">{{ order.App_Fee }}</td>
                    <td style="width:100px;" v-if="Permissions_Val('Prices_add')"></td>
                </tr>
            </tbody>
        </table>

        <br><br>

        <table border="0" class="table cust-table uk-card uk-card-default">
            <thead>
                <tr>
                    <th style="width:80px;">#</th>
                    <th style="width:200px;" v-if="Permissions_Val('Prices_delete')" class="text-center">
                        <li class="fa fa-gear"></li>
                    </th>
                    <th style="width:200px;">distance from</th>
                    <th style="width:200px;">distance to</th>
                    <th style="width:200px;">Deliver Fee</th>
                    <th style="width:200px;">App Fee</th>
                    <th style="width:100px;" v-if="Permissions_Val('Prices_add')">
                        <button class="btn btn-outline-info"  style="color: #04bacc;" @click.prevent="Addprice('Global')" uk-toggle="target: #AddLocalPrice">
                            <span class="fas fa-plus-circle"></span>
                        </button>
                    </th>
                </tr>
            </thead> 
            <tbody v-for="(order) in orders2.data" :key="order.id">
                <tr :id="`${order.id}`">
                    <th style="width:80px;">{{ order.id }}</th>
                    <td style="width:200px;" v-if="Permissions_Val('Prices_delete')" class="text-center">
                        <button :onclick="`$('#${order.id}').hide(1000);`"  @click.prevent="deletepricelocal(order)" class="btn btn-outline-danger del-icon">
                            <span class="fa fa-trash-o"></span>
                        </button>
                    </td>
                    <td style="width:200px;">{{ order.distance_range_from }}</td>
                    <td style="width:200px;">{{ order.distance_range_to }}</td>
                    <td style="width:200px;">{{ order.Deliver_Fee }}</td>
                    <td style="width:200px;">{{ order.App_Fee }}</td>
                    <td style="width:100px;" v-if="Permissions_Val('Prices_add')"></td>
                </tr>
            </tbody>
        </table>

        <!--AddPrice-->
        <div id="AddPrice" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <div class="uk-grid-match" uk-grid>

                    <div class="uk-width-1-1@s">
                        <div class="uk-text-right uk-text-cairo"> نوع المستخدم </div>
                        <div>
                            <select class="uk-select uk-text-cairo" style=" direction: rtl; " id="form-horizontal-select" v-model="AddGlobalPrice.type">
                                <option value="store">متجر</option>
                                <option value="user">مستخدم</option>
                            </select>
                        </div>
                        <div v-if="val_errors" style="color:red">
                            <font v-for="error in val_errors.title" :key="error.id"> ({{error}}) </font>
                        </div>
                    </div>

                    <div class="uk-width-1-1@s">
                        <div class="uk-text-right uk-text-cairo">المسافة من</div>
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon">KM</span>
                            <input id="form-horizontal-text" v-model="AddGlobalPrice.distance_range_from" class="uk-input" style="text-align: center; border-radius: 4px;" type="number" maxlength="10">
                            <validator :errors="val_errors" field="distance_range_from" />
                        </div>
                    </div>
                    
                    <div class="uk-width-1-1@s">
                        <div class="uk-text-right uk-text-cairo"> المسافة الى </div>
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon">KM</span>
                            <input id="form-horizontal-text" v-model="AddGlobalPrice.distance_range_to" class="uk-input" style="text-align: center; border-radius: 4px;" type="number" maxlength="10">
                            <validator :errors="val_errors" field="distance_range_to" />
                        </div>
                    </div>

                    <div class="uk-width-1-1@s">
                        <div class="uk-text-right uk-text-cairo">  عمولة المندوب </div>
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon">IQD</span>
                            <input id="form-horizontal-text" v-model="AddGlobalPrice.Deliver_Fee" class="uk-input" style="text-align: center; border-radius: 4px;" type="number" maxlength="10">
                            <validator :errors="val_errors" field="Deliver_Fee" />
                        </div>
                    </div>

                    <div class="uk-width-1-1@s">
                        <div class="uk-text-right uk-text-cairo"> عمولة التطبيق </div>
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon">IQD</span>
                            <input id="form-horizontal-text" v-model="AddGlobalPrice.App_Fee" class="uk-input" style="text-align: center; border-radius: 4px;" type="number" maxlength="10">
                            <validator :errors="val_errors" field="App_Fee" />
                        </div>
                    </div>

                </div>

                <p class="uk-text-center">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">الغاء</button>
                    <button :disabled="!AddGlobalIsValid" @click.prevent="AddGlobalPrice_M()" class="uk-button uk-button-primary" type="button">
                        <i v-if="miniload" class="fa fa-refresh fa-spin"></i>
                        <span v-else >اضافة</span>
                    </button>
                </p>
            </div>
        </div>

        <!--Update-->
        <div id="AddLocalPrice" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <div class="uk-grid-match" uk-grid>

                    <div class="uk-width-1-1@s">
                        <div class="uk-text-right uk-text-cairo">المسافة من</div>
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon">KM</span>
                            <input id="form-horizontal-text" v-model="AddLocalPrice.distance_range_from" class="uk-input" style="text-align: center; border-radius: 4px;" type="number" maxlength="10">
                            <validator :errors="val_errors" field="distance_range_from" />
                        </div>
                    </div>
                    
                    <div class="uk-width-1-1@s">
                        <div class="uk-text-right uk-text-cairo">المسافة الى</div>
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon">KM</span>
                            <input id="form-horizontal-text" v-model="AddLocalPrice.distance_range_to" class="uk-input" style="text-align: center; border-radius: 4px;" type="number" maxlength="10">
                            <validator :errors="val_errors" field="distance_range_to" />
                        </div>
                    </div>

                    <div class="uk-width-1-1@s">
                        <div class="uk-text-right uk-text-cairo">عمولة المندوب</div>
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon">IQD</span>
                            <input id="form-horizontal-text" v-model="AddLocalPrice.Deliver_Fee" class="uk-input" style="text-align: center; border-radius: 4px;" type="number" maxlength="10">
                            <validator :errors="val_errors" field="Deliver_Fee" />
                        </div>
                    </div>

                    <div class="uk-width-1-1@s">
                        <div class="uk-text-right uk-text-cairo">عمولة التطبيق</div>
                        <div class="uk-inline uk-width-1-1">
                            <span class="uk-form-icon">IQD</span>
                            <input id="form-horizontal-text" v-model="AddLocalPrice.App_Fee" class="uk-input" style="text-align: center; border-radius: 4px;" type="number" maxlength="10">
                            <validator :errors="val_errors" field="App_Fee" />
                        </div>
                    </div>

                </div>

                <p class="uk-text-center">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">الغاء</button>
                    <button :disabled="!AddLocalIsValid" @click.prevent="AddLocalPrice_M()" class="uk-button uk-button-primary" type="button">
                        <i v-if="miniload" class="fa fa-refresh fa-spin"></i>
                        <span v-else >اضافة</span>
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

            AddGlobalPrice : {
                type: 'store',
                distance_range_from: '',
                distance_range_to: '',
                Deliver_Fee:'',
                App_Fee:''
            },
            
            AddLocalPrice : {
                distance_range_from: '',
                distance_range_to: '',
                Deliver_Fee:'',
                App_Fee:''
            },

            orders: {},

            orders2: {},

            seccus: false,

            post: {},

            admin: false,

            receiver: false,

            isHidden: false,

            miniload:false,

            loading: false

          }
        },

        created(){
            this.fetchArticles();
            if ($cookies.get('table_type') == "admins") { this.admin = true; }
            if ($cookies.get('table_type') == "receivers") { this.receiver = true; }
        },

        methods: {

            fetchArticles(){
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/price?page=${this.$route.params.page}`)
                  .then(res => {
                    this.orders = res.data.by_type;
                    this.orders2 = res.data.by_local;
                    this.current_page = res.data.current_page;
                    if (res.data.next_page_url !== null) { this.next = true; }
                    if (res.data.prev_page_url !== null) { this.prev = true; }
                    this.isLoaded = true;
                  })
                .catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            EditGlobalPrice(data){
                this.AddGlobalPrice = data;
            },

            deleteprice(order_id) {

                this.miniload = true;

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/deleteprice`, order_id)
                .then(res => {
                    this.fetchArticles();
                    let toast = this.$toasted.show("Deleted", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.miniload = false;
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });

            },

            deletepricelocal(order_id) {

                this.miniload = true;

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/deleteprice_local`, order_id)
                .then(res => {
                    this.fetchArticles();
                    let toast = this.$toasted.show("Deleted", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.miniload = false;
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });

            },

            AddGlobalPrice_M() {
                this.miniload = true;
                this.val_errors = false;
                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/Add_price`, this.AddGlobalPrice)
                .then(res => {
                    this.fetchArticles();
                    let toast = this.$toasted.show("Added", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.miniload = false;
                })
                .catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            AddLocalPrice_M(){
                this.miniload = true;
                this.val_errors = false;
                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/Add_price_local`, this.AddLocalPrice)
                .then(res => {
                    this.fetchArticles();
                    let toast = this.$toasted.show("Added", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.miniload = false;
                })
                .catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            Next() { this.$router.push({path: `/AdminPrices/${this.current_page+1}` }); },

            Previous() { this.$router.push({path: `/AdminPrices/${this.current_page-1}` }); },

            Permissions_Val(rule){
                if(this.$A_Role == 'admins' || this.$Account.premissions.includes(rule)){
                    return true;
                }

                return false;
            },

        },

        computed: {
            AddGlobalIsValid(){
                return this.AddGlobalPrice.distance_range_from !== ''
                && this.AddGlobalPrice.distance_range_to !== ''
                && this.AddGlobalPrice.Deliver_Fee !== ''
                && this.AddGlobalPrice.App_Fee !== ''
            },

            AddLocalIsValid(){
                return this.AddLocalPrice.distance_range_from !== ''
                && this.AddLocalPrice.distance_range_to !== ''
                && this.AddLocalPrice.Deliver_Fee !== ''
                && this.AddLocalPrice.App_Fee !== ''
            }
        }
    };
</script>
