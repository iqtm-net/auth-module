<template>
    <div class="uk-margin">

    <div class="container-fluid">
        <div class="row no-gutter">
            <!-- The image half -->
            <div class="col-md-6 d-none d-md-flex bg-image"></div>


            <!-- The content half -->
            <div class="col-md-6 bg-color">
                <div class="login d-flex align-items-center py-5">

                    <!-- Demo content-->
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-10 col-xl-7 mx-auto">

                                <h4 class="display-4 uk-text-white">مرحبا بك {{Deliver_Data.first_name}} {{Deliver_Data.last_name}}</h4>
                                <p class="mb-4 uk-text-white">يرجى الموافقة على اعطاء الصفحة صلاحية الدخول الى كاميرا لأضهار خانة المسح الضوئي</p>

                                <form v-on:submit.prevent="Searching" class="uk-search uk-search-default uk-width-1-1@m">
                                    <a href="#" class="uk-search-icon-flip" uk-search-icon></a>
                                    <input class="uk-search-input uk-text-center" v-model="Order_id" style="border-radius: 5px; background: white;" type="search" placeholder="ادخل رمز الطلب">
                                </form>

                                <br><br>

                                <form>
                                    <div class="form-group mb-3">
                                        
                                        <div v-show="Show('scanner')">
                                            <StreamBarcodeReader
                                                @decode="onDecode"
                                                @loaded="onLoaded"
                                                class="border border-dark rounded uk-box-shadow-xlarge "
                                            ></StreamBarcodeReader>
                                        </div>

                                        <h3 v-show="Show('loading')" class="linear-wipe uk-text-bold"> قيد انتضار الموافقة </h3>

                                    </div>
                                </form>

                                <table v-show="Show('order_found')"  class="table table-hover uk-text-white">
                                    <thead>
                                        <tr>
                                        <th scope="col">{{scaned_order_details.product_name}}</th>
                                        <th scope="col">الطلب</th>
                                        </tr>
                                        <tr>
                                        <th scope="col">{{scaned_order_details.track_code}}</th>
                                        <th scope="col">رمز الطلب</th>
                                        </tr>
                                        <tr>
                                        <th scope="col">{{status_arrays(scaned_order_details.status)}}</th>
                                        <th scope="col">الحالة</th>
                                        </tr>
                                        <tr v-if="['pending', 'ReturnedToDeliver', 'ReturnedToClient'].includes(scaned_order_details.status)">
                                            <th scope="col" colspan="2">تغيير حالة الطلب</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="['pending', 'ReturnedToDeliver', 'ReturnedToClient'].includes(scaned_order_details.status)">

                                        <tr>
                                        <th scope="row" >واصل</th>
                                        <td><button type="button" @click.prevent="ChangeOrdersStatusWithWarning('delivered')" class="btn btn-secondary">تحديث</button></td>
                                        </tr>

                                        <tr>
                                        <th scope="row">واصل جزئي</th>
                                        <td><button type="button" class="btn btn-secondary" uk-toggle="target: #Delivered">تحديث</button></td>
                                        </tr>
                                        
                                        <tr>
                                        <th scope="row">مؤجل</th>
                                        <td><button type="button" class="btn btn-secondary" uk-toggle="target: #Delayed">تحديث</button></td>
                                        </tr>

                                        <tr>
                                        <th scope="row">راجع</th>
                                        <td><button type="button" class="btn btn-secondary" uk-toggle="target: #P_Refunded">تحديث</button></td>
                                        </tr>
                                        
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>

                    <!-- End -->

                </div>
            </div><!-- End -->

        </div>

        <!--Returned-->
        <div id="P_Refunded" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <div class="form-check uk-text-right">
                    <input class="form-check-input" type="radio" value="هاتف المستلم مغلق" v-model="returned_case">
                    <label class="form-check-label">
                        هاتف المستلم مغلق
                    </label>
                </div>
                <div class="form-check uk-text-right">
                    <input class="form-check-input" type="radio" value="المستلم لا يرد" v-model="returned_case">
                    <label class="form-check-label">
                        المستلم لا يرد 
                    </label>
                </div>
                <div class="form-check uk-text-right">
                    <input class="form-check-input" type="radio" value="الطلب غير مطابق" v-model="returned_case">
                    <label class="form-check-label">
                        الطلب غير مطابق 
                    </label>
                </div>
                <br>
                <div class="mb-3 uk-text-right">
                    <label for="exampleFormControlTextarea1" class="form-label">او اضف سبب اخر</label>
                    <textarea class="form-control" rows="3" @focus="returned_case = ''" v-model="returned_case"></textarea>
                </div>
                <p class="uk-text-center">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">الغاء</button>
                    <button :disabled="!P_Valid" @click.prevent="ChangeOrdersStatus('ReturnedToDeliver')" class="uk-button uk-button-primary" type="button">
                        <i v-if="show == 'loading'" class="fa fa-refresh fa-spin"></i>
                        <span v-else >تأكيد</span>
                    </button>
                </p>
            </div>
        </div>

        <!--Delayed-->
        <div id="Delayed" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <div class="form-check uk-text-right">
                    <input class="form-check-input" type="radio" value="المستلم خارج المنزل" v-model="returned_case">
                    <label class="form-check-label">
                        المستلم خارج المنزل 
                    </label>
                </div>
                <div class="form-check uk-text-right">
                    <input class="form-check-input" type="radio" value="العميل في العمل ولا يستطيع استلام الطلب" v-model="returned_case">
                    <label class="form-check-label">
                        العميل في العمل ولا يستطيع استلام الطلب
                    </label>
                </div>
                <div class="form-check uk-text-right">
                    <input class="form-check-input" type="radio" value="العميل مسافر" v-model="returned_case">
                    <label class="form-check-label">
                       العميل مسافر
                    </label>
                </div>
                <br>
                <div class="mb-3 uk-text-right">
                    <label for="exampleFormControlTextarea1" class="form-label">او اضف سبب اخر</label>
                    <textarea class="form-control" rows="3" @focus="returned_case = ''" v-model="returned_case"></textarea>
                </div>
                <p class="uk-text-center">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">الغاء</button>
                    <button :disabled="!P_Valid" @click.prevent="ChangeOrdersStatus('ReturnedToDeliver'); SetDelaySubStatus()" class="uk-button uk-button-primary" type="button">
                        <i v-if="show == 'loading'" class="fa fa-refresh fa-spin"></i>
                        <span v-else >تأكيد</span>
                    </button>
                </p>
            </div>
        </div>
        
        <!--Delivered-->
        <div id="Delivered" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <div class="uk-width-1-1">
                    <div class="uk-text-right uk-text-cairo">اسم البريد الراجع</div>
                    <input id="form-horizontal-text" v-model="post_name" class="uk-input" style="text-align: center; border-radius: 4px;" type="text">
                </div>
                <br>
                <div class="uk-width-1-1">
                    <div class="uk-text-right uk-text-cairo">سعر البريد الراجع</div>
                    <div class="uk-inline uk-width-1-1">
                        <span class="uk-form-icon">دينار</span>
                        <input id="form-horizontal-text" v-model="post_price" class="uk-input" style="text-align: center; border-radius: 4px;" type="number">
                    </div>
                </div>
                <p class="uk-text-center">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">الغاء</button>
                    <button :disabled="!D_Valid" @click.prevent="AddPartial()" class="uk-button uk-button-primary" type="button">
                        <i v-if="show == 'loading'" class="fa fa-refresh fa-spin"></i>
                        <span v-else >تأكيد</span>
                    </button>
                </p>
            </div>
        </div>

    </div>
    </div>
</template>


<script>

    import { StreamBarcodeReader } from "vue-barcode-reader";

    export default {
        
        components: {
            StreamBarcodeReader
        },

        data(){
          return {
                result_p : 'loading ......',
                Deliver_Data: {},
                scaned_order_details: {},
                loading: false,
                show: 'loading',
                scanner_loaded: false,
                returned_case: '',
                post_name: '',
                Order_id: '',
                search_data: '',
                post_price: '',  
                rules: [
                    value => { return !this.val_errors || 'رمز الطلب غير صحيح'; }
                ],  
          }
        },

        async created(){
            this.Auth();
        },

        methods: {

            Searching(){
                if(this.Order_id !== ''){ 
                   this.onDecode(this.Order_id);
                }
            },
        
            onBarcodeScanned(){
                console.log('asdasd')
            },
            
            ChangeOrdersStatusWithWarning(status){

                this.$confirm(this.status_arrays(status)+` تغيير الحالة الى `).then(() => {

                    this.ChangeOrdersStatus(status)

                });
            },

            ChangeOrdersStatus(status){

                this.show = 'loading';

                let formData = new FormData();

                formData.append('status', status);
                formData.append('id', this.scaned_order_details.id);
                formData.append('case_details', this.returned_case);

                this.axios.post(process.env.VUE_APP_URL+'/api/Deliver/update_track_status', formData)

                .then(res => {
                    this.$toasted.show("تم تحديث حالة الطلب", { type : 'success', theme: "bubble",  position: "bottom-center", duration : 2000 });
                    this.loading = false;
                    this.show = 'scanner';
                    UIkit.modal("#P_Refunded").hide();

                })
                .catch(res => {
                    this.val_errors = res.response.data.error;
                    this.show = 'scanner';
                });
            },

            SetDelaySubStatus(){

                this.miniload = true;
                let formData = new FormData();

                formData.append('order_id', this.scaned_order_details.id);

                this.axios.post(process.env.VUE_APP_URL+'/api/Deliver/Set_Delay_Status', formData)

                .then(res => {
                    this.miniload = false;
                    UIkit.modal("#Delayed").hide();
                })

                .catch(res => {
                    this.val_errors = res.response.data.error;
                    this.miniload = false;
                });

            },

            AddPartial(){
                this.miniload = true;
                let formData = new FormData();

                formData.append('order_id', this.scaned_order_details.id);
                formData.append('track_code', this.scaned_order_details.track_code);
                formData.append('post_name', this.post_name);
                formData.append('post_price', this.post_price);

                this.axios.post(process.env.VUE_APP_URL+'/api/Admin/AddPartialRefund', formData)

                .then(res => {
                    this.miniload = false;
                    this.ChangeOrdersStatus('delivered');
                    UIkit.modal("#Delivered").hide();
                })

                .catch(res => {
                    this.val_errors = res.response.data.error;
                    this.miniload = false;
                });
            },

            Auth(){
                if(!this.$session.has('deliver_data')){
                    this.$router.push({ name: 'deliver_auth'})
                }
                else{
                    this.Deliver_Data = this.$session.get('deliver_data');
                }
            },

            onDecode (result) {
                
                if(!this.loading){
                    this.loading = true;
                    
                    let formData = new FormData();
                    formData.append('tracking_number', result);

                    this.axios.post(process.env.VUE_APP_URL+`/api/track_order`, formData)
                    .then(res => {
                        this.scaned_order_details = res.data.data[0];
                        this.show = 'order_found';
                        this.loading = false;
                    }).catch(res => {
                        this.$fire({
                            title: "رمز الطلب غير صحيح",
                            type: "error",
                            timer: 3000
                        }).then(r => {
                            this.loading = false;
                        });
                    });
                }  
                
            },

            onLoaded () {
                if(this.show !== 'order_found'){this.show = 'scanner'}
            },
            
            Show(ToShow){
                return (this.show == ToShow) ? true : false;
            },

            CheckStatus(status){
                return (this.scaned_order_details.status == status) ? true : false;
            },

            status_arrays(status){
                if(status == 'waiting'){ return 'في الانتضار'; }
                if(status == 'pending'){ return 'في الطريق'; }
                if(status == 'delivered'){ return 'تم التسليم'; }
                if(status == 'ReturnedToDeliver' && this.scaned_order_details.delayed){ return 'مؤجل'; }
                if(status == 'ReturnedToClient' &&  !this.scaned_order_details.delayed){ return 'راجع'; }
            }
        },

        computed:{
            P_Valid(){
                return this.returned_case !== ''
            },
            
            D_Valid(){
                return this.post_name !== '' && this.post_price !== ''
            }
        },

        watch: {
        },

    };
</script>

<style lang="scss" scoped>
/*
*
* ==========================================
* CUSTOM UTIL CLASSES
* ==========================================
*
*/
.login,
.image {
  min-height: 100vh;
}

.bg-image {
  background-image: url('../../assets/hodhod.png');
  background-size: cover;
  background-position: center center;
}

.bg-color{
    background-image: linear-gradient(#fc4236, #fc4236ad 60%, #fc42368c);
}

.btn-bg{
    background: #fc42368c;
}

.linear-wipe {
  text-align: center;
  
  background: linear-gradient(to right, #FFF 20%, rgb(160, 41, 41) 40%, rgb(255, 0, 0) 60%, #FFF 80%);
  background-size: 200% auto;
  
  color: #000;
  background-clip: text;
  text-fill-color: transparent;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  
  animation: shine 1s linear infinite;
  @keyframes shine {
    to {
      background-position: 200% center;
    }
  }
}

</style>