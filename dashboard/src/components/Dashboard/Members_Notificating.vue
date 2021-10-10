<template>

    <div>
        <div class="uk-grid-match" uk-grid>
            <div class="uk-width-1-1@s">
                <div>
                    <input class="uk-input uk-text-center" type="text" placeholder="Title"  v-model="Notify.title">
                </div>
                <div v-if="val_errors" style="color:red">
                    <font v-for="error in val_errors.title" :key="error.id"> ({{error}}) </font>
                </div>
            </div>
            
            <div class="uk-width-1-1@s">
                <div>
                    <input class="uk-input uk-text-center" type="text" placeholder="order id"  v-model="Notify.order_id">
                </div>
                <div v-if="val_errors" style="color:red">
                    <font v-for="error in val_errors.title" :key="error.id"> ({{error}}) </font>
                </div>
            </div>

            <div class="uk-width-1-1@s">
                <div>
                    <textarea class="uk-textarea uk-height-small uk-text-center" type="text" placeholder="Message"  v-model="Notify.body"></textarea>
                </div>
                <div v-if="val_errors" style="color:red">
                    <font v-for="error in val_errors.title" :key="error.id"> ({{error}}) </font>
                </div>
            </div>

        </div>

        <p class="uk-text-center">
            <button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
            <button :disabled="!IsValid" @click.prevent="Send_Notification()" class="uk-button uk-button-primary" type="button">
                <i v-if="miniload" class="fa fa-refresh fa-spin"></i>
                <span v-else >Add</span>
            </button>
        </p>

    </div>
 
</template>

<script>
    export default {

        props: ['Account', 'Account_Role'], 

        data() {
            return {
                miniload: false,
                Notify: {
                    title:'',
                    body:'',
                    order_id:''
                },
                val_errors: {},
            };
        },

        methods: {  
            Send_Notification(){
                this.miniload = true;
                this.val_errors = false;

                let formData = new FormData();
                formData.append('member_id', this.Account.id);
                formData.append('member_role', this.Account_Role);
                formData.append('title', this.Notify.title);
                formData.append('MSG', this.Notify.body);
                formData.append('order_id', this.Notify.order_id);

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/notify`, formData)
                .then(res => { 
                    if (res.status == 200) {
                        this.$toasted.show("Sent", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.miniload = false;
                    }
                })
                .catch(res => { 
                    console.log(res);
                    this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.miniload = false;
                });
            }
        }, 

        computed: {
            IsValid() {
                return this.Notify.title !== '' && this.Notify.body !== ''
            }
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
