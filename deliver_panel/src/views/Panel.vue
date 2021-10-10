<template>
    <div>
      <v-app-bar
      app
      color="red"
      dark
    >
      <div class="d-flex align-center">
        <v-icon>far fa-user</v-icon>
        <span class="text-large mr-2 Cairo">{{Deliver_Data.first_name}} {{Deliver_Data.last_name}}</span>
      </div>

      <v-spacer></v-spacer>

      <v-btn icon @click="ChangeStep(1)"><v-icon>fas fa-home</v-icon></v-btn>

      <v-img
          alt="HodHod Logo"
          class="shrink mr-2"
          contain
          src="../assets/hodhod_white.png"
          transition="scale-transition"
          width="40"
        />
        
    </v-app-bar>

    <v-container v-show="!scanner_loaded">
      <v-alert border="left" close-text="Close Alert" color="orange accent-4" dark dismissible class="Cairo" >
        <v-icon class="px-1">fas fa-camera</v-icon> 
        يرجى الموافقة على اعطاء الصفحة صلاحية الدخول الى كاميرا الهاتف اعلى الصفحة لأضهار خانة المسح الضوئي
      </v-alert>
    </v-container>

    <v-container>
      <v-alert v-model="alert" border="left" close-text="Close Alert" color="green accent-4" dark dismissible class="Cairo">
        <v-icon class="px-1">fas fa-check</v-icon> 
        تم تحديث حالة البريد {{this.scaned_order_details.track_code}}
      </v-alert>
    </v-container>

    <v-container>

      <v-window v-model="step">

        <v-window-item :value="1">
          <v-layout align-center justify-center>
              <v-flex class="login-form text-xs-center">

                  <v-alert v-show="val_errors" border="right" type="error" class="Cairo">رمز الطلب غير صحيح</v-alert>

                  <v-card class="mt-5" light :loading="checking">
                      <template slot="progress">
                        <v-progress-linear color="grey" height="6" indeterminate ></v-progress-linear>
                      </template>

                      <v-card-text>
                          <div class="text-center Cairo">
                            ادخل رمز الطلب للمتابعة او قم بمسح الباركود المرفق مع البريد
                          </div>
                          <v-form>
                              <v-text-field class="centered-input Cairo text-center" v-model="Order_id" light prepend-icon label="رمز الطلب"></v-text-field>
                          </v-form>
                      </v-card-text>

                      <v-card-text>
                          <StreamBarcodeReader
                            @decode="onDecode"
                            @loaded="onLoaded"
                            class="border border-dark rounded uk-box-shadow-xlarge "
                        ></StreamBarcodeReader>
                      </v-card-text>
                  </v-card>
              </v-flex>
          </v-layout>
        </v-window-item>
        
        <v-window-item :value="2">
          
          <v-card class="mx-auto" max-width="344" outlined>
            <v-list-item >
              
              <v-list-item-content>
                <v-list-item-title class="text-h5 mb-1 Cairo"> {{scaned_order_details.product_name}} </v-list-item-title>
                <v-list-item-subtitle class="mb-1 Cairo">{{scaned_order_details.track_code}}</v-list-item-subtitle>
                <v-list-item-subtitle><v-alert color="blue" dense text class="Cairo">{{status_arrays(scaned_order_details.status)}}</v-alert></v-list-item-subtitle>
              </v-list-item-content>

              <v-icon class="ps-4" size="80" tile>fas fa-box</v-icon>

            </v-list-item>
          </v-card>

          <br>

          <v-card class="mx-auto px-3 py-3" max-width="344" outlined :loading="checking">

            <template slot="progress">
              <v-progress-linear color="grey" height="6" indeterminate ></v-progress-linear>
            </template>

            <v-list-item-title class="text-h5 mb-1 Cairo">تحديث حالة الطلب</v-list-item-title>
            <br>
            <v-timeline dense>
              <v-timeline-item color="green lighten-1" fill-dot icon="fas fa-check">
                <v-card outlined>
                  <v-btn block class="Cairo text-h6" :disabled="!allowButton('delivered')" elevation="3" x-large @click="ChangeOrdersStatus('delivered')">واصل</v-btn>
                </v-card>
              </v-timeline-item>
              
              <v-timeline-item color="green lighten-1" fill-dot icon="fas fa-check">
                <v-card outlined>
                  <v-btn block class="Cairo text-h6" :disabled="!allowButton('delivered')" elevation="3" x-large @click="ChangeStep(3)">واصل جزئي</v-btn>
                </v-card>
              </v-timeline-item>

              <v-timeline-item color="orange lighten-1" fill-dot icon="fas fa-history">
                <v-card outlined>
                  <v-btn block class="Cairo text-h6" :disabled="!allowButton('ReturnedToDeliver') && this.scaned_order_details.delayed == 1" elevation="3" x-large @click="ChangeStep(4)">مؤجل</v-btn>
                </v-card>
              </v-timeline-item>

              <v-timeline-item color="red lighten-1" fill-dot icon="fas fa-times">
                <v-card outlined>
                  <v-btn block class="Cairo text-h6" :disabled="!allowButton('ReturnedToDeliver') && this.scaned_order_details.delayed == 0" elevation="3" x-large @click="ChangeStep(5)">راجع</v-btn>
                </v-card>
              </v-timeline-item>
            </v-timeline>
          </v-card>

        </v-window-item>

        <v-window-item :value="3">

          <v-card class="mx-auto" max-width="344" outlined :loading="checking">

            <template slot="progress">
              <v-progress-linear color="grey" height="6" indeterminate ></v-progress-linear>
            </template>

            <v-form v-model="valid" ref="add_partial_form" lazy-validation>
              <v-container>
                <v-text-field class="centered-input Cairo text-center" :rules="rules.postName" v-model="post_name" light prepend-icon label="اسم البريد الراجع"></v-text-field>
                <v-text-field class="centered-input Cairo text-center" type="number" :rules="rules.postPrice" v-model="post_price" light prepend-icon label="سعر البريد الراجع"></v-text-field>
                <br>
                <v-btn block class="Cairo text-h6" color="error" elevation="3" :disabled="!valid" @click="AddPartial">تأكيد</v-btn>
              </v-container>
            </v-form>

          </v-card>

        </v-window-item>

        <v-window-item :value="4">

          <v-card class="mx-auto" max-width="344" outlined :loading="checking">

            <template slot="progress">
              <v-progress-linear color="grey" height="6" indeterminate ></v-progress-linear>
            </template>
            <v-list-item-title class="text-h5 px-3 py-3 Cairo">اضف سبب التأجيل</v-list-item-title>
            <v-form v-model="valid" ref="add_delay_form" lazy-validation>
              <v-container>
                <v-radio-group v-model="returned_case" returned_case >
                  <v-radio label="هاتف المستلم مغلق" value="هاتف المستلم مغلق" ></v-radio>
                  <v-radio label="المستلم لا يرد" value="المستلم لا يرد" ></v-radio>
                  <v-radio label="الطلب غير مطابق" value="الطلب غير مطابق" ></v-radio>
                </v-radio-group>
                <br>
                <v-textarea :rules="rules.refundReason" outlined label="او اضف سبب اخر" @focus="returned_case = ''" v-model="returned_case"
                ></v-textarea>
                <br>
                <v-btn block class="Cairo text-h6" color="error" elevation="3" :disabled="!valid" @click="SetDelaySubStatus()">تأكيد</v-btn>
              </v-container>
            </v-form>

          </v-card>

        </v-window-item>

        <v-window-item :value="5">

          <v-card class="mx-auto" max-width="344" outlined :loading="checking">

            <template slot="progress">
              <v-progress-linear color="grey" height="6" indeterminate ></v-progress-linear>
            </template>
            <v-list-item-title class="text-h5 px-3 py-3 Cairo">اضف سبب الراجع</v-list-item-title>
            <v-form v-model="valid" ref="add_delay_form" lazy-validation>
              <v-container>
                <v-radio-group v-model="returned_case" returned_case >
                  <v-radio label="المستلم خارج المنزل" value="المستلم خارج المنزل" ></v-radio>
                  <v-radio label="العميل مسافر" value="العميل مسافر" ></v-radio>
                  <v-radio label="العميل في العمل ولا يستطيع استلام الطلب" value="العميل في العمل ولا يستطيع استلام الطلب" ></v-radio>
                </v-radio-group>
                <br>
                <v-textarea :rules="rules.refundReason" outlined label="او اضف سبب اخر" @focus="returned_case = ''" v-model="returned_case"
                ></v-textarea>
                <br>
                <v-btn block class="Cairo text-h6" color="error" elevation="3" :disabled="!valid" @click="Refunded()">تأكيد</v-btn>
              </v-container>
            </v-form>

          </v-card>

        </v-window-item>

      </v-window>
        
    </v-container>
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
              step: 1,
              alert: false,
              val_errors: false,
              checking:false,
              result_p : 'loading ......',
              Deliver_Data: {},
              scaned_order_details: {},
              loading: false,
              show: 'loading',
              scanner_loaded: false,
              returned_case: '',
              post_name: '',
              post_price: '',
              Order_id: '',
              search_data: '',

              valid: false,
              rules:{
                postName: [
                  v => !!v || 'الحقل مطلوب',
                ],
                postPrice: [
                  v => !!v || 'الحقل مطلوب',
                  v => (v && this.post_price < this.scaned_order_details.recieved_price) || 
                  'كلفة البريد الراجع يجب ان يكون اقل من الكلفة الكلية للبريد ' + this.scaned_order_details.recieved_price+ ' IQD ',
                ],
                refundReason: [
                  v => !!v || 'الحقل مطلوب',
                ],
              }
          }
        },

        async created(){
            this.Auth();
            this.scanner_loaded = false;
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

                this.checking = true;
                let formData = new FormData();

                formData.append('status', status);
                formData.append('id', this.scaned_order_details.id);
                formData.append('case_details', this.returned_case);

                this.axios.post(process.env.VUE_APP_URL+'/api/Deliver/update_track_status', formData)

                .then(res => {
                    this.checking = false;
                    this.alert = true;
                    setTimeout(()=>{ this.alert = false },2000)
                    this.ChangeStep(1);


                })
                .catch(res => {
                    this.val_errors = res.response.data.error;
                    this.show = 'scanner';
                    this.checking = false;
                });
            },

            SetDelaySubStatus(){
                if(this.$refs.add_delay_form.validate()){
                  
                  let formData = new FormData();
                  formData.append('order_id', this.scaned_order_details.id);
                  formData.append('delayed', 'true');

                  this.axios.post(process.env.VUE_APP_URL+'/api/Deliver/Set_Delay_Status', formData)
                  .then(res => {
                      this.ChangeOrdersStatus('ReturnedToDeliver');
                  })
                  .catch(res => {
                  });

                }
            },

            AddPartial(){

              if(this.$refs.add_partial_form.validate()){

                this.checking = true;
                let formData = new FormData();

                formData.append('order_id', this.scaned_order_details.id);
                formData.append('track_code', this.scaned_order_details.track_code);
                formData.append('post_name', this.post_name);
                formData.append('post_price', this.post_price);

                this.axios.post(process.env.VUE_APP_URL+'/api/Admin/AddPartialRefund', formData)

                .then(res => {
                    this.checking = false;
                    this.ChangeOrdersStatus('delivered');
                })

                .catch(res => {
                    this.checking = false;
                });
              } 
            },

            Auth(){
                if(!Vue.$localStorage.hasKey('deliver_data')){
                    this.$router.push({ name: 'Home'})
                }
                else{
                    this.Deliver_Data = Vue.$localStorage.get('deliver_data');
                }
            },

            onDecode (result) {
                
                if(!this.checking && this.step == 1){
                    this.checking = true;
                    console.log(result);
                    let formData = new FormData();
                    formData.append('tracking_number', result);

                    this.axios.post(process.env.VUE_APP_URL+`/api/track_order`, formData)
                    .then(res => {
                        this.scaned_order_details = res.data.data[0];
                        this.checking = false;
                        this.val_errors = false;
                        this.ChangeStep(2);
                    }).catch(res => {
                        this.checking = false;
                        this.val_errors = true;
                    });
                }  
                
            },

            ChangeStep(value){
              this.step = value;
            },

            onLoaded () {
                this.scanner_loaded = true;
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
                if(status == 'ReturnedToDeliver' && !this.scaned_order_details.delayed){ return 'راجع الى هدهد'; }
            },

            allowButton(status){
              return ([status, 'delivered'].includes(this.scaned_order_details.status)) ? false : true;
            },

            Refunded(){
              if(this.$refs.add_delay_form.validate()){
                let formData = new FormData();
                formData.append('order_id', this.scaned_order_details.id);
                formData.append('delayed', 'false');

                this.axios.post(process.env.VUE_APP_URL+'/api/Deliver/Set_Delay_Status', formData)
                .then(res => {
                    this.ChangeOrdersStatus('ReturnedToDeliver');
                })
                .catch(res => {
                });
              }
            }

        },

        computed:{
            P_Valid(){
                return this.returned_case !== ''
            },
            
            D_Valid(){
                return this.post_name !== '' && this.post_price !== ''
            },
        },

        watch:{
            Order_id(value){
              (value.length < 5) ? this.val_errors = false : false;
              (value.length > 5) ? this.onDecode(value) : false;
            }
        }

    };
</script>

<style lang="scss" scoped>

.login-form{
  max-width: 500px
}

input{
  text-align: center !important;
}
.Cairo{
  font-family: Cairo !important;
}
</style>