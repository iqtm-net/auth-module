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
                                شركات التوصيل
                            </div>
                        </div>
                    </div>
                    <br>
                    <vcl-table class="uk-padding-large" v-if="!isLoaded" :rows="5" :columns="2"></vcl-table>
                    <table v-else class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-table-responsive">
                        <thead>
                            <tr>
                                <!-- <th class="text-center">
                                    <li class="fa fa-gear"></li>
                                </th> -->
                                <th class="uk-text-right">active</th>
                                <th class="uk-text-right">phone number</th>
                                <th class="uk-text-right">name</th>
                                
                            </tr>
                        </thead>
                        <tbody v-for="(order) in orders.data" :key="order.id">
                            <tr>
                                <!-- <td class="text-center">
                                    <button class="btn btn-outline-info" type="button" @click.prevent="ViewAccount(order)">
                                        <span class="fa fa-eye"></span>
                                    </button>
                                </td> -->
                                <td>
                                    <span v-if="order.confirmed">YES</span>
                                    <span v-else>NO</span>
                                </td>
                                <td>{{ order.phone_number }}</td>
                                <td>{{ order.name }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="uk-width-1-3@m">
                <div class="uk-card uk-card-default uk-margin-card uk-padding-small uk-text-right">
                    <div class="uk-text-right"> أضافة شركة </div>
                    <hr>
                    <form class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label" for="form-stacked-text">اسم الشركة</label>
                        <div class="uk-form-controls">
                            <input class="uk-input" id="form-stacked-text" type="text" v-model="Add.name">
                            <validator :errors="val_errors" field="first_name" />
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
                        <label class="uk-form-label" for="form-stacked-text">رقم الوصل التسلسلي يبدأ من</label>
                        <div class="uk-form-controls">
                            <input class="uk-input" id="form-stacked-text" type="number" v-model="Add.label_counter_start">
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
            companies:{},
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
            this.fetchArticles(this.current_page); 
            this.get_companies(); 
        },

        watch: {
            search_data(value){
                this.isLoaded = false;
            }
        },

        methods: {

            get_companies(Page){
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/get_companies`)
                .then(res => {
                    this.isLoaded = true;
                    this.companies = res.data;
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            fetchArticles(Page){
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/modify_account_get/companies?page=${Page}`)
                .then(res => {
                    this.isLoaded = true;
                    this.orders = res.data;
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

            AddSupport(){

                this.miniload = true;

                let formData = new FormData();
                formData.append('name', this.Add.name);
                formData.append('phone_number', this.Add.phone_number);
                formData.append('password', this.Add.password);
                formData.append('password_confirmation', this.Add.password_confirmation);
                formData.append('label_counter_start', this.Add.label_counter_start);
                formData.append('confirmed', this.Add.active);
                formData.append('premissions', JSON.stringify(this.Add.Premissions));

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/Add_Companie`, formData)

                .then(res => {

                    this.$toasted.show("Success", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.miniload = false;
                    this.fetchArticles(this.current_page); 

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
                    this.axios.get(process.env.VUE_APP_URL+`/api/Admin/search_for_companies/${this.search_data}`)
                    .then(res => {
                        this.orders = res;
                        this.isLoaded = true;
                    }).catch(res => { v.isLoaded = true;});
                }
            },

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

</style>