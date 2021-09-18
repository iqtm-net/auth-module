<template>

    <div class="uk-margin">
        
        <hr class="uk-divider-icon">

        <!-- Filter -->
        <nav style="margin: 20px 0px;" uk-navbar>
            <div class="uk-navbar-left">
                <ul class="uk-navbar-nav datepickerCs">
                    <li><datepicker class="datepickerCsLi" placeholder="From" @selected="onChangeDateFrom($event)"></datepicker></li>
                    <li><datepicker class="datepickerCsLi" placeholder="To" @selected="onChangeDateTo($event)"></datepicker></li>
                    <li>
                        <button :disabled="!Anytime" @click.prevent="onChangeAnytime()" class="btn btn-outline-info" style="padding: 0.1rem 0.5rem;">Anytime</button>
                    </li>
                    <li>
                        <button :disabled="!NoOrders" @click.prevent="DonloadExcel()" class="btn btn-outline-success" style="padding: 0.1rem 0.7rem;">
                            <span class="fas fa-file-download"></span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="uk-navbar-right">
                <ul class="uk-navbar-nav">
                    <li>
                        <form class="uk-search uk-search-default">
                            <a href="#" class="uk-search-icon-flip" uk-search-icon></a>
                            <input class="uk-search-input" v-debounce:2000ms="Searching" v-model="search_data" style="border-radius: 5px; background: white;" type="search" placeholder="Search">
                        </form> 
                    </li> 
                    <li>
                        <select class="uk-select" @change="FromStateFun($event)" v-model="FromState">
                            <option value="All">From</option>
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
                    </li>
                    <li>
                        <select class="uk-select" @change="ToStateFun($event)" v-model="ToState">
                            <option value="All">To</option>
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
                    </li>
                    <li>
                        <select @change="onChange($event)" class="uk-select" v-model="status">
                            <option value="waiting">Waiting (Accepted)</option>
                            <option value="waiting_unaccepted">Waiting (Unaccepted)</option>
                            <option value="pending">Pending</option>
                            <option value="delivered">Delivered</option>
                            <option value="ReturnedToDeliver">Returned To Deliver</option>
                            <option value="ReturnedToClient">Returned To Client</option>
                        </select>
                    </li>
                </ul>
            </div>
        </nav>

        <hr class="uk-divider-icon">

        <!-- <nav style="margin: 20px 0px;" uk-navbar>
            <div class="uk-navbar-left">
                <ul class="uk-navbar-nav datepickerCs">
                    <li>
                        <div class="uk-margin" uk-margin>
                            <div uk-form-custom="target: true">
                                <input type="file" id="file" ref="file" v-on:change="handleFileUpload()">
                                <input class="uk-input uk-form-width-medium" type="text" placeholder="Upload Excel File" disabled>
                            </div>
                            <button :disabled="file == '' || miniload" @click.prevent="submitFile()" class="uk-button uk-button-default">Upload</button>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="uk-navbar-right">
                <ul class="uk-navbar-nav">
                    <li>
                        <button :disabled="!checkedOrdersVal" @click.prevent="ChangeSelectedOrdersStatus()" class="btn btn-outline-success" style="padding: 0.5rem 0.7rem;">
                            Update Status
                        </button>
                    </li>
                    <li>
                        <select :disabled="!checkedOrdersVal" class="uk-select" v-model="selected_status">
                            <option value="waiting">Waiting (Accepted)</option>
                            <option value="waiting_unaccepted">Waiting (Unaccepted)</option>
                            <option value="pending">Pending</option>
                            <option value="delivered">Delivered</option>
                            <option value="ReturnedToDeliver">Returned To Deliver</option>
                            <option value="ReturnedToClient">Returned To Client</option>
                        </select>
                    </li>
                </ul>
            </div>
        </nav> -->
        
        <div class="uk-margin uk-child-width-1-4@m uk-child-width-1-1@s uk-text-center" uk-grid>
            <div>
                <button @click.prevent="DownloadSelectedOrdersExcel()" :disabled="SelectedIds.length == 0 || miniload" class="uk-button uk-button-danger uk-button-large uk-width-expand" style="border-radius:5px">
                    <i class="fas fa-file-download"></i>
                    Download Excel
                </button>
            </div>
            <div>
                <button @click.prevent="PrintLabel()" :disabled="SelectedIds.length == 0 || miniload" class="uk-button uk-button-danger uk-button-large uk-width-expand" style="border-radius:5px">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Print Invoices
                </button>
            </div>
            <div>
                <select @change="ChangeSelectedOrdersStatus($event)" :disabled="SelectedIds.length == 0 || miniload"  class="uk-select uk-button-danger" v-model="selected_status" style="height: 54px; border-radius: 5px;">
                    <option value="waiting" class="uk-text-white">Waiting (Accepted)</option>
                    <option value="waiting_unaccepted" class="uk-text-white">Waiting (Unaccepted)</option>
                    <option value="pending" class="uk-text-white">Pending</option>
                    <option value="delivered" class="uk-text-white">Delivered</option>
                    <option value="ReturnedToDeliver" class="uk-text-white">Returned To Deliver</option>
                    <option value="ReturnedToClient" class="uk-text-white">Returned To Client</option>
                </select>
            </div>
            <div>
                <input v-bind:class="{'uk-form-danger' : !valid_id && isLoaded_id, 'uk-form-success ' : valid_id && isLoaded_id}" class="uk-input uk-text-center uk-background-default" type="text" placeholder="Enter Order Id" v-model="Order_id" v-debounce:1000ms="Searching_id" style="height: 54px;"> 
            </div>
        </div>

        <div class="uk-margin uk-child-width-1-4@m uk-child-width-1-1@s uk-text-center" uk-grid>
            <div v-for="(order_id) in SelectedIds" :key="order_id.id">
                <div class="uk-card uk-card-default uk-padding-small uk-inline uk-width-1-1">
                    <div class="uk-text-large uk-text-center">{{order_id[1]}}</div>
                    <div class="uk-position-top-right">
                        <button @click.prevent="RemoveId(order_id[0])" class="uk-button uk-button-danger uk-button-small uk-width-expand" style="border-radius:0px 0px 0px 10px;"> x </button>
                    </div>
                </div>
            </div>
        </div>

        <hr class="uk-divider-icon">

        <vcl-table v-if="!isLoaded" :rows="9" :columns="5"></vcl-table>

        <div v-else>
            
            <table border="0" class="table cust-table uk-card uk-card-default">
                <thead>
                    <tr style="">
                        <th style="width:100px;"> <li class="fa fa-gear"></li> </th>
                        <th style="width:200px;">Shipment</th>
                        <th style="width:100px;">Sender name</th>
                        <th style="width:200px;">Sender phone</th>
                        <th style="width:100px;">Receiver name</th>
                        <th style="width:200px;">Receiver phone</th>
                        <th style="width:150px;">handeled by</th>
                        <th style="width:100px;">price</th>
                        <th style="width:100px;">order_id</th>
                        <th style="width:100px;">status</th>
                        <th style="width:100px;">date</th>
                    </tr>
                </thead> 
                <tbody v-for="(order) in orders" :key="order.id">
                    <tr>
                        <td style="width:100px;" class="text-center">
                            <router-link :to="`/order_details/${order.id}`">
                                <button class="btn btn-outline-info"><span class="fa fa-eye"></span></button>
                            </router-link>
                        </td>
                        <td style="width:200px;">{{ order.product_name }}</td>
                        <td style="width:100px;">{{ order.sender_full_name }}</td>
                        <td style="width:200px;">{{ order.sender_phone_number }}</td>
                        <td style="width:100px;">{{ order.receiver_full_name }}</td>
                        <td style="width:200px;">{{ order.reciever_phone_number }}</td>
                        <td style="width:150px;">{{ order.Deliver_Fname }} {{ order.Deliver_Lname }}</td>
                        <td style="width:100px;">{{ order.recieved_price }}</td>
                        <td style="width:100px;">{{ order.track_code }}</td>
                        <td style="width:100px;">{{ order.status }}</td>
                        <td style="width:100px;">
                            <button @click.prevent="ViewDetails(order.id)" class="btn btn-outline-info" uk-toggle="target: #ViewHistory">
                                <span class="far fa-calendar-alt"></span>
                            </button>
                        </td> 
                    </tr>
                </tbody>

            </table>

            <br> 

            <empty-result :Data="orders"></empty-result>

            <br>

            <div class="row Departmain">
                <div class="col-sm-6 Previous" align="center">
                    <button v-if="orders.prev_page_url !== null" _ngcontent-udn-32="" class="btn btn-secondary tab-nav-btn" type="button" @click.prevent="Previous()">
                        <i _ngcontent-udn-32="" class="fa fa-chevron-left"></i>
                    </button>
                </div>

                <div class="col-sm-6 Next" align="center">
                    <button v-if="orders.next_page_url !== null" _ngcontent-udn-32="" class="btn btn-secondary tab-nav-btn" type="button" @click.prevent="Next()">
                        <i _ngcontent-udn-32="" class="fa fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            
        </div>
        
        <!-- Update Status History -->
        <div id="ViewHistory" uk-modal>
            <div class="uk-modal-dialog uk-modal-body"> 
                <vcl-table v-if="!isLoadedHistory" :rows="4" :columns="2"></vcl-table>
                <div v-else>
                    <div v-if="order_history.length == 0" style="text-align:center"> <i class="fas fa-box-open" style="font-size: 10rem;"></i> </div>
                    <table v-else border="0" class="table cust-table uk-card uk-card-default">
                        <thead> 
                            <tr> 
                                <th style="width:50%;">Status</th>
                                <th style="width:50%;">Date</th>
                            </tr>
                        </thead> 
                        <tbody v-for="(order) in order_history" :key="order.id">
                            <tr>
                                <th style="width:50%;">{{ order.status }}</th> 
                                <td style="width:50%;">{{ formatDate(order.created_at) }}</td> 
                            </tr>
                        </tbody>
                    </table> 
                </div>
            </div>
        </div>

        <!-- Preview Invoices -->
        <div id="Invoices" uk-modal>

            <div class="uk-card uk-card-default uk-padding uk-width-1-1" align="center">
                <vcl-table v-if="!isLoadedSelectedOrders" :rows="9" :columns="2"></vcl-table>
                <div v-else class="uk-card uk-card-default uk-width-1-1 uk-margin-large-top div001" v-for="(order) in SelectedOrders" :key="order.id">
                    <div class="uk-card-header" style="border-bottom:none">
                        <div class="uk-flex uk-flex-middle uk-flex-between">
                            <div class="uk-width-auto">
                                <img class="uk-border-circle" width="80" src="/images/h_logo.jfif">
                            </div>
                            <div class="uk-width-expand">
                                <h3 class="uk-card-title uk-margin-remove-bottom">{{order.track_code}}</h3>
                            </div>
                            <div class="uk-width-auto">
                                <img class="uk-border-circle" width="60" height="60" src="/images/unicome_loco.png">
                            </div>
                        </div>
                    </div>

                    <div>
                        <table border="0" class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-center">
                            <thead>
                                <tr>
                                    <th class="uk-text-right uk-table-expand"></th>
                                    <th class="uk-text-right uk-width-small"></th>
                                </tr>
                            </thead> 
                            <!-- <tbody v-for="(order) in orders" :key="order.id"> -->
                            <tbody>
                                <tr>
                                    <td>{{order.sender_full_name}}</td>
                                    <td>المرسل</td>
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
                                    <td>{{order.recieved_price}}</td>
                                    <td>سعر البريد</td>
                                </tr>
                                <!-- <tr>
                                    <td>ملاحضات</td>
                                    <td>{{order.receiver_full_name}}</td>
                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                    <div class="uk-card-footer">
                        <!-- <a href="#" class="uk-button uk-button-text">Read more</a> -->
                        <img :src="order.barcode" />
                    </div>
                </div>
            </div>
        </div>

        <br>

    </div>
</template>


<script>
    import Datepicker from "vuejs-datepicker/dist/vuejs-datepicker.esm.js"
    export default {
        props: {
            Account_Id: {
                default: 'All'
            },
            
        },
        components: {
            Datepicker,
        },
        data(){
          return {
            orders: {},
            order_history: {},
            status: 'pending',
            selected_status: 'pending',
            dateFrom: 'All',
            dateTo: 'All',
            FromState: 'All',
            ToState: 'All', 
            search_data: '', 
            current_page: 1, 
            is_valid:false,
            isLoaded:false,
            isLoadedHistory:false,
            isLoadedSelectedOrders:false,
            miniload: false,
            isLoaded_id: false,
            valid_id:null,
            View:false,
            checkedOrders: [],
            file: '',
            Order_id: '',
            Counter: 0,
            
            SelectedIds: [],
            SelectedOrders: {},
            output: null
          }
        }, 
        created(){
            this.fetchArticles();  
        }, 
        methods: {
            handleFileUpload(){
                this.file = this.$refs.file.files[0];
            },
            submitFile(){
                this.miniload = true;
                let formData = new FormData();
                formData.append('file', this.file);
                axios.post('/api/Admin/ChangeOrdersStatusByExcel', formData,{ headers: { 'Content-Type': 'multipart/form-data' } })
                .then(res => {
                    this.$toasted.show("Success", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.fetchArticles();
                    this.checkedOrders = [],
                    this.miniload = false;
                })
                .catch(res => {
                    this.val_errors = res.response.data.error;
                    this.miniload = false;
                });
            },
            async fetchArticles(){
                // this.isLoaded = false;
                // await axios.get(`/api/Admin/Orders/${this.Account_Role_Number}/${this.Account_Id}/${this.status}/${this.dateFrom}/${this.dateTo}/${this.FromState}/${this.ToState}?page=${this.current_page}`)
                // .then(res => {
                //     this.isLoaded = true;
                //     this.orders = res.data;
                //     this.awaitingSearch = false; 
                // })
                // .catch(res => {
                //     let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                // });
            },
            formatDate(time) {
                return new Date(time).toLocaleDateString(['en-iq'], {month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'});
            },
            ViewDetails(order_id){
                this.isLoadedHistory = false;
                axios.get(`/api/Admin/order_status_history/${order_id}`)
                .then(res => {
                    if (res.status == 200) {
                        this.order_history = res.data;
                        this.isLoadedHistory = true;
                    }
                });
            },
            Next() { this.current_page = this.current_page+1; this.fetchArticles(); },
            Previous() { this.current_page = this.current_page-1; this.fetchArticles(); },
            onChange(event) {
                this.status = event.target.value;
                this.fetchArticles();
            },
            onChangeDateFrom(event) {
                this.dateFrom = event.toISOString().substr(0,10);
                if(this.dateFrom !== 'All' && this.dateTo !== 'All'){ this.fetchArticles(); }
                
            },
            onChangeDateTo(event) {
                this.dateTo = event.toISOString().substr(0,10);
                if(this.dateFrom !== 'All' && this.dateTo !== 'All'){ this.fetchArticles(); }
            },
            onChangeAnytime() {
                this.dateTo = 'All';
                this.dateFrom = 'All';
                this.fetchArticles();
            },
            ToStateFun(event){
                this.ToState = event.target.value;
                this.fetchArticles();
            },
            FromStateFun(event){
                this.FromState = event.target.value;
                this.fetchArticles();
            },
            
            DonloadExcel(){
                this.miniload = true;
                let formData = new FormData();
                formData.append('orders', JSON.stringify(this.orders));
                axios({
                    url: `/api/Admin/orders/DownloadExcel`,
                    method: 'POST',
                    responseType: 'blob',
                    data: formData,
                }).then((response) => {
                    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    var fileLink = document.createElement('a');
                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', Date()+'.xlsx');
                    document.body.appendChild(fileLink);
                    fileLink.click();
                });
            },
            DownloadSelectedOrdersExcel(){
                this.miniload = true;
                let formData = new FormData();
                formData.append('orders', JSON.stringify(this.SelectedIds));
                axios({
                    url: `/api/comp/DownloadSelectedOrdersExcel`,
                    method: 'POST',
                    responseType: 'blob',
                    data: formData,
                }).then((response) => {
                    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    var fileLink = document.createElement('a');
                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', Date()+'.xlsx');
                    document.body.appendChild(fileLink);
                    fileLink.click();
                    this.miniload = false;
                });
            },
            
            ChangeSelectedOrdersStatus(event){
                this.miniload = true;
                let formData = new FormData();
                formData.append('status', event);
                formData.append('order_ids', JSON.stringify(this.SelectedIds));
                axios.post('/api/comp/ChangeSelectedOrdersStatus', formData)
                .then(res => {
                    if (res.status == 200) {
                        this.$toasted.show("Success", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.fetchArticles();
                        this.checkedOrders = [],
                        this.miniload = false;
                    }
                })
                .catch(res => {
                        this.val_errors = res.response.data.error;
                        this.miniload = false;
                });
            },
            Searching(){
                if(this.search_data == ''){ this.fetchArticles(1); }
                else{
                    // axios.get(`/api/Admin/search_for_order/${this.Account_Role_Number}/${this.Account_Id}/${this.search_data}`)
                    // .then(res => {
                    //     if (res.status == 200) {
                    //         this.orders = res.data;
                    //         this.isLoaded = true;
                    //     }
                    // }).catch(res => { this.isLoaded = true;});
                }
            },
            Searching_id(){
                
                this.isLoaded_id = false;
                var v = this;
                if(this.Order_id !== ''){ 
                   axios.post(`/api/comp/check_order_id/${this.Order_id}`)
                    .then(res => {
                        
                        this.Counter = this.Counter+1;
                        let wrongMap = new Map();
                        
                        wrongMap.set(this.Counter, this.Order_id );
                        function logMapElements(value, key, map) {
                            v.SelectedIds.push([key, value]); 
                        }
                        
                        wrongMap.forEach(logMapElements);
                        this.valid_id = 1;
                        this.isLoaded_id = true;
                    }).catch(res => { 
                        this.valid_id = 0;
                        this.isLoaded_id = true;
                    }); 
                }
            },
            RemoveId(index){
                //console.log(index)
                var v = this;
                let wrongMap = new Map(this.SelectedIds);
                
                v.SelectedIds = [];
                wrongMap.delete(index);
                
                function logMapElements(value, key, map) {
                    v.SelectedIds.push([ key , value ]); // Set New Map Array
                }
                wrongMap.forEach(logMapElements);
            },
            PrintLabel(){
                this.isLoadedSelectedOrders = false;
                let formData = new FormData();
                formData.append('orders', JSON.stringify(this.SelectedIds));
                axios.post(`/api/comp/GetSelectedOrders`, formData)
                .then((res) => {
                    this.SelectedOrders = res.data.data;
                    this.isLoadedSelectedOrders = true;
                    this.$nextTick(() => { this.$htmlToPaper('Invoices'); });
                    
                });
            },
            
        },
        
        watch: {
            Order_id(){
                this.isLoaded_id = false; 
            },
            Account_Id: function() {
                this.fetchArticles();
                this.View = true;
            }
        },
        computed:{
            id_field_valid(){
                return this.IDS.length !== 0
            },
            Anytime(){
                return this.dateFrom !== 'All' && this.dateTo !== 'All'
            },
            NoOrders(){
                return this.orders.length !== 0
            },
            checkedOrdersVal(){
                return this.checkedOrders.length > 0
            }
        }
    };
</script>
<style>
    .TLF {
        text-align: right;
    }
    .ids_inp {
        margin: 12px 0px;text-align: center; border-radius: 4px;
    }
    .uk-navbar-nav li {
        margin: 0px 5px;
    } 
    .div001{ 
        border: 2px solid #ed6464;
        border-radius: 5px;
    }
</style>