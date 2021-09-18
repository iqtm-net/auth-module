<template>

    <div class="body">
        <div class="container mt-5 px-5">
            <!-- <div class="mb-4">  
                <h2>{{item_data.item}}</h2> 
                <span>{{item_data.description}}</span>
                formatDate(time) {
                    return new Date(time).toLocaleDateString(['en-iq'], {month: 'numeric', day: 'numeric', year: 'numeric'});
                },
            </div> -->
            <div v-if="OrderConfiremd" class="c_card">
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
                <div class="col-md-8">
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
                            <!-- <div class="col-md-6">
                                <div class="uk-flex uk-flex-center mt-3 mr-2">
                                    <div class="uk-width-1-2">
                                        <div>
                                            <input type="checkbox" id="toggle" hidden v-model="NewOrder.insurance" />
                                            <label for="toggle" class="checkboxlabel"></label>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2 mt-1"> تأمين المنتج ؟ </div>
                                </div>
                            </div> -->
                            
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
                </div>
                <div class="col-md-4">
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
                            <div class="d-flex card-title d-flex px-4">
                                <div>IQD {{insurance}}</div>
                                <div class="uk-text-bold">تأمينات</div>
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
                    <!-- <div class="card card-blue p-3 text-white mb-3"> <span>You have to pay</span>
                        <div class="d-flex flex-row align-items-end mb-3">
                            <h1 class="mb-0 yellow">$549</h1> <span>.99</span>
                        </div> <span>Enjoy all the features and perk after you complete the payment</span> <a href="#" class="yellow decoration">Know all the features</a>
                        <div class="hightlight"> <span>100% Guaranteed support and update for the next 5 years.</span> </div>
                    </div> -->
                </div>
            </div>

        </div>
    </div>
 
</template>

<script>

    // import axios from 'axios';

    export default {

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

                this.axios.get(process.env.VUE_APP_URL+`/api/view_item/32`)
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
                    // formData.append('Store_Shared_Code', this.$route.params.Code);
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
                            // this.Release_Form();
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
                console.log(event.target.value)
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

@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');

.body{
    position: absolute;
    background-color: #f9f9f9;
    width: 100%;
    height: 100%;
}

.container {
    background-color: #f9f9f9;
}

.card {
    border: none;
    box-shadow: 0 6px 20px 0 rgba(86, 86, 86, 0.19);
}

.form-control {
    border-bottom: 2px solid #eee !important;
    
    border: none;
    font-weight: 600;
    text-align: right;
}

.form-control:focus {
    color: #495057;
    background-color: #fff;
    border-color: #8bbafe;
    outline: 0;
    box-shadow: none;
    border-radius: 0px;
    border-bottom: 2px solid blue !important
}

.inputbox {
    position: relative;
    margin-bottom: 20px;
    width: 100%
}

.inputbox span {
    position: absolute;
    top: 7px;
    right: 11px;
    transition: 0.5s
}

.inputbox i {
    position: absolute;
    top: 13px;
    right: 8px;
    transition: 0.5s;
    color: #3F51B5
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0
}

.inputbox input:focus~span {
    transform: translateX(-0px) translateY(-15px);
    top: -5px;
    right: 0px;
    font-size: 12px
}

.inputbox input:valid~span {
    transform: translateX(-0px) translateY(-15px);
    top: -5px;
    right: 0px;
    font-size: 12px
}

.card-blue {
    background-color: #492bc4
}

.hightlight {
    background-color: #5737d9;
    padding: 10px;
    border-radius: 10px;
    margin-top: 15px;
    font-size: 14px
}

.yellow {
    color: #fdcc49
}

.decoration {
    text-decoration: none;
    font-size: 14px
}

.btn-success {
    color: #fff;
    background-color: #492bc4;
    border-color: #492bc4
}

.btn-success:hover {
    color: #fff;
    background-color: #492bc4;
    border-color: #492bc4
}

.decoration:hover {
    text-decoration: none;
    color: #fdcc49
}


img {
    width: 100%
}

.card-title {
    justify-content: space-between;
    margin-top: 25px
}

.register {
    font-size: 10px;
    position: relative;
    bottom: 5px
}

.cvc {
    width: 2.5em;
    position: absolute
}

input {
    border: none;
    padding-left: 4px;
    background-color: #f7f1f1;
    font-size: 15px
}

.card-body {
    background-color: #f7f1f1
}

.footer {
    background-color: #00BCD4;
    color: white
}

.footer:hover {
    cursor: pointer;
    background-color: #0097A7
}

.footer-disabled{
    background-color: #6c6c6c;
    color: white
}

.numbr {
    border-bottom: 1px solid #c1bcbc;
    padding-bottom: 8px
}

.line2 {
    border-bottom: 1px solid #c1bcbc;
    padding-bottom: 8px;
    padding-left: 0px
}

input.focus,
input:focus {
    outline: 0;
    box-shadow: none !important
}

.numbr.numbr.hover,
.numbr:hover {
    border-bottom: 1px solid aqua
}

.line2.hover,
.line2:hover {
    border-bottom: 1px solid aqua
}

.fa-lock {
    margin-right: 10px
}

.order {
    margin-top: 10px;
    margin-bottom: 10px;
}

.colors{
    width:20px;
    height:20px;
    display:inline-block;
    transition:0.3s all;
    border-radius:50%;
    border: 0.1px solid #d8d8d8;
    background: #ef8bef; 
    margin: 0 15px;
    opacity: 0.5;
}
.colors:hover, .size:hover{
    transform: scale(1.2);
    box-shadow: 0 0 0 8px rgba(173, 173, 170, .3);
    cursor: pointer;
}
.colors:active, .size:active{
    transform: scale(.8);
}
.colors:nth-child(2){
    background: #f43542;
}.colors:nth-child(3){
    background: #ffe55b;
}.colors:nth-child(4){
    background: #6cf96c;
}
.selected{
    opacity: 1 !important;

}
.size{
    border-radius: 50%;
    // width: 30px;
    // height: 30px;
    justify-content: center;
    align-items: center;
    margin: 0 5px;
    cursor: pointer;
    opacity: 0.5;
    transition: all .3s;
}

// *********** CHECKBOX ************ //


input[type="checkbox"] {
	visibility: hidden;
	&:checked + label {
		// transform: rotate(360deg);
		background-color: #000;
		&:before {
			transform: translateX(25px);
			background-color: #FFF;
		}
	}
}

.checkboxlabel {
	width: 70px;
	height: 35px;
	border: 6px solid;
	border-radius: 99em;
	position: relative;
	transition: transform .25s ease-in-out;
	transform-origin: 50% 50%;
	cursor: pointer;
	
	&:before {
		transition: transform .25s ease;
		transition-delay: .1s;
		content: "";
		display: block;
		position: absolute;
		width: 15px;
		height: 15px;
		background-color: #000;
		border-radius: 50%;
		top: 4px;
		left: 12px;
	}
}

// *********** ORDER CONFIRMED ************ //

.c_card {
    margin: auto;
    // width: 38%;
    max-width: 600px;
    padding: 4vh 0;
    box-shadow: 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-top: 3px solid rgb(252, 103, 49);
    border-bottom: 3px solid rgb(252, 103, 49);
    border-left: none;
    border-right: none
}

@media(max-width:768px) {
    .c_card {
        width: 90%
    }
}

.title {
    color: rgb(252, 103, 49);
    font-weight: 600;
    margin-bottom: 2vh;
    padding: 0 8%;
    font-size: initial
}

#details {
    font-weight: 400
}

.info {
    padding: 5% 8%
}

.info .col-5 {
    padding: 0
}

#heading {
    color: grey;
    line-height: 6vh
}

.pricing {
    background-color: #ddd3;
    padding: 2vh 8%;
    font-weight: 400;
    line-height: 2.5
}

.pricing .col-3 {
    padding: 0
}

.total {
    padding: 2vh 8%;
    color: rgb(252, 103, 49);
    font-weight: bold
}

.total .col-3 {
    padding: 0
}

.c_footer {
    padding: 0 8%;
    font-size: x-small;
    color: black
}

.c_footer img {
    height: 5vh;
    opacity: 0.2
}

.c_footer a {
    color: rgb(252, 103, 49)
}

.c_footer .col-10,
.col-2 {
    display: flex;
    padding: 3vh 0 0;
    align-items: center
}

.c_footer .row {
    margin: 0
}

#progressbar {
    margin-bottom: 3vh;
    overflow: hidden;
    color: rgb(252, 103, 49);
    padding-left: 0px;
    margin-top: 3vh
}

#progressbar li {
    list-style-type: none;
    font-size: x-small;
    width: 25%;
    float: left;
    position: relative;
    font-weight: 400;
    color: rgb(160, 159, 159)
}

#progressbar #step1:before {
    content: "";
    color: rgb(252, 103, 49);
    width: 5px;
    height: 5px;
    margin-left: 0px !important
}

#progressbar #step2:before {
    content: "";
    color: #fff;
    width: 5px;
    height: 5px;
    margin-left: 32%
}

#progressbar #step3:before {
    content: "";
    color: #fff;
    width: 5px;
    height: 5px;
    margin-right: 32%
}

#progressbar #step4:before {
    content: "";
    color: #fff;
    width: 5px;
    height: 5px;
    margin-right: 0px !important
}

#progressbar li:before {
    line-height: 29px;
    display: block;
    font-size: 12px;
    background: #ddd;
    border-radius: 50%;
    margin: auto;
    z-index: -1;
    margin-bottom: 1vh
}

#progressbar li:after {
    content: '';
    height: 2px;
    background: #ddd;
    position: absolute;
    left: 0%;
    right: 0%;
    margin-bottom: 2vh;
    top: 1px;
    z-index: 1
}

.progress-track {
    padding: 0 8%
}

#progressbar li:nth-child(2):after {
    margin-right: auto
}

#progressbar li:nth-child(1):after {
    margin: auto
}

#progressbar li:nth-child(3):after {
    float: left;
    width: 68%
}

#progressbar li:nth-child(4):after {
    margin-left: auto;
    width: 132%
}

#progressbar li.active {
    color: black
}

#progressbar li.active:before,
#progressbar li.active:after {
    background: rgb(252, 103, 49)
}

</style>
