<template>

    <div style="margin-bottom: 0px" class="uk-margin">
        
        <view-statistic :FilterOptions="{
            Account_Id:Account_Id, 
            Account_Role_Number:Account_Role_Number, 
            dateFrom:dateFrom, 
            dateTo:dateTo,
            FromState:FromState, 
            ToState:ToState, 
        }"></view-statistic>

        <hr class="uk-divider-icon">

        <!-- Filter -->
        <div class="uk-text-center uk-text-large uk-text-bold uk-text-cario">Filters</div>
        <br>
        <div uk-grid>

            <div class="uk-width-1-5@m">
                <div class="position-relative">
                    <!-- {{dateRange}} -->
                    <date-range-picker 
                        class="uk-width-1-1"
                        :opens="`right`"
                        :linkedCalendars="true"
                        @update="clickApply"
                        v-model="dateRange" 
                        ref="clickCancel"
                    ></date-range-picker>
                    <span class="position-absolute top-0 translate-middle border border-light rounded-circle">
                        <button :disabled="!Anytime" @click.prevent="clickCancel()" id="buttonid2" class="uk-button uk-button-danger">
                            <i class="fas fa-window-close"></i>
                        </button>
                    </span>
                </div>
            </div>

            <div class="uk-width-1-5@m">
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
            </div>

            <div class="uk-width-1-5@m">
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
            </div>

            <div class="uk-width-1-5@m">
                <select @change="onChange($event)" class="uk-select" v-model="status">
                    <option value="All" selected disabled hidden>Select Status</option>
                    <option value="Delay">Delay</option>
                    <option value="waiting">Waiting</option>
                    <option value="pending">Pending</option>
                    <option value="delivered">Delivered</option>
                    <option value="ReturnedToDeliver">Returned To Deliver</option>
                    <option value="ReturnedToClient">Returned To Client</option>
                </select>
            </div>
            
            <div class="uk-width-1-5@m">
                <form v-on:submit.prevent="Searching" class="uk-search uk-search-default uk-width-1-1@m">
                    <a href="#" class="uk-search-icon-flip" uk-search-icon></a>
                    <input class="uk-search-input" v-model="search_data" style="border-radius: 5px; background: white;" type="search" placeholder="Search">
                </form>
            </div>

        </div>

        <hr class="uk-divider-icon">

        <div class="uk-text-center uk-text-large uk-text-bold uk-text-cario">Options</div>
        <br>

        <div>

            <div uk-grid>

                <div class="uk-width-1-3@m" v-if="Permissions_Val('Orders_Change status by excel')">
                    <button id="buttonid" :disabled="miniload" class="uk-button uk-button-danger uk-button-large uk-width-expand" style="border-radius:5px">
                        <div v-if="up_miniload">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <div v-else>
                            <i class="fas fa-arrow-circle-up"></i>
                            Change status by excel
                        </div>
                    </button>
                    <input id="fileid" ref="file" type="file" v-on:change="submitFile()" hidden/>
                </div>

                <div class="uk-width-1-3@m" v-if="Permissions_Val('Orders_Print Invoices')">
                    <button @click.prevent="PrintLabel()" :disabled="checkedOrders.length == 0 || miniload" class="uk-button uk-button-danger uk-button-large uk-width-expand" style="border-radius:5px">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Print Invoices
                    </button>
                </div>
                
                <div class="uk-width-1-3@m" v-if="Permissions_Val('Orders_Print Single Invoice')">
                    <form v-on:submit.prevent="Single_Order_Printer" class="uk-search uk-search-default uk-width-1-1@m">
                        <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: print"></span>
                        <input v-bind:class="{'uk-form-danger' : !valid_id_single && isLoaded_id, 'uk-form-success ' : valid_id_single && isLoaded_id}" class="uk-input uk-text-center uk-background-default" type="text" placeholder="Single Order Invoice" v-model="Single_Invoice_Order_id" style="height: 54px;"> 
                    </form>
                </div>

                <div class="uk-width-1-3@m" v-if="Permissions_Val('Orders_Download Excel')">
                    <button @click.prevent="DonloadExcel()" :disabled="checkedOrders.length == 0 || miniload" class="uk-button uk-button-danger uk-button-large uk-width-expand" style="border-radius:5px">
                        <i class="fas fa-file-download"></i>
                        Download Excel
                    </button>
                </div>

                <div class="uk-width-1-3@m" v-if="Permissions_Val('Orders_Change status by selector')">
                    <select @change="ChangeSelectedOrdersStatus()" :disabled="checkedOrders.length == 0 || miniload" class="uk-select uk-form-large uk-button-danger" v-model="selected_status">
                        <option class="uk-text-white" value="default" hidden>Change status to</option>
                        <option class="uk-text-white" value="waiting">Waiting</option>
                        <option class="uk-text-white" value="pending">Pending</option>
                        <option class="uk-text-white" value="ReceivedByHodHod">Received By HodHod</option>
                        <option class="uk-text-white" value="delivered">Delivered</option>
                        <option class="uk-text-white" value="ReturnedToDeliver">Returned To Deliver</option>
                        <option class="uk-text-white" value="ReturnedToClient">Returned To Client</option>
                    </select>
                </div>

                <div v-if="$A_Role !== 'companies'" class="uk-width-expand@m">
                    <select :disabled="checkedOrders.length == 0 || miniload" @change="TransferOrder($event)" class="uk-select uk-form-large uk-button-danger" v-model="select_companie">
                        <option class="uk-text-white" value="default" hidden>Transfer To</option>
                        <option class="uk-text-white" v-for="store in companies" :key="store" v-bind:value="store" >{{store.name}}</option>
                    </select>
                </div>

            </div>

        </div>

        <hr class="uk-divider-icon">

        <div class="uk-text-center uk-text-large uk-text-bold uk-text-cario">Multiple Selector</div>
        <br>

        <div uk-grid>
            <div class="uk-width-auto@m">
                <button @click.prevent="earse" :disabled="!earse_btn" class="uk-button uk-button-default uk-button-large uk-width-expand" style="border-radius:5px">
                    <i class="fas fa-eraser uk-text-large uk-text-bold"></i>
                </button>
            </div>
            <div class="uk-width-expand@m">
                <form v-on:submit.prevent="Searching_id">
                    <input v-bind:class="{'uk-form-danger' : !valid_id && isLoaded_id, 'uk-form-success ' : valid_id && isLoaded_id}" class="uk-input uk-text-center uk-background-default" type="text" placeholder="Enter Order Id" v-model="Order_id" style="height: 54px;"> 
                </form>
            </div>
        </div>

        <div class="uk-margin uk-child-width-1-4@m uk-child-width-1-1@s uk-text-center" uk-grid>
            <div v-for="(order_id) in SelectedIds" :key="order_id.id">
                <div class="uk-card uk-card-default uk-padding-small uk-inline uk-width-1-1">
                    <div class="uk-text-large uk-text-bold uk-text-center">{{order_id[1].track_code}}</div>
                    <div class="uk-position-top-right">
                        <button @click.prevent="RemoveId(order_id[0], order_id[1])" class="uk-button uk-button-danger uk-button-small uk-width-expand" style="border-radius:0px 0px 0px 10px;"> x </button>
                    </div>
                </div>
            </div>
        </div>

        <hr class="uk-divider-icon">

        <vcl-table v-if="!isLoaded" :rows="9" :columns="5"></vcl-table>

        <div v-else>
            <table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-center uk-table-responsive">
                <thead>
                    <tr>
                        <th v-if="Permissions_Val('Orders_checkbox')"><input class="uk-checkbox" type="checkbox" @click="select_all" :disabled="orders.data.length == 0 || miniload || !checkbox_av"></th>
                        <th class="uk-table-shrink"> <li class="fa fa-gear"></li> </th>
                        <th class="uk-table-shrink">Code</th>
                        <th>Shipment</th>
                        <th>Sender name</th>
                        <th>Sender phone</th>
                        <th>Receiver name</th>
                        <th>Receiver phone</th>
                        <th>price</th>
                        <th>Last Status</th>
                        <th>Creation date</th>
                        <th>Received By HodHod</th>
                        <th class="uk-table-shrink">dates</th>
                    </tr>
                </thead> 

                <tbody v-for="(order) in orders.data" v-bind:key="order.id">
                    <tr>
                        <td v-if="Permissions_Val('Orders_checkbox')">
                            <div v-if="order.status !== 'delivered'"><input :disabled="!checkbox_av" class="uk-checkbox" type="checkbox" :value="order" v-model="checkedOrders"></div>
                            <div v-else>
                                <i class="fas fa-check-square" style="font-size: 20px;"></i>
                                <div uk-dropdown="pos: right-center"> تم توصيل البريد </div>
                            </div>
                        </td>
                        <td>
                            <router-link :to="`/order_details/${order.id}`">
                                <button class="btn btn-outline-info"><span class="fa fa-eye"></span></button>
                                <button v-if="$A_Role == 'admins'" @click.prevent="DeleteOrder(order)" class="btn btn-outline-danger del-icon">
                                    <span class="fa fa-trash-o"></span>
                                </button>                            
                            </router-link>
                        </td>
                        <td>{{ order.track_code }}</td>                      
                        <td>{{ order.product_name }}</td>
                        <td>{{ order.sender_full_name }}</td>
                        <td>{{ order.sender_phone_number }}</td>
                        <td>{{ order.receiver_full_name }}</td>
                        <td>{{ order.reciever_phone_number }}</td>
                        <td>{{ order.recieved_price }}</td>
                        <td>{{ order.status }}</td>
                        <td>{{formatDate(order.created_at)}}</td>
                        <td>
                            <span v-if="order.sent_to_hodhod">YES</span>
                            <span v-else>NO</span>
                        </td>
                        <td>
                            <button @click.prevent="ViewDetails(order.id)" class="btn btn-outline-info" uk-toggle="target: #ViewHistory">
                                <span class="far fa-calendar-alt"></span>
                            </button>
                        </td> 
                    </tr>
                </tbody>
            </table>
        </div>

        <empty-result :Data="orders.data"></empty-result>
        <paginator class="uk-padding-large" v-on:childToParent="pagination" :data="orders"></paginator>

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
                        <tbody v-for="(order) in order_history" v-bind:key="order.id">
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
            <div class="uk-card uk-card-default uk-width-1-1 uk-margin-large-top div001" v-for="(order) in SelectedOrders" v-bind:key="order.id">
                <div class="uk-flex-middle" uk-grid>
                        <div class="uk-width-1-1">
                            <div class="uk-inline">

                                <img src="/keyan.jpg" width="1800" height="1200" alt="">

                                <div class="uk-position-top uk-overlay" style="left: 400px !important; right: initial !important;">
                                    <img :src="order.barcode" width="400" style="height: 100px;" />
                                </div>

                            </div>
                        </div>
                        <br>
                        <div class="uk-width-1-1 mt-3">
                            <div class="uk-child-width-1-3" uk-grid>
                                <div><h2 class="uk-text-left uk-text-cario uk-text-bold ps-3">رمز الطلب : {{order.track_code}}</h2></div>
                                <div><h2 class="uk-text-left uk-text-cario uk-text-bold ps-3"> رقم الوصل : {{l_counter}}</h2></div>
                                <div><h2 class="uk-text-right uk-text-cario uk-text-bold pe-2" dir="rtl">التاريخ : {{formatDatekyan(order.created_at)}}</h2></div>
                            </div>
                        </div>

                        <div class="uk-width-1-1">
                            <div class="uk-grid-collapse uk-grid-match" uk-grid>

                                <div class="uk-width-1-5">
                                    <div class="">
                                        <div class="p-3 m-1 bg-dark text-white rounded-3 uk-text-large uk-text-bold">ملاحضات مندوب الشركة</div>
                                        <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark" style="height: 208px;"></div>
                                        <div class="p-3 m-1 bg-dark text-white rounded-3 uk-text-large uk-text-bold" style="background: #6b3a8c">توقيع الزبون على استلام المنتج كامل وسليم</div>
                                    </div>
                                </div>

                                <div class="uk-width-4-5">
                                    <div class="uk-grid-collapse" uk-grid>
                                        <div class="uk-width-expand">
                                            <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark uk-text-large uk-text-bold">6126 / Whatsapp : 07509049155</div>
                                        </div>
                                        <div class="uk-width-auto">
                                            <div class="p-3 m-1 bg-dark text-white rounded-3 uk-text-large uk-text-bold" style="background: #6b3a8c">رقم المرسل</div>
                                        </div>
                                        <div class="uk-width-expand">
                                            <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark uk-text-large uk-text-bold">{{order.sender_full_name}}</div>
                                        </div>
                                        <div class="uk-width-auto">
                                            <div class="p-3 m-1 bg-dark text-white rounded-3 uk-text-large uk-text-bold" style="background: #6b3a8c">المحل / المرسل</div>
                                        </div>
                                    </div>

                                    <div class="uk-grid-collapse uk-grid-match" uk-grid>

                                        <div class="uk-width-1-5">
                                            <div class="p-3 m-1 rounded-3 border border-2 border-dark uk-text-large uk-text-bold">
                                                {{order.reciever_phone_number}}
                                            </div>
                                        </div>
                                        <div class="uk-width-1-5">
                                            <div class="bg-dark uk-text-white m-1 rounded-3">
                                                <div class="uk-text-large uk-text-bold mt-4 text-white uk-text-center">رقم</div>
                                                <div class="uk-text-large uk-text-bold text-white uk-text-center ">المستلم</div>
                                            </div>
                                        </div>
                                        <div class="uk-width-3-5">
                                            <div class="uk-grid-collapse" uk-grid>
                                                <div class="uk-width-expand">
                                                    <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark uk-text-large uk-text-bold">{{order.receiver_full_name}}</div>
                                                </div>
                                                <div class="uk-width-auto">
                                                    <div class="p-3 m-1 bg-dark text-white rounded-3 uk-text-large uk-text-bold">اسم المستلم</div>
                                                </div>
                                            </div>

                                            <div class="uk-grid-collapse" uk-grid>
                                                <div class="uk-width-expand">
                                                    <div class="p-3 m-1 text-white rounded-3 border border-2 border-dark">.</div>
                                                </div>
                                                <div class="uk-width-auto">
                                                    <div class="p-3 m-1 bg-dark text-white rounded-3 uk-text-large uk-text-bold" style="background: #6b3a8c">ملاحضات التسليم</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>

                                    <div class="uk-grid-collapse" uk-grid>
                                        <div class="uk-width-expand">
                                            <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark uk-text-large uk-text-bold">{{order.location_to_state}} {{order.location_to_region}}</div>
                                        </div>
                                        <div class="uk-width-auto">
                                            <div class="p-3 m-1 bg-dark text-white rounded-3 uk-text-large uk-text-bold">عنوان المستلم</div>
                                        </div>
                                    </div>

                                    <div class="uk-grid-collapse" uk-grid>
                                        <div class="uk-width-expand">
                                            <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark uk-text-large uk-text-bold">{{order.quantity}}</div>
                                        </div>
                                        <div class="uk-width-auto">
                                            <div class="p-3 m-1 bg-dark text-white rounded-3 uk-text-large uk-text-bold">العدد</div>
                                        </div>
                                        <div class="uk-width-expand">
                                            <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark uk-text-large uk-text-bold">{{order.product_name}}</div>
                                        </div>
                                        <div class="uk-width-auto">
                                            <div class="p-3 m-1 bg-dark text-white rounded-3 uk-text-large uk-text-bold" style="background: #6b3a8c">النوع</div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="uk-grid-collapse" uk-grid>
                                <div class="uk-width-expand">
                                    <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark uk-text-large uk-text-bold">{{parseInt(order.recieved_price) + parseInt(order.Deliver_Fee)}} </div>
                                </div>
                                <div class="uk-width-auto">
                                    <div class="p-3 m-1 bg-dark text-white rounded-3 uk-text-large uk-text-bold" style="background: #6b3a8c">كتابتا</div>
                                </div>
                                <div class="uk-width-expand">
                                    <div class="p-3 m-1 text-dark rounded-3 border border-2 border-dark uk-text-large uk-text-bold">{{parseInt(order.recieved_price) + parseInt(order.Deliver_Fee)}}</div>
                                </div>
                                <div class="uk-width-auto">
                                    <div class="p-3 m-1 bg-dark text-white rounded-3 uk-text-large uk-text-bold" style="background: #6b3a8c">رقما</div>
                                </div>
                                <div class="uk-width-auto">
                                    <div class="p-3 m-1 bg-dark text-white rounded-3 uk-text-large uk-text-bold">المبلغ الكلي مع التوصيل</div>
                                </div>
                            </div>

                            <div class="uk-width-expand">
                                <div class="p-3 m-1 bg-dark text-white rounded-3 border border-2 border-dark" uk-grid>
                                    <div class="uk-width-1-3 text-white uk-text-large uk-text-bold"> يسقط حق المطالبة بتفاصيل الوصل (المبلغ او البضاعة) بعد 2 اشهر</div>
                                    <div class="uk-width-1-3 text-white uk-text-large uk-text-bold">لا يعتمد وصل الحساب بدون ختم الشركة</div>
                                    <div class="uk-width-1-3 text-white uk-text-large uk-text-bold">الشركة طرف ناقل فقط وغير مسزولة عن جودة او نوع البضاعة</div>
                                </div>
                            </div>

                        </div>
                </div>
            </div>

        </div>

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
            'https://cdn.jsdelivr.net/npm/uikit@3.6.20/dist/css/uikit.min.css',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css',
        ]
    }

    import VueHtmlToPaper from 'vue-html-to-paper';
    Vue.use(VueHtmlToPaper, options);

    import DateRangePicker from 'vue2-daterange-picker'
    import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

    import ViewStatistic from '../../components/Dashboard/Member_Statistic'
    export default {

        props: {
            Account_Id: {
                default: 'All'
            },
            Account_Role_Number: {
                default: 'All'
            }
        },

        components: {
            DateRangePicker,
            'view-statistic': ViewStatistic,
        },

        data(){
          return {
            companies:{},
            dateRange: { startDate: '', endDate: '' },
            orders: {},
            order_history: {},
            status: 'All',
            selected_status: 'default',
            dateFrom: 'All',
            l_counter: '',
            dateTo: 'All',
            FromState: 'All',
            ToState: 'All', 
            search_data: '', 
            is_valid:false,
            isLoaded:false,
            isLoadedHistory:false,
            miniload: false,
            up_miniload: false,
            View:false,
            file: '',
            checkedOrders: [],
            SelectedOrders: [],
            page : 1,
            get_premissions_arr: [],
            all_selected: false,
            SelectedIds : [],
            Order_id: '',
            Counter: 0,
            valid_id:null,
            valid_id_single:null,
            isLoaded_id:false,
            checkbox: true,
            select_companie: 'default',
            Single_Invoice_Order_id: ''
          }
        }, 

        created(){
            this.fetchArticles();  
            this.get_companies();
        }, 

        watch: {

            Account_Id: function() {
                this.fetchArticles();
                this.View = true;
            }
        },

        methods: {
            
            Single_Order_Printer(){
                
                this.isLoaded_id = false;
                var v = this;
                if(this.Single_Invoice_Order_id !== ''){ 
                   this.axios.get(process.env.VUE_APP_URL+`/api/Admin/check_order_id/${this.Single_Invoice_Order_id}`)
                    .then(res => {
                        this.SelectedOrders = [],
                        this.SelectedOrders.push(res.data); 
                        this.SelectedOrders.push(res.data); 
                        this.SelectedOrders.push(res.data); 

                        this.FT();
                        this.HandleOrder(res.data);
                        this.valid_id_single = 1;
                        this.isLoaded_id = true;
                        this.Single_Invoice_Order_id = '';

                        //Change Order Status
                        let formData = new FormData();
                        formData.append('status', 'pending');
                        formData.append('order_ids', JSON.stringify([res.data]));
                        this.axios.post(process.env.VUE_APP_URL+'/api/Admin/ChangeSelectedOrdersStatus', formData)
                        .then(res => {
                            this.$htmlToPaper('Invoices');
                        })

                        

                        // this.$nextTick(() => { this.$htmlToPaper('Invoices'); });

                    }).catch(res => {
                        this.valid_id_single = 0;
                        this.isLoaded_id = true;
                    });
                }
            },

            formatDatekyan(time) {
                return new Date(time).toLocaleDateString(['en-iq'], {month: '2-digit', day: '2-digit', year: 'numeric',});
            },

            HandleOrder(order_id){
                this.miniload = true;
                let formData = new FormData();

                formData.append('company_id', parseInt(this.$Account.id));
                formData.append('order_ids', JSON.stringify([order_id]));


                this.axios.post(process.env.VUE_APP_URL+'/api/Admin/Transfer_order', formData)

                .then(res => {
                    this.miniload = false;
                })

                .catch(res => {
                    this.miniload = false;
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

            TransferOrder(){

                this.$confirm(this.select_companie.name+` تغيير شركة الاستلام الى `).then(() => {

                    this.miniload = true;
                    let formData = new FormData();

                    formData.append('company_id', this.select_companie.id);
                    formData.append('order_ids', JSON.stringify(this.checkedOrders));


                    this.axios.post(process.env.VUE_APP_URL+'/api/Admin/Transfer_order', formData)

                    .then(res => {
                        this.$toasted.show("Success", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.get_companies();
                        this.fetchArticles();
                        this.miniload = false;
                    })

                    .catch(res => {
                            this.val_errors = res.response.data.error;
                            this.miniload = false;
                    });

                    this.selected_status = 'default'
                });
                
            },

            earse(){
                this.checkedOrders = [];
                this.SelectedIds = [];
            },

            RemoveId(index, checkbox){
                var v = this;
                let wrongMap = new Map(this.SelectedIds);
                
                v.SelectedIds = [];
                wrongMap.delete(index);
                
                function logMapElements(value, key, map) {
                    v.SelectedIds.push([ key , value ]); // Set New Map Array
                }
                wrongMap.forEach(logMapElements);

                v.checkedOrders.pop(checkbox);
            },

            Searching_id(){
                
                this.isLoaded_id = false;
                var v = this;
                if(this.Order_id !== ''){ 
                   this.axios.get(process.env.VUE_APP_URL+`/api/Admin/check_order_id/${this.Order_id}`)
                    .then(res => {
                        
                        this.checkedOrders.push(res.data); 
                        
                        this.Counter = this.Counter+1;
                        let wrongMap = new Map();
                        
                        wrongMap.set(this.Counter, this.Order_id );
                        function logMapElements(value, key, map) {
                            v.SelectedIds.push([key, res.data]); 
                        }
                        
                        wrongMap.forEach(logMapElements);
                        this.valid_id = 1;
                        this.isLoaded_id = true;

                        this.Order_id = '';

                    }).catch(res => { 
                        this.valid_id = 0;
                        this.isLoaded_id = true;
                    }); 
                }
            },

            PrintLabel(){
                this.isLoadedSelectedOrders = false;
                let formData = new FormData();
                formData.append('orders', JSON.stringify(this.checkedOrders));
                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/PrintLabels`, formData)
                .then((res) => {
                    this.SelectedOrders = res.data.data;
                    this.isLoadedSelectedOrders = true;
                    this.$nextTick(() => { this.$htmlToPaper('Invoices'); });
                });
            },

            DeleteOrder(order){
                this.$confirm(`
                    يرجى الملاحضة بأن عملية حذف طلب حساسة 
                    ولايفضل استخدامها سوى للطلبات الجديدة والتي لم يتم اجراء عليها اي عملية توصيل او تحديث او غيرها من العمليات
                    `,
                    `${order.track_code} حذف الطلب`   
                ).then(() => {
                    this.miniload = true;
                    let formData = new FormData();
                    formData.append('order_id', order.id);

                    this.axios.post(process.env.VUE_APP_URL+`/api/Admin/Delete_Orders`, formData)

                    .then(res => {
                        this.$toasted.show("Success", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.fetchArticles();
                        this.miniload = false;
                    })
                    .catch(res => {
                            this.val_errors = res.response.data.error;
                            this.miniload = false;
                    });
                });
            },

            submitFile(){
                this.up_miniload = true;
                let formData = new FormData();
                formData.append('file', this.$refs.file.files[0]);
                this.axios.post(process.env.VUE_APP_URL+'/api/Admin/ChangeOrdersStatusByExcel', formData,{ headers: { 'Content-Type': 'multipart/form-data' } })
                .then(res => {
                    this.$toasted.show("Success", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.fetchArticles();
                    this.up_miniload = false;
                    this.file = '';
                })
                .catch(res => {
                    this.val_errors = res.response.data.error;
                    this.up_miniload = false;
                });
            },

            select_all(){

                var v = this;
                
                if(!v.all_selected){
                    v.checkedOrders = [];
                    v.all_selected = true;
                    v.orders.data.forEach(function(user) { v.checkedOrders.push(user); });
                }
                else{
                    v.checkedOrders = [];
                    v.all_selected = false;
                    v.orders.data.forEach(function(user) { v.checkedOrders.pop(user); });
                }

            },

            pagination(page) {
                this.page = page;
                this.fetchArticles();
            },

            fetchArticles(){
                this.isLoaded = false;
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/Orders/${this.Account_Role_Number}/${this.Account_Id}/${this.status}/${this.dateFrom}/${this.dateTo}/${this.FromState}/${this.ToState}?page=${this.page}`)
                .then(res => {
                    this.isLoaded = true;
                    this.orders = res.data; 
                    this.awaitingSearch = false; 
                })
                .catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            formatDate(time) {
                return new Date(time).toLocaleDateString(['en-iq'], {month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'});
            },

            ViewDetails(order_id){
                this.isLoadedHistory = false;
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/order_status_history/${order_id}`)
                .then(res => {
                    if (res.status == 200) {
                        this.order_history = res.data;
                        this.isLoadedHistory = true;
                    }
                });
            },

            onChange(event) {
                this.status = event.target.value;
                this.checkedOrders = [];
                this.fetchArticles();
            },

            clickCancel() {
                this.dateTo = 'All';
                this.dateFrom = 'All';
                this.dateRange.startDate = '';
                this.dateRange.endDate = '';
                this.dateFrom = 'All';
                this.$refs.clickCancel.clickCancel()
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
                formData.append('orders', JSON.stringify(this.checkedOrders));

                this.axios({
                    url: process.env.VUE_APP_URL+`/api/Admin/orders/DownloadExcel`,
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
                })
                .catch(res => {
                    this.miniload = false;
                });

            },

            ChangeSelectedOrdersStatus(alert){

            
                this.$confirm(this.selected_status+` تغيير حالة الطلبات المحددة الى `).then(() => {

                    this.miniload = true;
                    let formData = new FormData();

                    formData.append('status', this.selected_status);
                    formData.append('order_ids', JSON.stringify(this.checkedOrders));

                    this.axios.post(process.env.VUE_APP_URL+'/api/Admin/ChangeSelectedOrdersStatus', formData)

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

                    this.selected_status = 'default'
                });
                
            },

            Searching(){
                this.isLoaded = false;
                if(this.search_data == ''){ this.fetchArticles(1); }
                else{
                    this.axios.get(process.env.VUE_APP_URL+`/api/Admin/search_for_order/${this.Account_Role_Number}/${this.Account_Id}/${this.search_data}`)
                    .then(res => {
                        if (res.status == 200) {
                            this.orders = res.data;
                            this.isLoaded = true;
                        }
                    }).catch(res => { this.isLoaded = true;});
                }
            },

            FT(){
                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/FT`)
                .then(res => {
                    // this.$toasted.show("Success", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.l_counter = res.data;
                }).catch(res => { this.isLoaded = true;});

            },

            Permissions_Val(value){
                if(this.$A_Role == 'admins' || this.$Account.premissions.includes(value)){
                    return true;
                }
                return false;
            },

            clickApply(){
                this.dateFrom = this.dateRange.startDate.toISOString().substr(0,10);
                this.dateTo = this.dateRange.endDate.toISOString().substr(0,10);
                this.fetchArticles();
            },

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
            },

            checkbox_av(){
                return (this.SelectedIds.length > 0) ? false : true;
            },

            earse_btn(){
                return (this.SelectedIds.length > 0) ? true : false;
            }
        },

        mounted() {
            document.getElementById('buttonid').addEventListener('click', openDialog);
            function openDialog() {
                document.getElementById('fileid').click();
            }
            
            //disable checkbox on multiple selection
            if(this.SelectedIds.length > 0 ) {
                this.checkbox = false;
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
    .reportrange-text{
        padding: .375rem .75rem !important;
    }
    select {
        border-radius: 6px !important;
    }
    #buttonid2{
        border-radius:5px;line-height:0px;padding:5px
    }
    .bg-dark{
        background: #6b3a8c !important;
    }
    .uk-position-top{
        left: 355px !important;
        right: initial !important;
    }
</style>
