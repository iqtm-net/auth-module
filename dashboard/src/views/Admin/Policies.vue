<template>

    <div style="margin-bottom: 0px" class="uk-margin">
        <!-- /////////////////////////////////// LISTING ////////////////////////////////////  -->
        <table border="0" class="table cust-table uk-card uk-card-default">
            <thead>
                <tr style="">
                    <th style="width:80px;">#</th>
                    <th style="width:200px;" v-if="Permissions_Val('Policies_delete')" class="text-center">
                        <li class="fa fa-gear"></li>
                    </th>
                    <th style="width:200px;">title</th>
                    <th style="width:1000px;">body</th>
                    <th style="width:100px;" v-if="Permissions_Val('Policies_add')">
                        <button class="btn btn-outline-info" style="color: #04bacc;" uk-toggle="target: #modal-example">
                            <span class="fas fa-plus-circle"></span>
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody v-if="!isLoaded" class="tbdy">
                <tr>
                    <td style="width:80px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:200px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:200px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:1000px;"><i class="fa fa-refresh fa-spin"></i></td>
                    <td style="width:100px;"></td>
                </tr>
            </tbody>
            <tbody v-for="(order) in orders.data" :key="order.id">
                <tr>
                    <th style="width:80px;">{{ order.id }}</th>
                    <td style="width:200px;" v-if="Permissions_Val('Policies_delete')" class="text-center">
                        <button @click.prevent="DeleteArticle(order.id)" class="btn btn-outline-danger del-icon">
                            <span class="fa fa-trash-o"></span>
                        </button>
                    </td>
                    <td style="width:200px;">{{ order.title }}</td>
                    <td style="width:1000px;">{{ order.body }}</td>
                    <td style="width:100px;" v-if="Permissions_Val('Policies_add')"></td>
                </tr>
            </tbody>
        </table>

        <br><br>

        <div id="modal-example" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <h2 style="text-align: center; height:48px;  border-radius: 4px;" class="uk-modal-title">Add Policy</h2>

                <form class="uk-form-horizontal uk-margin-larg">
                    <div class="">
                        <label class="uk-form-label" for="form-horizontal-text" style="text-align: center;">Title</label>
                        <div class="uk-form-controls">
                            <input id="form-horizontal-text" v-model="new_policy.title" class="uk-input" style="text-align: center; border-radius: 4px;" type="text">
                        </div>
                    </div>
                    <br>
                    <div class="">
                        <label class="uk-form-label" for="form-horizontal-text" style="text-align: center;">Body</label>
                        <div class="uk-form-controls">
                            <textarea id="form-horizontal-text" v-model="new_policy.body" class="uk-input" style="height:90px;text-align: center; border-radius: 4px;" type="text"></textarea>
                        </div>
                    </div>
                </form>

                <p class="uk-text-right">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">ألغاء</button>
                    <button :disabled="!IsValid" @click.prevent="AddP()" class="uk-button uk-button-primary" type="button">
                        <i v-if="miniload" class="fa fa-refresh fa-spin"></i>
                        <span v-else >Add</span>
                    </button>
                </p>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        data(){
          return {
            orders: {},
            orders2: {},
            seccus: false,
            post: {},
            new_policy: {
                title: '',
                body: ''
            },
            admin: false,
            receiver: false,
            isHidden: false,
            miniload:false,
            loading: false
          }
        },

        created(){
            this.fetchArticles();
            if (this.$session.get('table_type') == "admins") { this.admin = true; }
            if (this.$session.get('table_type') == "receivers") { this.receiver = true; }
        },

        methods: {
            fetchArticles(){
                this.axios.get(process.env.VUE_APP_URL+`/api/Policies`)
                  .then(res => {
                    console.log(res.data);
                    this.orders = res.data;
                    this.current_page = res.data.current_page;
                    if (res.data.next_page_url !== null) { this.next = true; }
                    if (res.data.prev_page_url !== null) { this.prev = true; }
                    this.isLoaded = true;
                  })
                .catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            DeleteArticle(order_id) {

                this.miniload = true;

                let formData = new FormData();
                formData.append('id', order_id);

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/Policies_Delete`, formData)
                    .then(res => {

                    if (res.status == 200) {
                        let toast = this.$toasted.show("Deleted", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.miniload = false;
                        this.fetchArticles();
                    }
                    else{
                        let toast = this.$toasted.show("Error", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.miniload = false;
                    }
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            AddP(){

                this.miniload = true;

                let formData = new FormData();
                formData.append('title', this.new_policy.title);
                formData.append('body', this.new_policy.body);

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/Policies_Put`, formData)
                    .then(res => {
                    if (res.status == 200) {
                        let toast = this.$toasted.show("Added", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.miniload = false;
                        this.fetchArticles();
                        UIkit.modal('#modal-example').hide();
                    }
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.miniload = false;
                });
            },

            Permissions_Val(rule){
                if(this.$A_Role == 'admins' || this.$Account.premissions.includes(rule)){
                    return true;
                }

                return false;
            },

        }, 

        computed: {
            IsValid() {
                    return this.new_policy.title !== ''
                    && this.new_policy.body !== ''
            }
        }
    };
</script>
