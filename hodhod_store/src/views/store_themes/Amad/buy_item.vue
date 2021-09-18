<template>

    <div class="body">
        <div class="container mt-5 px-5">

            <div v-if="OrderConfiremd" class="c_card" id="printMe">
                <div class="title">تم تقديم الطلب</div>
                <div class="info">
                    <div class="row">
                        <div class="col-md-7"> <span id="heading">تاريخ الطلب</span><br> <span id="details">{{formatDate(Date.now())}}</span> </div>
                        <div class="col-md-5 pull-right"> <span id="heading">رمز تتبع الطلب</span><br> <span id="details">{{NewOrder.track_code}}</span> </div>
                    </div>
                </div>
                <div class="pricing">
                    <div class="row">
                        <div class="col-md-7"> <span id="price">{{NewOrder.product}}</span> </div>
                        <div class="col-md-5"> <span id="name">المنتج</span> </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7"> <span id="price">{{NewOrder.size}}</span> </div>
                        <div class="col-md-5"> <span id="name">الحجم</span> </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7"> <span id="price"><span v-bind:style="`background-color:${NewOrder.color};`" class="colors"></span></span> </div>
                        <div class="col-md-5"> <span id="name">اللون</span> </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7"> <span id="price">{{NewOrder.quantity}}</span> </div>
                        <div class="col-md-5"> <span id="name">العدد</span> </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7"> <span id="price">{{NewOrder.receiver_full_name}}</span> </div>
                        <div class="col-md-5"> <span id="name">الاسم</span> </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7"> <span id="price">{{NewOrder.reciever_phone_number}}</span> </div>
                        <div class="col-md-5"> <span id="name">رقم الهاتف</span> </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7"> <span id="price">{{NewOrder.location_to_state}}</span> </div>
                        <div class="col-md-5"> <span id="name">المحافضة</span> </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7"> <span id="price">{{NewOrder.location_to_region}}</span> </div>
                        <div class="col-md-5"> <span id="name">العنوان</span> </div>
                    </div>
                </div>
                <div class="total">
                    <div class="row">
                        <div class="col-md-7"><big>IQD {{item_data.price}}</big></div>
                        <div class="col-md-5">سعر المنتج</div>
                    </div>
                    <div class="row" v-if="NewOrder.insurance">
                        <div class="col-md-7"><big>IQD {{insurance}}</big></div>
                        <div class="col-md-5">تأمينات</div>
                    </div>
                    <div class="row">
                        <div class="col-md-7"><big>IQD {{NewOrder.recieved_price}}</big></div>
                        <div class="col-md-5">المجموع</div>
                    </div>
                </div>

                <br>

                <!-- <div class="tracking">
                    <div class="title">متابعة التسوق</div>
                </div> -->
            </div>

            <div v-else class="row">
                <div class="col-md-8" >

                    <div class="card p-3">
                        <p class="text-uppercase uk-text-medium uk-text-right">تفاصيل المنتج</p>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="uk-text-right uk-margin-bottom">اللون</div>
                                <div>
                                    <span v-for="(color,index) in item_data.colors" v-bind:key="index" v-bind:style="`background-color:${color};`" v-bind:class="{'selected': selected_color_index === index}" class="colors" @click="select_color(color,index)"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="uk-text-right uk-margin-bottom">الحجم</div>
                                <div>
                                    <span v-for="(size,index) in item_data.sizes" v-bind:key="index" class="size uk-text-bold" v-bind:class="{'selected': selected_size_index === index}" @click="select_size(size,index)">{{size}}</span>
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="d-flex flex-row">
                                    <div class="inputbox mt-4 mr-2">
                                        <input type="number" class="form-control" required="required" v-model="NewOrder.quantity" maxlength="1"> 
                                        <span class="uk-text-right ">العدد</span>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <div class="d-flex flex-row">
                                    <div class="inputbox mt-3 mr-2"> <input type="text" name="name" class="form-control" required="required"> <span>Expiry</span> </div>
                                    <div class="inputbox mt-3 mr-2"> <input type="text" name="name" class="form-control" required="required"> <span>CVV</span> </div>
                                </div>
                            </div> -->
                        </div>
                            
                        <div class="mt-4 mb-4">
                            <p class="text-uppercase uk-text-medium uk-text-right"> معلومات المشتري</p>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="inputbox mt-3 mr-2"> <input type="number" v-model="NewOrder.reciever_phone_number" class="form-control" required="required"> <span>رقم الهاتف</span> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="inputbox mt-3 mr-2"> <input type="text" v-model="NewOrder.receiver_full_name" class="form-control" required="required"> <span>الاسم الكامل</span> </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="inputbox mt-3 mr-2"> <input type="text" v-model="NewOrder.location_to_region" class="form-control" required="required"> <span>العنوان</span> </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="inputbox mt-3 mr-2"> 
                                        <select class="form-control" style=" direction: rtl; " id="form-horizontal-select" v-model="NewOrder.location_to_state">
                                            <option value="default" disabled>المحافضة</option>
                                            <option value="Erbil">أربيل</option>
                                            <option value="AlAnbar">الأنبار</option>
                                            <option value="Babil">بابل</option>
                                            <option value="Baghdad">بغداد</option>
                                            <option value="Basra">البصرة</option>
                                            <option value="Dahuk">دهوك</option>
                                            <option value="AlDiwaniyah">الديوانية</option>
                                            <option value="Diyala">ديالي</option>
                                            <option value="Dhi Qar">ذي قار</option>
                                            <option value="AsSulaymaniyah">السليمانية</option>
                                            <option value="Saladin">صلاح الدّين</option>
                                            <option value="Kirkuk">كركوك</option>
                                            <option value="Karbala">كربلاء</option>
                                            <option value="AlMuthana">المثنى</option>
                                            <option value="Maysan">ميسان</option>
                                            <option value="Najaf">النجف</option>
                                            <option value="Nineveh">نينوى</option>
                                            <option value="Wasit">واسط</option>
                                            <option value="Zakho">زاخو</option>
                                        </select>  
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="card p-3">
                        <p class="text-uppercase uk-text-medium uk-text-right"> الوصف</p>
                        <p class="text-uppercase uk-text-small uk-text-right">{{item_data.description}}</p>
                    </div>

                </div>
                <div class="col-md-4">

                    <br>

                    <div class="card mx-auto p-0"> <img class='mx-auto pic' :src="item_data.image" />
                        <div class="d-flex card-title d-flex px-4">
                            <div>
                                <span class="hint-star star" style="display:initial">
                                    <i v-for="item in item_data.rate" v-bind:key="item" class="fa fa-star" aria-hidden="true"></i>
                                    <i v-for="item in (5 - item_data.rate)" v-bind:key="item" class="far fa-star" aria-hidden="true"></i>
                                </span>
                            </div>
                            <div class="uk-text-bold">{{item_data.item}} {{item_data.rate}}</div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex card-title d-flex px-4">
                                <div>IQD {{item_data.price}}</div>
                                <div class="uk-text-bold">سعر المنتج</div>
                            </div>
                            <div v-if="NewOrder.quantity > 1" class="d-flex card-title d-flex px-4">
                                <div>{{NewOrder.quantity}}</div>
                                <div class="uk-text-bold">العدد</div>
                            </div>
                            <hr>
                            <div class="d-flex card-title d-flex px-4">
                                <div>IQD {{NewOrder.recieved_price}}</div>
                                <div class="uk-text-bold">المجموع</div>
                            </div>
                            <!-- <p class="text-muted">Your payment details</p>
                            <div class="numbr mb-3"> <i class=" col-1 fas fa-credit-card text-muted p-0"></i> <input class="col-10 p-0" type="text" placeholder="Card Number"> </div>
                            <div class="line2 col-lg-12 col-12 mb-4"> <i class="col-1 far fa-calendar-minus text-muted p-0"></i> <input class="cal col-5 p-0" type="text" placeholder="MM/YY"> <i class="col-1 fas fa-lock text-muted"></i> <input class="cvc col-5 p-0" type="text" placeholder="CVC"> </div> -->
                        </div>
                        <div class="text-center p-0" v-bind:class="{'footer' : IsValid, 'footer-disabled' : !IsValid || MiniLoading}">
                            <div class="col-lg-12 col-12 p-0">
                                <div v-if="MiniLoading" class="order" uk-spinner></div>
                                <p v-else class="order" @click.prevent="ConfirmOrder()">اتمام الطلب</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <!-- <button  class="uk-button uk-button-default uk-button-medium" @click.prevent="print()"><i class="fas fa-file-download" style="font-size: 19px;"></i></button> -->

    </div>
 
</template>

<script>

    

    const options = {
        name: '_blank',
        specs: [
            'fullscreen=yes',
            'titlebar=yes',
            'scrollbars=yes'
        ],
        styles: [
            'https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.3.1/cerulean/bootstrap.min.css',
            'https://cdn.jsdelivr.net/npm/uikit@3.3.7/dist/css/uikit.min.css',
            'https://unpkg.com/kidlat-css/css/kidlat.css'
        ]
    }

    import Vue from 'vue'
    import VueHtmlToPaper from 'vue-html-to-paper';
    Vue.use(VueHtmlToPaper, options);

    export default {

        components: {
        },

        data(){
            return {
                selected_color_index: null,
                selected_size_index: null,
                Code:'',
                val_errors:{},
                OrderFound:false,
                data:{},
                CodeExpired:false,
                Store_Items: {},
                item_data: {},
                SelectedItem:{},
                is_valid:false,
                MiniLoading:false,
                output: null,
                OrderConfiremd:false,
                is_not_valid:false,
                insurance: 0,
                NewOrder: {
                    product: 'default',
                    quantity: 1,
                    size: '',
                    color: '',
                    recieved_price: 0,
                    receiver_full_name: '',
                    reciever_phone_number: '',
                    location_to_state: 'default',
                    location_to_region: '',
                    insurance: false,
                    payment_method: 'RECIEVER'
                },
                invoice: {},
            }
        },

        created(){
            this.item_data_get();
        },

        methods: {
            item_data_get(){
                this.axios.get(process.env.VUE_APP_URL+`/api/view_item/${this.$route.params.item_id}`) 
                .then(res => {
                    this.item_data = res.data.data;
                    this.NewOrder.recieved_price = this.item_data.price;
                    this.NewOrder.product = this.item_data.item;
                })
                .catch(res => {
                    this.val_errors = res.response.data.error;
                    this.MiniLoading = false;
                });
            },

            select_color(color,selected_color_index){
                this.selected_color_index = selected_color_index;
                this.NewOrder.color = color;
            },

            select_size(size,selected_size_index){
                this.selected_size_index = selected_size_index;
                this.NewOrder.size = size;
            },

            ConfirmOrder(){
                if(this.IsValid){
                    this.MiniLoading = true;
                    let formData = new FormData();
                    formData.append('store_id', parseFloat(this.item_data.store_id));
                    formData.append('product_id', this.item_data.id);
                    formData.append('product_name', this.item_data.item);
                    formData.append('product_image', this.item_data.image);
                    formData.append('quantity', this.NewOrder.quantity);
                    formData.append('receiver_full_name', this.NewOrder.receiver_full_name);
                    formData.append('reciever_phone_number', this.NewOrder.reciever_phone_number);
                    formData.append('recieved_price', this.NewOrder.recieved_price);
                    formData.append('location_to_state', this.NewOrder.location_to_state);
                    formData.append('location_to_region', this.NewOrder.location_to_region);
                    formData.append('insurance', this.NewOrder.insurance);
                    formData.append('size', this.NewOrder.size);
                    formData.append('color', this.NewOrder.color);
                    formData.append('payment_method', this.NewOrder.payment_method);

                    this.axios.post(process.env.VUE_APP_URL+`/api/submit_orders_By_Coustomer`, formData)
                    .then(res => {      
                        if (res.status == 200){
                            this.NewOrder.track_code = res.data.data[0].Code;
                            this.OrderConfiremd = true;
                            this.MiniLoading = false;
                        }
                    })
                    .catch(res => {
                        this.val_errors = res.response.data.error;
                        this.MiniLoading = false;
                    });
                }
            },
            
            OnItemSelect(event){
                this.SelectedItem = event.target.value;
            },

            Release_Form(){
                this.NewOrder = {
                    product: 'default',
                    quantity: 1,
                    price: 0,
                    size: '',
                    color: '',
                    selected_color_index : null,
                    selected_size_index : null,
                    recieved_price: 0,
                    receiver_full_name: '',
                    reciever_phone_number: '',
                    location_to_state: 'default',
                    location_to_region: '',
                    insurance: false,
                    payment_method: 'RECIEVER'
                }
            },

            Total_Price() {
                
                this.NewOrder.recieved_price = parseFloat(this.item_data.price) * parseFloat(this.NewOrder.quantity);
                
                if(this.NewOrder.insurance == true){
                    if(this.NewOrder.location_to_state == 'Baghdad'){
                        this.NewOrder.recieved_price = this.NewOrder.recieved_price + 1000;
                        this.insurance = 1000;
                    }
                    else{
                        this.NewOrder.recieved_price = this.NewOrder.recieved_price + 9000;
                        this.insurance = 9000;
                    }
                }
                
            },

            formatDate(time) {
                return new Date(time).toLocaleDateString(['en-iq'], {month: 'numeric', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric'});
            },

            print () {
                this.$htmlToPaper('printMe');
            }
        },

        watch: {
            'NewOrder.insurance'(){
                this.Total_Price();
            },

            'NewOrder.quantity'(val){
                (val !== '') ? this.Total_Price() : true;
            },

            'NewOrder.location_to_state'(){
                this.Total_Price();
            },
        },

        computed: {
            IsValid() {
                return this.NewOrder.receiver_full_name !== ''
                && this.NewOrder.quantity > 0
                && this.NewOrder.reciever_phone_number !== ''
                && this.NewOrder.size !== ''
                && this.NewOrder.color !== ''
                && this.NewOrder.location_to_state !== 'default'
                && this.NewOrder.location_to_region !== ''
            },

        }
    };

</script>

<style scoped lang="scss"> 
@import './scss/buy.scss';
</style>
