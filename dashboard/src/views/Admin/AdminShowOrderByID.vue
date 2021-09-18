<template>
    <div>
        <vcl-table v-if="notFound" :rows="9" :columns="2"></vcl-table>
        
        <div style="margin-bottom: 0px" class="uk-margin" v-else>

                <div v-if="$A_Role == 'admins' || $Account.premissions.includes('Orders_edit')" class="uk-child-width-1-2@s uk-grid-match" uk-grid>
                    <div>
                        <div v-if="updatecheck">

                            <button v-if="miniload" style="padding: 9px;" @click.prevent="" class="btn btn-outline-success del-icon">
                                <span class="fa fa-refresh fa-spin"></span>
                            </button>

                            <button v-else style="padding: 9px;" @click.prevent="createPost()" class="btn btn-outline-success del-icon">
                                <span class="fas fa-check-circle"></span>
                            </button>
                        </div>
                    </div>
                    <div>
                        <div style="display: grid; justify-content: end;">
                            <button v-on:click="Update" style="padding: 9px;" @click.prevent="" class="btn btn-outline-info del-icon">
                                <span class="fas fa-pen"></span>
                            </button>
                        </div>
                    </div>

                </div>


                <div class="uk-child-width-1-2@s uk-grid-match" uk-grid>
                    
                    <div>
                        <div class="uk-card uk-card-default uk-width-1-1">

                            <!--///////////////////////////////////////////////////////// SENDER ////////////////////////////////////////////////////////////-->

                            <div>
                                <div class="uk-card-header">
                                    <div class="uk-grid-small uk-flex-middle" uk-grid>
                                        <div class="uk-width-auto">
                                            <i class="fas fa-user" style="font-size:2rem;color: rgb(255, 85, 147)"></i>
                                        </div>
                                        <div class="uk-width-expand">
                                            <h3 class="uk-card-title uk-margin-remove-bottom">SENDER</h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="uk-card-body uk-child-width-1-2" uk-grid>

                                    <div> <div class="uk-card customUkCard" >Account Type</div> </div>
                                    <div>
                                        <div v-if="customer.account_type == 3" class="uk-card customUkCard1" >Store</div>
                                        <div v-if="customer.account_type == 2" class="uk-card customUkCard1" >User</div>
                                    </div>

                                    <div v-if="customer.account_type == 3"> <div class="uk-card customUkCard" >Store name</div> </div>
                                    <div v-if="customer.account_type == 3"> <div class="uk-card customUkCard1" >{{customer.store_name}}</div> </div>
                                    
                                    <div>
                                        <div class="uk-card customUkCard" >Full name
                                            <div v-if="val_errors" style="color:red">
                                                <font v-for="error in val_errors.sender_full_name" :key="error.id"> ({{error}}) </font>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.sender_full_name"> </div>

                                    <div>
                                        <div class="uk-card customUkCard" >Phone Number
                                            <div v-if="val_errors" style="color:red">
                                                <font v-for="error in val_errors.sender_phone_number" :key="error.id"> ({{error}}) </font>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.sender_phone_number"> </div>

                                    <div>
                                        <div class="uk-card customUkCard" >State
                                            <div v-if="val_errors" style="color:red">
                                                <font v-for="error in val_errors.location_from_state" :key="error.id"> ({{error}}) </font>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.location_from_state"> </div>

                                    <div>
                                        <div class="uk-card customUkCard" >Region
                                            <div v-if="val_errors" style="color:red">
                                                <font v-for="error in val_errors.location_from_region" :key="error.id"> ({{error}}) </font>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.location_from_region"> </div>

                                </div> 
                            </div>
                            <hr class="uk-divider-icon">

                            <!--//////////////////////////////////////////////////////// RECEIVER ///////////////////////////////////////////////////////////-->
                            <div>
                                <div class="uk-card-header">
                                    <div class="uk-grid-small uk-flex-middle" uk-grid>
                                        <div class="uk-width-auto">
                                            <i class="fas fa-user" style="font-size:2rem;color: rgb(47, 206, 36)"></i>
                                        </div>
                                        <div class="uk-width-expand">
                                            <h3 class="uk-card-title uk-margin-remove-bottom">RECEIVER</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-card-body">
                                    <div class="uk-card-body uk-child-width-1-2" uk-grid>
                                    <div>
                                        <div class="uk-card customUkCard" >Full name
                                            <div v-if="val_errors" style="color:red">
                                                <font v-for="error in val_errors.receiver_full_name" :key="error.id"> ({{error}}) </font>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.receiver_full_name"> </div>
                                    </div>

                                    <div>
                                        <div class="uk-card customUkCard" >Phone Number
                                            <div v-if="val_errors" style="color:red">
                                                <font v-for="error in val_errors.reciever_phone_number" :key="error.id"> ({{error}}) </font>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.reciever_phone_number"> </div>
                                    </div>

                                    <div>
                                        <div class="uk-card customUkCard" >State
                                            <div v-if="val_errors" style="color:red">
                                                <font v-for="error in val_errors.location_to_state" :key="error.id"> ({{error}}) </font>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.location_to_state"> </div>
                                    </div>

                                    <div>
                                        <div class="uk-card customUkCard" >Region
                                            <div v-if="val_errors" style="color:red">
                                                <font v-for="error in val_errors.location_to_region" :key="error.id"> ({{error}}) </font>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.location_to_region"> </div>
                                    </div>

                                    <!-- <div>
                                        <div class="uk-card customUkCard" >Receive Date
                                            <div v-if="val_errors" style="color:red">
                                                <font v-for="error in val_errors.recieve_date" :key="error.id"> ({{error}}) </font>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.recieve_date"> </div>
                                    </div> -->

                                    </div>
                                    <!-- <div style="padding:0px;" class="uk-card-body uk-child-width-1-1" uk-grid>
                                        <div>
                                            <GmapMap ref="map" :options="{ mapTypeControl: false,streetViewControl: false,}" :center="{lat:33.3152 ,lng:44.3661}" :zoom="5" map-type-id="terrain" style="width: 100%; height: 287px;box-shadow: 0px 0px 6px #919191;" >
                                            <gmap-polyline :options="{'strokeColor': '#FF0000'}" :path="[markerFrom, markerTo]" ></gmap-polyline>
                                            <gmap-custom-marker :marker="markerFrom"> <img src="/images/OrderLogo.jfif" width="30"> </gmap-custom-marker>
                                            <gmap-custom-marker :marker="current"> <img src="/images/DeliverLogo.jfif" width="30"> </gmap-custom-marker>
                                            <gmap-custom-marker :marker="markerTo"> <img src="/images/ReceiverLogo.jfif" width="30"> </gmap-custom-marker>
                                            </GmapMap>
                                        </div>
                                    </div> -->
                                </div>
                            </div>

                        </div>
                    </div>

                    <!--//////////////////////////////////////////////////////// ORDER ////////////////////////////////////////////////////////////-->
                    <div>
                        <div class="uk-card uk-card-default uk-width-1-1@m">

                            <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto">
                                        <i class="fas fa-box" style="font-size:2rem;color: rgb(115, 166, 228)"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">ORDER</h3>
                                        <p class="uk-text-meta uk-margin-remove-top">{{formatDate(customer.created_at)}}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="uk-card-body uk-child-width-1-2" uk-grid>

                                <div>
                                    <div class="uk-card customUkCard" >Product
                                        <div v-if="val_errors" style="color:red">
                                            <font v-for="error in val_errors.product_name" :key="error.id"> ({{error}}) </font>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.product_name"> </div>

                                <div>
                                    <div class="uk-card customUkCard" >Price
                                        <div v-if="val_errors" style="color:red">
                                            <font v-for="error in val_errors.recieved_price" :key="error.id"> ({{error}}) </font>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.recieved_price"> </div>

                                <div>
                                    <div class="uk-card customUkCard" >Size Cm
                                        <div v-if="val_errors" style="color:red">
                                            <font v-for="error in val_errors.size" :key="error.id"> ({{error}}) </font>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.size"> </div>

                                <div>
                                    <div class="uk-card customUkCard" >Payment method
                                        <div v-if="val_errors" style="color:red">
                                            <font v-for="error in val_errors.payment_method" :key="error.id"> ({{error}}) </font>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.payment_method"> </div>

                                <div>
                                    <div class="uk-card customUkCard" >Order Status
                                        <div v-if="val_errors" style="color:red">
                                            <font v-for="error in val_errors.status" :key="error.id"> ({{error}}) </font>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.status"> </div>

                                <div class="uk-card customUkCard" >Returned order description
                                        <div v-if="val_errors" style="color:red">
                                            <font v-for="error in val_errors.case_details" :key="error.id"> ({{error}}) </font>
                                        </div>
                                    </div>
                                <div class="uk-card customUkCard1"><input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="customer.case_details"></div>


                                <div>
                                    <div class="uk-card customUkCard" >Txt Service</div>
                                </div>
                                <div class="uk-card customUkCard1" > {{customer.txt_service}} IQD</div>

                                <div>
                                    <div class="uk-card customUkCard" >Track code
                                        <div v-if="val_errors" style="color:red">
                                            <font v-for="error in val_errors.track_code" :key="error.id"> ({{error}}) </font>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-card customUkCard1" > {{customer.track_code}} </div>


                                <div> <div class="uk-card customUkCard" >Current location on map</div> </div>
                                <div> <div class="uk-card customUkCard2" >SOON</div> </div>
                            </div>

                            <hr class="uk-divider-icon">

                            <!--Add Partial Form-->
                            <div class="uk-card-header">
                                <h3 class="uk-text-left uk-margin"> Add Partial Refund</h3>
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-expand">
                                        <input class="uk-input uk-text-center" v-model="AddPartialFrm.post_name"  type="text" placeholder="Post Name">
                                    </div>
                                    <div class="uk-width-expand">
                                        <div class="uk-inline uk-width-1-1">
                                            <span class="uk-form-icon" style=" background: #f3f3f3; margin: 1px 1px; ">IQD</span>
                                            <input class="uk-input uk-text-center" v-model="AddPartialFrm.post_price" type="number" placeholder="Post Price">
                                        </div>
                                    </div>
                                    <div class="uk-width-auto">
                                        <button :disabled="!validPR" @click.prevent="AddPartial()" 
                                        class="uk-button uk-button-danger uk-button-medium uk-width-expand" style="border-radius:5px">
                                            <div uk-spinner v-if="miniload"></div>
                                            <div v-else>Add + </div>
                                        </button>
                                    </div>
                                </div>

                                <h3 class="uk-text-left">Refund Posts</h3>

                                <div v-if="customer.partial_refunded.length == 0" class="uk-text-muted uk-text-center">No Partial Posts Refunded</div>
                                <table v-else border="1" class="uk-table uk-table-middle uk-table-divider uk-width-1-1">
                                    <thead>
                                        <tr>
                                            <th class="uk-text-left">Refunded Post</th>
                                            <th class="uk-text-left">Price (IQD)</th>
                                            <th class="uk-text-left">Date</th>
                                        </tr>
                                    </thead> 
                                    <tbody v-for="(PartialRefund) in customer.partial_refunded" :key="PartialRefund.id">
                                        <tr>
                                            <td class="uk-text-left">{{PartialRefund.post_name}}</td>
                                            <td class="uk-text-left">{{PartialRefund.post_price}}</td>
                                            <td class="uk-text-left">{{formatDate(PartialRefund.created_at)}}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>

                            <!-- <div class="uk-card-header">
                                <div class="uk-grid-small uk-flex-middle" uk-grid>
                                    <div class="uk-width-auto">
                                        <i class="fas fa-box" style="font-size:2rem;color: rgb(115, 166, 228)"></i>
                                    </div>
                                    <div class="uk-width-expand">
                                        <h3 class="uk-card-title uk-margin-remove-bottom">Partial Refunds</h3>
                                    </div>
                                </div>
                            </div> -->

                            <!-- <div class="uk-card-body uk-margin">
                                <table border="1" class="uk-table uk-table-middle uk-table-divider uk-width-1-1">
                                    <thead>
                                        <tr>
                                            <th class="uk-text-left">Refunded Post</th>
                                            <th class="uk-text-left">Price (IQD)</th>
                                            <th class="uk-text-left">Date</th>
                                        </tr>
                                    </thead> 
                                    <tbody v-for="(PartialRefund) in customer.partial_refunded" :key="PartialRefund.id">
                                        <tr>
                                            <td class="uk-text-left">{{PartialRefund.post_name}}</td>
                                            <td class="uk-text-left">{{PartialRefund.post_price}}</td>
                                            <td class="uk-text-left">{{formatDate(PartialRefund.created_at)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> -->

                            <hr class="uk-divider-icon">

                            <div class="uk-card-body uk-child-width-1-3" uk-grid>

                                <div> <div class="uk-card customUkCard" >Deliver Fee</div> </div>
                                <div class="uk-card customUkCard1" >{{customer.Deliver_Fee}}</div>
                                <div> <div style="color:green" class="uk-card customUkCard" >IQD</div> </div>

                                <div> <div class="uk-card customUkCard" >App Fee</div> </div>
                                <div class="uk-card customUkCard1" > {{customer.App_Fee}} </div>
                                <div> <div style="color:green" class="uk-card customUkCard" >IQD</div> </div>

                                <div> <div class="uk-card customUkCard" >Credit Invoice</div> </div>
                                <div class="uk-card customUkCard1" > {{customer.Credit_Invoice}} </div>
                                <div> <div style="color:green" class="uk-card customUkCard" >IQD</div> </div>

                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</template>

 <script>
     import GmapCustomMarker from 'vue2-gmap-custom-marker';
     import * as VueGoogleMaps from 'vue2-google-maps'

     Vue.use(VueGoogleMaps, {
        load: {
                key: 'AIzaSyAvWpsePUWUf5VIiRh6xckWdZrmJI-t2i4',
                libraries: 'places',
        },
     }) 

     export default {
          
            components: {
                'gmap-custom-marker': GmapCustomMarker,
            }, 

            data() {
                return {
                    AddPartialFrm:{
                        post_name:'',
                        post_price:'',
                    },
                    updatecheck : 0,
                    disabled: 1, 
                    customer: {}, 
                    markerFrom: { lat: '', lng: '' },
                    current: { lat: '', lng: '' },
                    markerTo: { lat: '', lng: '' },
                    val_errors: {},
                    notFound: true,
                    miniload: false, 
                    get_premissions_arr: []
                }
            },

            created() { 
                this.GetData();
            },

            methods: {

                GetData(){
                    this.axios.get(process.env.VUE_APP_URL+`/api/Admin/Orders/${this.$route.params.id}`)
                    .then((response) => {
                        if (response.status == 203) {
                            this.notFound = true;
                        }
                        else{
                            this.customer = response.data.customer;

                            this.current.lat = parseFloat(response.data.customer.current_location_lat);
                            this.current.lng = parseFloat(response.data.customer.current_location_lng);

                            this.markerFrom.lat = parseFloat(response.data.customer.current_location_from_lat);
                            this.markerFrom.lng = parseFloat(response.data.customer.current_location_from_lng);

                            this.markerTo.lat = parseFloat(response.data.customer.current_location_to_lat);
                            this.markerTo.lng = parseFloat(response.data.customer.current_location_to_lng);

                            this.notFound = false;
                        }
                    }).catch(res => {
                        let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    }); 
                },

                AddPartial(){
                    this.miniload = true;
                    let formData = new FormData();

                    formData.append('order_id', this.$route.params.id);
                    formData.append('track_code', this.customer.track_code);
                    formData.append('post_name', this.AddPartialFrm.post_name);
                    formData.append('post_price', this.AddPartialFrm.post_price);

                    this.axios.post(process.env.VUE_APP_URL+'/api/Admin/AddPartialRefund', formData)

                    .then(res => {
                        this.$toasted.show("Success", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.GetData();
                        this.miniload = false;
                    })

                    .catch(res => {
                            this.val_errors = res.response.data.error;
                            this.miniload = false;
                    });
                },

                formatDate(time) {
                    return new Date(time).toLocaleDateString(['en-iq'], {month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'});
                },

                Update(){
                    this.updatecheck = 1;
                    this.disabled = 0;
                },
                createPost(post) { 
                    this.miniload = true;
                    this.val_errors = false;

                    //UPdate Order
                    this.axios.post(process.env.VUE_APP_URL+`/api/Admin/Orders/${this.$route.params.id}`, this.customer)
                    .then(res => {
                        if (res.status == 202) {
                            let toast = this.$toasted.show("Error", { type : 'error', theme: "bubble",  position: "bottom-center", duration : 2000 });
                            this.val_errors = res.data.errs;
                            this.miniload = false;
                        }
                        if (res.status == 200) {
                            let toast = this.$toasted.show("Order Updated", { type : 'success', theme: "bubble",  position: "bottom-center", duration : 2000 });
                            this.miniload = false;
                        }

                    })
                    .catch(res => {
                        let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.miniload = false;
                    }); 
                },
            },

            computed:{
                validPR(){
                    return this.AddPartialFrm.post_name !== '' && this.AddPartialFrm.post_price !== '' && this.AddPartialFrm.post_price < this.customer.recieved_price
                }
            }
        };
</script>

