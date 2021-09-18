<template>

    <div class="">
        <br>
        <div class="uk-padding-small" uk-grid>
            <div class="uk-width-2-3@m">
                <table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-center uk-table-responsive uk-card-default">
                    <thead>
                        <tr>
                            <th class="uk-text-center uk-text-truncate uk-width-auto">الرسالة</th>
                            <th class="uk-text-center uk-text-truncate uk-width-auto">العنوان</th>
                            <th class="uk-text-center uk-width-auto">المتجر</th>
                            <th class="uk-text-center uk-width-auto">نوع الحساب</th>
                            <th class="uk-text-center uk-width-auto">المستخدم</th>
                        </tr>
                    </thead>
                    <tbody v-for="(user) in users.data" :key="user.id">
                        <tr>
                            <td>{{user.MSG}}</td>
                            <td>{{user.title}}</td>
                            <td>
                                <span v-if="user.user_infos.user_type == 'stores' ">{{user.user_infos.data.store_name}}</span>
                                <span v-else> ------ </span>
                            </td>
                            <td>{{user.user_infos.user_type}}</td>
                            <td>{{user.user_infos.data.first_name}} {{user.user_infos.data.last_name}}</td>
                        </tr>
                    </tbody>
                </table>

                <paginator class="uk-padding-large" v-on:childToParent="pagination" :data="users"></paginator>
            </div>

            <div class="uk-width-1-3@m">
                <div class="uk-card uk-card-default uk-margin-card uk-padding-small uk-text-right">
                    <div class="uk-text-right"> ارسال اشعارات  </div>
                    <hr>
                    <form class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label" for="form-stacked-text"> العنوان</label>
                        <div class="uk-form-controls">
                            <input class="uk-input" id="form-stacked-text" type="text" v-model="Add.title">
                        </div>
                    </div>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label" for="form-stacked-text"> الرسالة </label>
                        <div class="uk-form-controls">
                                <textarea class="uk-textarea" v-model="Add.msg"></textarea>
                        </div>
                    </div>
                    <div class="uk-width-1-1 uk-text-center">
                        <button :disabled="!edit_val || miniload" class="uk-button uk-button-danger" @click.prevent="AddProduct()">تأكيد</button>
                    </div>
                </form>
                </div>
                
            </div>
        </div>
    </div>

</template>

<script> 

    import expand from "../../directives/expand";
    var numberAbbreviate = require("number-abbreviate");

    export default {
        components: {
        },

        data() {
            return {
                page:1,
                users: {},
                Add:{
                    title:'',
                    msg:'',
                },
                account:{},
                isLoaded:false,
                miniload: false,
            };
        },

        created(){
            this.get_users();
        },

        methods: {

            pagination(page) {
                this.page = page;
                this.get_users();
            },

            get_users(){
                this.isLoaded = false,
                this.axios.get(process.env.VUE_APP_URL+`/api/Admin/notifications?page=${this.page}`)
                .then(res => {
                    this.users = res.data;
                    this.isLoaded = true;
                });
            },


            AddProduct(){

                this.miniload = true;

                const config = {
                    headers: { 'content-type': 'multipart/form-data' }
                }

                let formData = new FormData();

                formData.append('member_role', '*');
                formData.append('member_id', '*');
                formData.append('title', this.Add.title);
                formData.append('MSG', this.Add.msg);
            
                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/notify_all`, formData, config)

                .then(res => {

                    this.$toasted.show("Success", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.get_users();
                    this.miniload = false;

                })
                .catch(res => {
                        this.val_errors = res.response.data.error;
                        this.miniload = false;
                });
            },

        },

        computed: {
            
            edit_val(){
                return this.Add.title !== ''
                && this.Add.msg !== ''
            },

        },

    };
</script>

<style>

.c_lable{
    justify-content: flex-end;
}
.c_lable:hover{
    padding-right: 10px;
    transition: all 1s;
}
.c_item{
    display: flex;
    justify-content: flex-end !important;
    margin-right: 20px;
    margin: 10px 20px;
}
.c_item:hover{
    padding-right: 10px;
    transition: all 1s;
}
.mini-i{
    font-size: 15px;
    margin: 0px 3px;
} 

.edt_cat_op:hover{
    color:#b8c400 !important;
}

.add_cat_op:hover{
    color:#00ba4b !important;
}

.del_cat_op:hover{
    color:#ff5b5b !important;
}

</style>

