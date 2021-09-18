<template>

    <div class="">
        <div class="uk-padding-small" uk-grid>
            <div class="uk-width-1-1@m">
                <table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-center uk-table-responsive uk-card-default">
                    <thead>
                        <tr>
                            <th class="uk-text-center uk-text-truncate uk-width-auto">الاعدادات</th>
                            <th class="uk-text-center uk-text-truncate uk-width-auto"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select @change="SelectReceivingCompany($event)" class="uk-select" v-model="select_companie">
                                    <option v-for="store in companies" :key="store" v-bind:value="store" >{{store.name}}</option>
                                </select>
                            </td>
                            <td>استلام البريد من قبل</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="uk-card uk-card-default uk-padding uk-width-1-1" align="center">

                <div class="uk-card uk-card-default uk-width-1-1 uk-margin-large-top div001" v-for="(order) in SelectedOrders" v-bind:key="order.id">
                
                    <div class="uk-flex-middle" uk-grid>
                        <div class="uk-width-1-1">
                            <div class="uk-inline">

                                <img src="/keyan.jpg" width="1800" height="1200" alt="">

                                <div class="uk-position-top uk-overlay">
                                    <img :src="order.barcode" width="400" style="height: 100px;" />
                                </div>

                            </div>
                        </div>
                        <div class="uk-width-1-1">
                            <div class="uk-child-width-1-3" uk-grid>
                                <div><h2 class="uk-text-left uk-text-cario uk-text-bold ps-3">رمز الطلب : {{order.track_code}}</h2></div>
                                <div><img :src="order.barcode" /> </div>
                                <div><h2 class="uk-text-right uk-text-cario uk-text-bold pe-2" dir="rtl">التاريخ : {{formatDate(order.created_at)}}</h2></div>
                            </div>
                        </div>
                        <div class="uk-width-1-1">
                            <div class="uk-grid-collapse uk-grid-match" uk-grid>

                                <div class="uk-width-1-5">
                                    <div class="">
                                        <div class="p-3 m-1 bg-dark text-white rounded-3">ملاحضات مندوب الشركة</div>
                                        <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark" style="height: 208px;">.bg-white</div>
                                        <div class="p-3 m-1 p_label text-white rounded-3 ">توقيع الزبون على استلام المنتج كامل وسليم</div>
                                    </div>
                                </div>

                                <div class="uk-width-4-5">
                                    <div class="uk-grid-collapse" uk-grid>
                                        <div class="uk-width-expand">
                                            <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark">{{order.sender_phone_number}}</div>
                                        </div>
                                        <div class="uk-width-auto">
                                            <div class="p-3 m-1 p_label text-white rounded-3 ">رقم المرسل</div>
                                        </div>
                                        <div class="uk-width-expand">
                                            <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark">{{order.sender_full_name}}</div>
                                        </div>
                                        <div class="uk-width-auto">
                                            <div class="p-3 m-1 p_label text-white rounded-3">المحل / المرسل</div>
                                        </div>
                                    </div>

                                    <div class="uk-grid-collapse uk-grid-match" uk-grid>

                                        <div class="uk-width-1-5">
                                            <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark">
                                                {{order.reciever_phone_number}}
                                            </div>
                                        </div>
                                        <div class="uk-width-1-5">
                                            <div class="bg-dark text-white m-1">
                                                <div class="uk-text-large mt-4">رقم</div>
                                                <div class="uk-text-large">المستلم</div>
                                            </div>
                                        </div>
                                        <div class="uk-width-3-5">
                                            <div class="uk-grid-collapse" uk-grid>
                                                <div class="uk-width-expand">
                                                    <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark">{{order.receiver_full_name}}</div>
                                                </div>
                                                <div class="uk-width-auto">
                                                    <div class="p-3 m-1 bg-dark text-white rounded-3 ">اسم المستلم</div>
                                                </div>
                                            </div>

                                            <div class="uk-grid-collapse" uk-grid>
                                                <div class="uk-width-expand">
                                                    <div class="p-3 m-1 text-white rounded-3 border border-2 border-dark">.</div>
                                                </div>
                                                <div class="uk-width-auto">
                                                    <div class="p-3 m-1 p_label text-white rounded-3 ">ملاحضات التسليم</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>

                                    <div class="uk-grid-collapse" uk-grid>
                                        <div class="uk-width-expand">
                                            <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark">{{order.location_to_state}} {{order.location_to_region}}</div>
                                        </div>
                                        <div class="uk-width-auto">
                                            <div class="p-3 m-1 bg-dark text-white rounded-3 ">عنوان المستلم</div>
                                        </div>
                                    </div>

                                    <div class="uk-grid-collapse" uk-grid>
                                        <div class="uk-width-expand">
                                            <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark">{{order.quantity}}</div>
                                        </div>
                                        <div class="uk-width-auto">
                                            <div class="p-3 m-1 bg-dark text-white rounded-3 ">العدد</div>
                                        </div>
                                        <div class="uk-width-expand">
                                            <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark">{{order.product_name}}</div>
                                        </div>
                                        <div class="uk-width-auto">
                                            <div class="p-3 m-1 p_label text-white rounded-3">النوع</div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="uk-grid-collapse" uk-grid>
                                <div class="uk-width-expand">
                                    <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark">{{parseInt(order.recieved_price) + parseInt(order.Deliver_Fee)}} </div>
                                </div>
                                <div class="uk-width-auto">
                                    <div class="p-3 m-1 p_label text-white rounded-3 ">كتابتا</div>
                                </div>
                                <div class="uk-width-expand">
                                    <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark">{{parseInt(order.recieved_price) + parseInt(order.Deliver_Fee)}}</div>
                                </div>
                                <div class="uk-width-auto">
                                    <div class="p-3 m-1 p_label text-white rounded-3">رقما</div>
                                </div>
                                <div class="uk-width-auto">
                                    <div class="p-3 m-1 bg-dark text-white rounded-3">المبلغ الكلي مع التوصيل</div>
                                </div>
                            </div>

                            <div class="uk-width-expand">
                                <div class="p-3 m-1 bg-dark text-white rounded-3 border border-2 border-dark" uk-grid>
                                    <div class="uk-width-1-3"> يسقط حق المطالبة بتفاصيل الوصل (المبلغ او البضاعة) بعد 2 اشهر</div>
                                    <div class="uk-width-1-3">لا يعتمد وصل الحساب بدون ختم الشركة</div>
                                    <div class="uk-width-1-3">الشركة طرف ناقل فقط وغير مسزولة عن جودة او نوع البضاعة</div>
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="uk-card-header" style="border-bottom:none">
                        <div class="uk-flex uk-flex-middle uk-flex-between">
                            <div class="uk-width-auto">
                                <img class="uk-border-circle" width="80" src="hodhod.png">
                            </div>
                            <div class="uk-width-expand">
                                <img :src="order.barcode" />
                            </div>
                            <div class="uk-width-auto">
                                <h3 class="uk-card-title uk-margin-remove-bottom">{{order.track_code}}</h3>
                            </div>
                        </div>
                    </div>

                    <div>

                        <table border="0" class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-center">
                            <thead>
                                <tr>
                                    <th class="uk-text-center"></th>
                                    <th class="uk-text-center"></th>
                                </tr>
                            </thead> 
                            <tbody>
                                <tr>
                                    <td>{{order.product_name}}</td>
                                    <td>البريد</td>
                                </tr>
                                <tr>
                                    <td>{{order.sender_full_name}}</td>
                                    <td>المرسل</td>
                                </tr>
                                <tr>
                                    <td>{{order.sender_phone_number}}</td>
                                    <td>رقم هاتف المرسل</td>
                                </tr>
                                <tr>
                                    <td>{{order.receiver_full_name}}</td>
                                    <td>المستلم</td>
                                </tr>
                                <tr>
                                    <td>{{order.reciever_phone_number}}</td>
                                    <td>رقم هاتف المستلم</td>
                                </tr>
                                <tr>
                                    <td>{{order.location_to_state}} | {{order.location_to_region}}</td>
                                    <td>عنوان المستلم</td>
                                </tr>
                                <tr>
                                    <td>{{parseInt(order.recieved_price) + parseInt(order.Deliver_Fee)}}</td>
                                    <td>سعر البريد</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr>

                </div>
            </div>

        </div>
    </div>

</template>

<script> 

    import expand from "../../directives/expand";
    var numberAbbreviate = require("number-abbreviate");

    export default {
        components: {
        },

        data() {
            return {
                page:1,
                users: {},
                Add:{
                    title:'',
                    msg:'',
                },
                account:{},
                companies:{},
                select_companie: '',
                isLoaded:false,
                miniload: false,
                SelectedOrders: [{"id":24,"user_id":"761","deliver_id":null,"account_type":3,"OrderGroupe_Id":13,"in_cart":0,"sender_full_name":"abbas muhil","sender_phone_number":9647509049155,"location_from_country":"iraq","location_from_state":"Baghdad","location_from_region":"test","location_on_map_from":"33.2677135,44.31188420000001","receiver_full_name":"testy","reciever_phone_number":"9647811664854","location_to_country":"iraq","location_to_state":"Baghdad","location_to_region":"test","location_on_map_to":"33.26801738275297,44.31157719343901","recieved_price":4000,"size":"52","color":null,"quantity":null,"shipping_type":"local","recieve_date":"2021-08-08","sent_to_hodhod":1,"product_id":null,"product_name":"testot","product_image":null,"Deliver_Fee":"5000","App_Fee":750,"debts":null,"Order_Discount":null,"insurance":0,"created_by_shared_link":0,"payment_method":"RECEIVER","rate":null,"status":"waiting","case_details":null,"track_code":"T36036","deliver_track_code":49551524,"created_at":"2021-08-08T23:47:07.000000Z","updated_at":"2021-08-30T04:55:43.000000Z","barcode":"data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAALQAAAAeAQMAAAC\/hKb5AAAABlBMVEX\/\/\/8AAABVwtN+AAAAAXRSTlMAQObYZgAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAClJREFUKJFj+MzDfP7AeZ4\/xp\/\/\/PlvbMPD\/+GMDf+ZDwyj4qPiQ1AcABUXgKwEdCSeAAAAAElFTkSuQmCC"
                }]
                
            };
        },

        created(){
            this.get_users();
            this.get_companies();
            this.current_receiving_company();
        },

        methods: {
            
            current_receiving_company(){
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/current_receiving_company`)
                .then(res => {
                    this.isLoaded = true;
                    this.select_companie = res.data;
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            get_companies(){
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/get_companies`)
                .then(res => {
                    this.isLoaded = true;
                    this.companies = res.data;
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            pagination(page) {
                this.page = page;
                this.get_users();
            },

            get_users(){
                this.isLoaded = false,
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/notifications?page=${this.page}`)
                .then(res => {
                    this.users = res.data;
                    this.isLoaded = true;
                });
            },


            AddProduct(){

                this.miniload = true;

                const config = {
                    headers: { 'content-type': 'multipart/form-data' }
                }

                let formData = new FormData();

                formData.append('member_role', '*');
                formData.append('member_id', '*');
                formData.append('title', this.Add.title);
                formData.append('MSG', this.Add.msg);
            
                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/notify_all`, formData, config)

                .then(res => {

                    this.$toasted.show("Success", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.get_users();
                    this.miniload = false;

                })
                .catch(res => {
                        this.val_errors = res.response.data.error;
                        this.miniload = false;
                });
            },

            SelectReceivingCompany(event){

                this.$confirm(this.select_companie.name+` تغيير شركة الاستلام الى `).then(() => {

                    this.miniload = true;
                    let formData = new FormData();

                    formData.append('id', this.select_companie.id);

                    this.axios.post(process.env.VUE_APP_URL+'/api/Admin/change_receiving_company', formData)

                    .then(res => {
                        this.$toasted.show("Success", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.get_users();
                        this.get_companies();
                        this.current_receiving_company();
                        this.miniload = false;
                    })

                    .catch(res => {
                            this.val_errors = res.response.data.error;
                            this.miniload = false;
                    });

                    this.selected_status = 'default'
                });
                
            },

            formatDate(time) {
                return new Date(time).toLocaleDateString(['en-iq'], {month: '2-digit', day: '2-digit', year: 'numeric',});
            },
        },

        computed: {
            
            edit_val(){
                return this.Add.title !== ''
                && this.Add.msg !== ''
            },

        },

    };
</script>

<style>

.p_label{
    background: #6b3a8c !important;
}
.c_lable{
    justify-content: flex-end;
}
.c_lable:hover{
    padding-right: 10px;
    transition: all 1s;
}
.c_item{
    display: flex;
    justify-content: flex-end !important;
    margin-right: 20px;
    margin: 10px 20px;
}
.c_item:hover{
    padding-right: 10px;
    transition: all 1s;
}
.mini-i{
    font-size: 15px;
    margin: 0px 3px;
} 

.edt_cat_op:hover{
    color:#b8c400 !important;
}

.add_cat_op:hover{
    color:#00ba4b !important;
}

.del_cat_op:hover{
    color:#ff5b5b !important;
}

.uk-position-top{
    left: 355px !important;
    right: initial !important;
}
</style>

