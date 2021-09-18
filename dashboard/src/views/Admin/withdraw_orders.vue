<template>

    <div class="uk-margin">
        <!-- /////////////////////////////////// LISTING ////////////////////////////////////  -->
        <nav style="margin: 20px 0px;" uk-navbar> 
            <div class="uk-navbar-right">
                <ul class="uk-navbar-nav">
                    <li>
                        <select @change="onChange($event)" class="uk-select" v-model="status">
                            <option value="0">On Hold</option>
                            <option value="1">Repayed</option>
                        </select>
                    </li>
                </ul>
            </div>
        </nav>
        <br><br>
        <vcl-table v-if="!isLoaded" :rows="9" :columns="5"></vcl-table>
        <div v-else>
            <table border="0" class="table cust-table uk-card uk-card-default">
                <thead>
                    <tr style="">
                        <th style="width:80px;">select</th>
                        <th style="width:80px;">#</th>
                        <!-- <th style="width:200px;" class="text-center">
                            <li class="fa fa-gear"></li>
                        </th> -->
                        <th style="width:150px;">Name</th>
                        <th style="width:200px;">balance</th>
                        <th style="width:100px;">Method</th>
                        <th style="width:200px;">Card/Phone Number</th>
                        <th style="width:200px;">Date</th>
                    </tr>
                </thead>  
                <tbody v-for="(order) in orders" :key="order.id">
                    <tr>
                        <th style="width:80px;"><input class="uk-checkbox" :value="order.id" type="checkbox" v-model="checkedNames"></th>
                        <th style="width:80px;">{{ order.id }}</th>
                        <!-- <td style="width:200px;" class="text-center">
                            <button class="btn btn-outline-info" type="button" @click.prevent="ViewAccount(order)" uk-toggle="target: #ViewAccount">
                                <span class="fas fa-user-tie"></span>
                            </button>
                        </td> -->
                        <td style="width:150px;">{{ order.Member.first_name }} {{ order.Member.last_name }}</td>
                        <td style="width:200px;">{{ order.balance }}</td>
                        <td style="width:100px;">{{ order.withdraw_method }}</td>
                        <td style="width:200px;">{{ order.card_or_phone_number }}</td>
                        <td style="width:200px;">{{ new Date(order.created_at).toJSON().slice(0,16).replace(/-/g,'/').replace(/T/g,' ') }}</td>
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
            <br>

            <div class="row Departmain">
                <div class="col-sm-12 Previous" align="center">
                    <button v-if="Allow && Permissions_Val('Withdraw Orders_withdrawing')" :disabled="!isValid" @click.prevent="Withdraw(checkedNames)" class="btn btn-outline-success del-icon">Withdraw</button>
                    <button v-if="Permissions_Val('Withdraw Orders_download excel')" :disabled="!isValid" @click.prevent="DownloadExcel()"  class="btn btn-outline-success"><span class="fas fa-download"></span></button>
                </div>
            </div>
        </div> 

        <!-- VIEW -->
        <!-- <div id="ViewAccount" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <view-account :Account="ViewAccountData.Member" :Account_Role="ViewAccountData.Member_Role" :Account_Role_Number="ViewAccountData.Member_Role_Number"></view-account>
            </div>
        </div>  -->

    </div>
</template>


<script>
    // import ViewAccount from '../../components/Dashboard/ViewAndManageAccount.vue'

    export default {

        props: {
            Account_Id: {
                default: 'All'
            },
            Account_Role: {
                default: 'All'
            }
        },

        components: {
            // 'view-account': ViewAccount, 
        },

        data(){
          return {
            orders: {},
            status: '0',
            current_page: 1,
            isLoaded2: false,
            isLoaded: false,
            miniload: false,
            checkedNames: [],
            ViewAccountData: {
                Member: {
                    id:null
                }
            }, 
          }
        },

        created(){
          this.fetchArticles();
        },

        watch:{
            Account_Id: function() {
                this.fetchArticles();
                this.View = true;
            }
        },
        methods: {
            fetchArticles(){
                this.axios.get(process.env.VUE_APP_URL+`/api/withdraw_orders/${this.Account_Id}/${this.Account_Role}/${this.status}?page=${this.current_page}`)
                .then(res => {
                    this.isLoaded = true;
                    this.orders = res.data; 
                })
                .catch(err => console.log(err));
            },

            Next() { this.current_page = this.current_page+1; this.fetchArticles(); },

            Previous() { this.current_page = this.current_page-1; this.fetchArticles(); },

            async Withdraw(checkedNames) {

                this.miniload = true;
                await this.DownloadExcel();

                this.axios.post(process.env.VUE_APP_URL+`/api/Accept_withdraw`, checkedNames)
                    .then(res => {
                    if (res.status == 200) {
                        this.fetchArticles();
                        this.miniload = false;
                    }
                    else{
                        let toast = this.$toasted.show("Error", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.miniload = false;
                    }
                }).catch(err => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.miniload = false;
                })

            },

            async DownloadExcel(){
                this.miniload = true; 

                let formData = new FormData();
                formData.append('status', 0);
                formData.append('names', JSON.stringify(this.checkedNames));

                // Download STACK
                this.axios.post(process.env.VUE_APP_URL+`/api/DownloadWithdrawOrders`, formData, { responseType: 'blob' })
                .then((response) => {
                    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    var fileLink = document.createElement('a');

                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', Date()+'.xlsx');
                    document.body.appendChild(fileLink);
                    fileLink.click();
                    this.miniload = false; 
                });
            }, 

            onChange(event) {
                this.status = event.target.value;
                this.checkedNames = [];
                this.fetchArticles();
            },

            ViewAccount(Infos){
                this.ViewAccountData = Infos;
            },

            Permissions_Val(rule){
                if(this.$A_Role == 'admins' || this.$Account.premissions.includes(rule)){
                    return true;
                }

                return false;
            },
        },

        computed: {
            isValid() {
                if (this.checkedNames == 0) {
                    return false;
                }
                else{
                    return true;
                }
            },

            Allow() { return this.status == '0' },

            CDate(IDate) {
                new Date(IDate).toJSON().slice(0,16).replace(/-/g,'/').replace(/T/g,' ')
            }
        }
    };
</script>
