<template>

    <div style="margin-bottom: 0px" class="uk-margin">
        
        <br>
        <vcl-table v-if="!isLoaded" :rows="9" :columns="5"></vcl-table>

        <div v-else>
            <table border="0" class="table cust-table uk-card uk-card-default">
                <thead>
                    <tr style="">
                        <th style="width:80px;">#</th>
                        <th style="width:200px;" v-if="Permissions_Val('Learn With HodHod_delete')" class="text-center">
                            <li class="fa fa-gear"></li>
                        </th>
                        <th style="width:400px;">title</th>
                        <th style="width:800px;">body</th>
                        <th style="width:100px;" v-if="Permissions_Val('Learn With HodHod_add')">
                            <button class="btn btn-outline-info" style="color: #04bacc;" uk-toggle="target: #AddِArtical">
                                <span class="fas fa-plus-circle"></span>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody v-for="(order) in orders.data" :key="order.id">
                    <tr>
                        <th style="width:80px;">{{ order.id }}</th>
                        <td style="width:200px;" v-if="Permissions_Val('Learn With HodHod_delete')" class="text-center">
                            <button @click.prevent="DeleteArticle(order)" class="btn btn-outline-danger del-icon">
                                <span class="fa fa-trash-o"></span>
                            </button>

                            <button @click.prevent="BeforeUpdate(order)" class="btn btn-outline-success del-icon" uk-toggle="target: #UpdateArtical">
                                <span class="fas fa-pencil-alt"></span>
                            </button>
                        </td>
                        <td style="width:400px;">{{ order.title }}</td>
                        <td style="width:800px;"> ... {{ order.body.slice(0, 40) }}</td>
                        <td style="width:100px;" v-if="Permissions_Val('Learn With HodHod_add')"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br><br>

        <!-- <div class="row Departmain">
            <div class="col-sm-6 Previous" align="center">
                <button v-if="prev" _ngcontent-udn-32="" class="btn btn-secondary tab-nav-btn" type="button" @click.prevent="Previous()">
                    <i _ngcontent-udn-32="" class="fa fa-chevron-left"></i>
                </button>
                <button v-else _ngcontent-udn-32="" class="btn btn-secondary tab-nav-btn disabled-btn" type="button" disabled>
                    <i _ngcontent-udn-32="" class="fa fa-chevron-left"></i>
                </button>
            </div>

            <div class="col-sm-6 Next" align="center">
                <button v-if="next" _ngcontent-udn-32="" class="btn btn-secondary tab-nav-btn" type="button" @click.prevent="Next()">
                    <i _ngcontent-udn-32="" class="fa fa-chevron-right"></i>
                </button>
                <button v-else _ngcontent-udn-32="" class="btn btn-secondary tab-nav-btn disabled-btn" type="button" disabled>
                    <i _ngcontent-udn-32="" class="fa fa-chevron-right"></i>
                </button>
            </div>
        </div> -->

        <!--Add-->
        <div id="AddِArtical" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <div class="uk-grid-match" uk-grid>
                    <div class="uk-width-1-1@s">
                        <div>
                            <input class="uk-input uk-text-center" type="text" placeholder="Title"  v-model="AddArticalForm.title">
                        </div>
                        <div v-if="val_errors" style="color:red">
                            <font v-for="error in val_errors.title" :key="error.id"> ({{error}}) </font>
                        </div>
                    </div>
                    
                    <div class="uk-width-1-1@s">
                        <div>
                            <textarea class="uk-textarea uk-height-small uk-text-center" type="text" placeholder="Message"  v-model="AddArticalForm.body"></textarea>
                        </div>
                        <div v-if="val_errors" style="color:red">
                            <font v-for="error in val_errors.body" :key="error.id"> ({{error}}) </font>
                        </div>
                    </div>

                    <div class="uk-width-1-1@s">
                        <div>
                            <input class="uk-input uk-text-center" type="text" placeholder="Video Url"  v-model="AddArticalForm.video">
                        </div>
                        <div v-if="val_errors" style="color:red">
                            <font v-for="error in val_errors.video" :key="error.id"> ({{error}}) </font>
                        </div>
                    </div>
                </div>

                <p class="uk-text-center">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
                    <button :disabled="!AddIsValid" @click.prevent="AddِArtical()" class="uk-button uk-button-primary" type="button">
                        <i v-if="miniload" class="fa fa-refresh fa-spin"></i>
                        <span v-else >Add</span>
                    </button>
                </p>
            </div>

            
        </div>

        <!--Update-->
        <div id="UpdateArtical" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <div class="uk-grid-match" uk-grid>
                    <div class="uk-width-1-1@s">
                        <div>
                            <input class="uk-input uk-text-center" type="text" placeholder="Title"  v-model="AddArticalForm.title">
                        </div>
                        <div v-if="val_errors" style="color:red">
                            <font v-for="error in val_errors.title" :key="error.id"> ({{error}}) </font>
                        </div>
                    </div>
                    
                    <div class="uk-width-1-1@s">
                        <div>
                            <textarea class="uk-textarea uk-height-small uk-text-center" type="text" placeholder="Message"  v-model="AddArticalForm.body"></textarea>
                        </div>
                        <div v-if="val_errors" style="color:red">
                            <font v-for="error in val_errors.body" :key="error.id"> ({{error}}) </font>
                        </div>
                    </div>

                    <div class="uk-width-1-1@s">
                        <div>
                            <input class="uk-input uk-text-center" type="text" placeholder="Video Url"  v-model="AddArticalForm.video">
                        </div>
                        <div v-if="val_errors" style="color:red">
                            <font v-for="error in val_errors.video" :key="error.id"> ({{error}}) </font>
                        </div>
                    </div>
                </div>

                <p class="uk-text-center">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
                    <button :disabled="!AddIsValid" @click.prevent="UpdateArtical()" class="uk-button uk-button-primary" type="button">
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
            AddArticalForm:{
                title: '',
                body: '',
                video: ''
            },
            miniload:false,
            isLoaded: false
          }
        },

        created(){
            this.fetchArticles(); 
        },

        methods: {
            fetchArticles(){
                this.axios.get(process.env.VUE_APP_URL+`/api/Articles`)
                  .then(res => {
                    this.orders = res.data;
                    this.isLoaded = true;
                  })
                .catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            Next() { this.$router.push({path: `/Articles/${this.current_page+1}` }); },

            Previous() { this.$router.push({path: `/Articles/${this.current_page-1}` }); },

            DeleteArticle(order_id) {

                this.miniload = true;

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/DeleteArticle`, order_id)
                    .then(res => {
                    console.log(res.data);
                    if (res.status == 200) {
                        let toast = this.$toasted.show("Deleted", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.miniload = false;
                    }
                    else{
                        let toast = this.$toasted.show("Error", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.miniload = false;
                    }
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            }, 

            AddِArtical(){
                this.miniload = true;
                this.val_errors = false;

                let formData = new FormData(); 
                formData.append('title', this.AddArticalForm.title);
                formData.append('body', this.AddArticalForm.body);
                formData.append('video', this.AddArticalForm.video);

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/AddArticle`, formData)
                .then(res => { 
                    if (res.status == 200) {
                        let toast = this.$toasted.show("Added", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.fetchArticles()
                        this.miniload = false;
                        this.AddArticalForm = {};
                        UIkit.modal('#AddArtical').hide();
                        
                    }
                })
                .catch(res => { 
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.miniload = false;
                });
            },
            
            BeforeUpdate(Data){
                this.AddArticalForm = Data;
            },

            UpdateArtical(){
                this.miniload = true;
                this.val_errors = false;

                let formData = new FormData(); 
                formData.append('id', this.AddArticalForm.id);
                formData.append('title', this.AddArticalForm.title);
                formData.append('body', this.AddArticalForm.body);
                formData.append('video', this.AddArticalForm.video);

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/UpdateArticle`, formData)
                .then(res => { 
                    if (res.status == 200) {
                        let toast = this.$toasted.show("Updated", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.fetchArticles()
                        this.miniload = false;
                        this.AddArticalForm = {};
                        UIkit.modal('#UpdateArtical').hide();
                        
                    }
                })
                .catch(res => { 
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
            AddIsValid(){
                return this.AddArticalForm.title !== ''
                && this.AddArticalForm.body !== ''
                && this.AddArticalForm.video !== ''
            }
        }
    };
</script>
