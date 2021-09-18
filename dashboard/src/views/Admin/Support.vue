<template>

    <div class="uk-margin">
        <div class="uk-padding-small" uk-grid>

            <div class="uk-width-2-3@m">
                <div class="uk-card uk-card-default uk-margin-card uk-padding-small uk-text-right">
                    <div class="uk-child-width-1-2 uk-padding-remove-bottom" uk-grid>
                        <div>
                            <form class="uk-search uk-search-default uk-width-1-1">
                                <a href="#" class="uk-search-icon-flip" uk-search-icon></a>
                                <input class="uk-search-input" v-debounce:2000ms="Searching" v-model="search_data" style="border-radius: 5px; background: white;" type="search" placeholder="بحث">
                            </form> 
                        </div>
                        <div>
                            <div class="uk-text-large uk-text-right">
                                المحررين
                            </div>
                        </div>
                    </div>
                    <br>
                    <vcl-table class="uk-padding-large" v-if="!isLoaded" :rows="5" :columns="2"></vcl-table>
                    <table v-else class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-right uk-table-responsive">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    <li class="fa fa-gear"></li>
                                </th>
                                <th>first name</th>
                                <th>last name</th>
                                <th>address</th>
                                <th>phone number</th>
                            </tr>
                        </thead>
                        <tbody v-for="(order) in orders.data" :key="order.id">
                            <tr>
                                <td class="text-center">
                                    <button class="btn btn-outline-info" type="button" @click.prevent="ViewAccount(order)">
                                        <span class="fa fa-eye"></span>
                                    </button>
                                </td>
                                <td>{{ order.first_name }}</td>
                                <td>{{ order.last_name }}</td>
                                <td>{{ order.address }}</td>
                                <td>{{ order.phone_number }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="uk-width-1-3@m">
                <div class="uk-card uk-card-default uk-margin-card uk-padding-small uk-text-right">
                    <div class="uk-text-right"> أضافة محرر </div>
                    <hr>
                    <form class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label" for="form-stacked-text">الاسم الاول</label>
                        <div class="uk-form-controls">
                            <input class="uk-input" id="form-stacked-text" type="text" v-model="Add.first_name">
                            <validator :errors="val_errors" field="first_name" />
                        </div>
                    </div>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label" for="form-stacked-text">الاسم الثاني</label>
                        <div class="uk-form-controls">
                            <input class="uk-input" id="form-stacked-text" type="text" v-model="Add.last_name">
                            <validator :errors="val_errors" field="last_name" />
                        </div>
                    </div>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label" for="form-stacked-text"> العنوان</label>
                        <div class="uk-form-controls">
                            <input class="uk-input" id="form-stacked-text" type="text" v-model="Add.address">
                            <validator :errors="val_errors" field="address" />
                        </div>
                    </div>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label" for="form-stacked-text">رقم الهاتف</label>
                        <div class="uk-form-controls">
                            <input class="uk-input" id="form-stacked-text" type="number" v-model="Add.phone_number">
                            <validator :errors="val_errors" field="phone_number" />
                        </div>
                    </div>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label" for="form-stacked-text">كلمة السر</label>
                        <div class="uk-form-controls">
                            <input class="uk-input" id="form-stacked-text" type="text" v-model="Add.password">
                            <validator :errors="val_errors" field="password" />
                        </div>
                    </div>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label" for="form-stacked-text">تأكيد كلمة السر</label>
                        <div class="uk-form-controls">
                            <input class="uk-input" id="form-stacked-text" type="text" v-model="Add.password_confirmation">
                            <validator :errors="val_errors" field="password_confirmation" />
                        </div>
                    </div>
                    <div class="uk-width-1-2@s uk-text-left">
                        <div class="uk-form-controls">
                            <input class="uk-checkbox" type="checkbox" v-model="Add.active">
                        </div>
                    </div>
                    <div class="uk-width-1-2@s">
                        <div>تفعيل الحساب ؟</div>
                    </div>
                    
                    <div class="uk-width-1-1">
                        <br>
                        <div class="uk-text-right">  الصلاحيات </div>
                        <hr>
                    </div>

                    <!--Support Premissions-->
                    <vcl-table class="uk-padding-large" v-if="!Pre_Loaded" :rows="5" :columns="1"></vcl-table>
                    <div v-else class="uk-width-1-1" v-for="(order) in get_premissions_arr" :key="order.id">

                        <!--main-->
                        <div class="uk-width-1-1 uk-margin-remove-left" uk-grid>
                            <div class="uk-width-auto uk-text-left">
                                <div class="uk-form-controls">
                                    <input class="uk-checkbox" type="checkbox" :value="order[0]" v-model="Premissions">
                                </div>
                            </div>
                            <div class="uk-width-expand uk-text-bold uk-text-cairo"> {{order[0]}} </div>
                        </div>

                        <!--sub-->
                        <div v-if="order[1] !== null">
                            <div class="uk-width-1-1 uk-margin-remove" v-for="(sub_order) in order[1]" :key="sub_order.id" uk-grid>
                                <div class="uk-width-auto uk-text-left">
                                    <div class="uk-form-controls">
                                        <input class="uk-checkbox" type="checkbox" :value="`${order[0]}_${sub_order}`" v-model="Premissions">
                                    </div>
                                </div>
                                <div class="uk-width-expand uk-margin-right"> {{sub_order}}</div>
                            </div>
                        </div>
                        

                    </div>

                    <div class="uk-width-1-1 uk-text-center">
                        <button :disabled="!AddVal || miniload" class="uk-button uk-button-danger" @click.prevent="AddSupport()">تأكيد</button>
                    </div>

                    </form>
                    
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
            current_page: 1,
            searching: false,
            disabled: 1,
            val_errors: {},
            isLoaded: false,
            Pre_Loaded: false,
            Add: {
                
            },
            get_premissions_arr: {},
            Premissions: []

          }
        },

        async created(){
            this.fetchArticles(this.current_page); 
            this.get_premissions(); 
        },

        watch: {
            search_data(value){
                this.isLoaded = false;
            }
        },

        methods: {
            fetchArticles(Page){
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/modify_account_get/receivers?page=${Page}`)
                .then(res => {
                    this.isLoaded = true;
                    this.orders = res.data;
                    this.current_page = res.data.current_page;
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            }, 
            
            get_premissions(){
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/get_premissions`)
                .then(res => {
                    this.get_premissions_arr = res.data;
                    this.Pre_Loaded = true;
                })
                .catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            }, 

            Next() { this.fetchArticles(this.current_page+1); },
                
            Previous() { this.fetchArticles(this.current_page-1); },

            ViewAccount(Infos){
                this.ViewAccountData = Infos;
                UIkit.modal('#ViewAccount').show();
            },

            AddSupport(){

                this.miniload = true;

                let formData = new FormData();
                formData.append('first_name', this.Add.first_name);
                formData.append('last_name', this.Add.last_name);
                formData.append('email', this.Add.email);
                formData.append('phone_number', this.Add.phone_number);
                formData.append('address', this.Add.address);
                formData.append('password', this.Add.password);
                formData.append('password_confirmation', this.Add.password_confirmation);
                formData.append('confirmed', this.Add.active);
                formData.append('premissions', JSON.stringify(this.Premissions));

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/Add_Support`, formData)

                .then(res => {

                    this.$toasted.show("Success", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.miniload = false;
                    this.val_errors = {};
                    this.Add = {};

                })
                .catch(res => {
                    this.val_errors = res.response.data.response;
                    this.miniload = false;
                });
            },

            Searching(){
                this.isLoaded = false;
                if(this.search_data == ''){ this.fetchArticles(1); }
                else{
                    this.axios.get(process.env.VUE_APP_URL+`/api/Admin/search_for_account/${this.search_data}/receivers`)
                    .then(res => {
                        if (res.status == 200) {
                            this.orders = res.data.customers;
                            this.isLoaded = true;
                        }
                    }).catch(res => { v.isLoaded = true;});
                }
            },

        },

        computed:{
            AddVal(){
                return this.Premissions.length > 0
            }
        }

    };
</script>

<style scoped>
.uk-table th{
    text-align: center !important;
}
</style>