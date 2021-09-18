<template>

    <div class="uk-margin">

        <div v-if="$A_Role == 'admins'" class="uk-grid-small uk-flex-middle" uk-grid>
            <div class="uk-width-auto">
                <i class="fas fa-user" style="font-size:2rem;color: #32d296"></i>
            </div>

            <div class="uk-width-expand">
                <h3 class="uk-card-title uk-margin-remove-bottom">{{Account.first_name}} {{Account.last_name}}</h3>
            </div>

            <div v-if="AvailableStack">
                <button style="padding: 9px;" uk-toggle="target: #member-stack" class="btn btn-outline-success del-icon"> 
                    <span class="fas fa-cubes"></span> 
                </button>
            </div>

            <div v-if="updatecheck">
                <button v-if="miniload" style="padding: 9px;" @click.prevent="" class="btn btn-outline-success del-icon">
                    <span class="fa fa-refresh fa-spin"></span>
                </button>

                <button v-else style="padding: 9px;" @click.prevent="createPost()" class="btn btn-outline-success del-icon">
                    <span class="fas fa-check-circle"></span>
                </button>
            </div>

            <div>
                <button v-on:click="Update" style="padding: 9px;" @click.prevent="" class="btn btn-outline-info del-icon"> 
                    <span class="fas fa-pen"></span> 
                </button>
            </div>
        </div>

        <div class="uk-child-width-1-2@s uk-grid-match" uk-grid>

            <div v-if="Account_Role == 'stores' || Account_Role == 'users'">
                <div class="uk-card customUkCard" >Balance
                    <div v-if="val_errors" style="color:red">
                        <font v-for="error in val_errors.balance" :key="error.id"> ({{error}}) </font>
                    </div>
                </div>
            </div>
            <div v-if="Account_Role == 'stores' || Account_Role == 'users'">
                <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="Account.balance"> </div>
            </div>
             
            <div v-if="Account_Role == 'stores' || Account_Role == 'users'">
                <div class="uk-card customUkCard" >Deliver Fee
                    <div v-if="val_errors" style="color:red">
                        <font v-for="error in val_errors.Deliver_Fee" :key="error.id"> ({{error}}) </font>
                    </div>
                </div>
            </div>
            <div v-if="Account_Role == 'stores' || Account_Role == 'users'">
                <div class="uk-card customUkCard1"> 
                    <div v-if="Account.Deliver_Fee == null && disabled == 1"> ----- </div>
                    <input v-else class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="Account.Deliver_Fee"> 
                </div>
            </div>

            <div v-if="Account_Role == 'stores'">
                <div class="uk-card customUkCard" >Store name</div>
            </div>
            <div v-if="Account_Role == 'stores'">
                <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="Account.store_name"> </div>
            </div>
            <div>
                <div class="uk-card customUkCard" >Phone Number</div>
            </div>
            <div>
                <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="Account.phone_number"> </div>
            </div>
            <div>
                <div class="uk-card customUkCard" >Country</div>
            </div>
            <div>
                <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="Account.address_country"> </div>
            </div>
            <div>
                <div class="uk-card customUkCard" >State</div>
            </div>
            <div>
                <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="Account.address_state"> </div>
            </div>

            <div>
                <div class="uk-card customUkCard" >Region</div>
            </div>
            <div>
                <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="Account.address_region"> </div>
            </div>
    
            <div>
                <div class="uk-card customUkCard" >Account unique code</div>
            </div>
            <div>
                <div class="uk-card customUkCard1" > <input class="dbld form-control"  :disabled="disabled == 1" type="text"  v-model="Account.Code"> </div>
            </div>

            <div> <div class="uk-card customUkCard" >Account is confirmed ?</div> </div>
            <div>
                <div v-if="updatecheck">
                    <div class="uk-card customUkCard2"> <input class="dbld uk-checkbox"  :disabled="disabled == 1" type="checkbox"  v-model="Account.confirmed"> </div>
                </div>
                <div v-else>
                    <div v-if="Account.confirmed == 1" class="uk-card customUkCard2" > YES </div>
                    <div v-else class="uk-card customUkCard3" > NO </div>
                </div>
            </div>

        </div>

        <!-- Add Deliver Credit -->
        <div id="member-stack" uk-modal>
            <view-stack :AccountId="Account.id" :Account_Role_Number="Account_Role_Number"></view-stack>
        </div>
    </div>
 
</template>

<script>
    import ViewStack from '../../components/Dashboard/Member_Stack.vue'

    export default {

        props: ['Account', 'Account_Role', 'Account_Role_Number'],

        components: {
            'view-stack': ViewStack,
        },

        data() {
            return {
                miniload: false,
                val_errors: null,
                updatecheck : 0,
                member_stacks: {},
                disabled: 1,
            };
        },

        methods: {

                Update(){
                    this.updatecheck = 1;
                    this.disabled = 0;
                },

                createPost() {

                    this.seccus = false;
                    this.val_errors = false;
                    this.miniload = true;

                    this.axios.post(process.env.VUE_APP_URL+`/api/Admin/Account/${this.Account_Role}/${this.Account.id}`, this.Account)
                        .then(res => {
                            if (res.status == 202) {
                                this.val_errors = res.data.errs;
                                this.$toasted.show("Error", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                                this.miniload = false;
                            }
                            if (res.status == 200) {
                                this.$toasted.show("Updated", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                                this.seccus = true;
                                this.miniload = false;
                                this.updatecheck = 0;
                                this.disabled = 1;
                            }
                        })
                        .catch(res => {
                            console.log(res.response);
                            this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        });
                }, 

        }, 

        computed:{
            AvailableStack(){
                var AvailableRoles = ['delivers','users', 'stores'];
                return AvailableRoles.includes(this.Account_Role)
            },
        }
    };
</script>

<style scoped>
.customer-view {
    display: flex;
    align-items: center;
}
.user-img {
    flex: 1;
}
.user-img img {
    max-width: 160px;
}
.user-info {
    flex: 3;
    overflow-x: scroll;
}
.uk-navbar-nav li {margin: 0px 5px;}
</style>
