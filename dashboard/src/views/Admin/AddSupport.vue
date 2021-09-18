<template>

<div style="margin-bottom: 0px" class="uk-margin" align="center">
    <div class="uk-card uk-card-default" style="padding:10px; width: 60%;" >
            <div class="uk-margin uk-grid-small" uk-grid >

                    <div class="uk-width-1-1@s">
                            <font v-if="val_errors" class="val_errors">{{val_errors.first_name}}</font>
                            <input type="text" placeholder="First Name" v-model="post.first_name" class="uk-input uk-form-width-larg uk-form-larg" >
                    </div>

                    <div class="uk-width-1-1@s">
                            <font v-if="val_errors" class="val_errors">{{val_errors.last_name}}</font>
                            <input type="text" placeholder="Last Name" v-model="post.last_name" class="uk-input uk-form-width-larg uk-form-larg" >
                    </div>

                    <div class="uk-width-1-1@s">
                            <font v-if="val_errors" class="val_errors">{{val_errors.address_country}}</font>
                            <input type="text" placeholder="Country" v-model="post.address_country" class="uk-input uk-form-width-larg uk-form-larg" >
                    </div>

                    <div class="uk-width-1-1@s">
                            <font v-if="val_errors" class="val_errors">{{val_errors.address_state}}</font>
                            <input type="text" placeholder="State" v-model="post.address_state" class="uk-input uk-form-width-larg uk-form-larg" >
                    </div>

                    <div class="uk-width-1-1@s">
                            <font v-if="val_errors" class="val_errors">{{val_errors.address_region}}</font>
                            <input type="text" placeholder="Region" v-model="post.address_region" class="uk-input uk-form-width-larg uk-form-larg" >
                    </div>

                    <div class="uk-width-1-1@s">
                            <font v-if="val_errors" class="val_errors">{{val_errors.phone_number}}</font>
                            <input type="text" placeholder="Phone Number" v-model="post.phone_number" class="uk-input uk-form-width-larg uk-form-larg" >
                    </div>

                    <div class="uk-width-1-1@s">
                            <font v-if="val_errors" class="val_errors">{{val_errors.password}}</font>
                            <input type="password" placeholder="Password" v-model="post.password" class="uk-input uk-form-width-larg uk-form-larg" >
                    </div>

                    <div class="uk-width-1-1@s">
                            <font v-if="val_errors" class="val_errors">{{val_errors.password_confirmation}}</font>
                            <input type="password" placeholder="Confirm password" v-model="post.password_confirmation" class="uk-input uk-form-width-larg uk-form-larg" >
                    </div>

                    <div class="uk-width-1-1@s" align="center">
                            <button v-if="miniload" disabled class="uk-button uk-button-default"><i class="fa fa-refresh fa-spin"></i></button>
                            <button v-else :disabled="!isValid" class="uk-button uk-button-default updub" @click.prevent="createPost(post)">Add</button>
                    </div>

            </div>
    </div>
</div>


</template>

 <script>
        export default {
            name: "CreatePost",

            data() {
                return {
                    customer: null,
                    post: {},
                    val_errors: null,
                    notFound: false,
                    miniload: false,
                    seccus: false
                }
            },

            methods: {
                createPost(post) {

                    this.miniload = true;
                    this.seccus = false;
                    this.val_errors = false;

                    this.axios.post(process.env.VUE_APP_URL+`/api/Admin/Add_Support`, post)
                    .then(res => {
                        if (res.status == 202) {
                            let toast = this.$toasted.show("Error", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                            this.val_errors = res.data.errs;
                            this.miniload = false;
                            console.log(res);
                        }
                        if (res.status == 200) {
                            let toast = this.$toasted.show("Added", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                            this.seccus = true;
                            this.miniload = false;
                            console.log(res);
                        }

                    })
                    .catch(res => {
                        this.miniload = false;
                        let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    });

                }
            },
            computed: {
                isValid() {
                    return this.post.sender_full_name !== ''
                    && this.post.sender_phone_number !== ''
                    && this.post.receiver_full_name !== ''
                    && this.post.reciever_phone_number !== ''
                    && this.post.recieved_price !== ''
                    && this.post.location_from_country !== ''
                    && this.post.location_from_state !== ''
                    && this.post.location_from_region !== ''
                    && this.post.location_to_country !== ''
                    && this.post.location_to_state !== ''
                    && this.post.location_to_region !== ''
                    && this.post.current_location_on_map !== ''
                    && this.post.recieve_date !== ''
                    && this.post.location_on_map_from !== ''
                    && this.post.location_on_map_to !== ''
                    && this.post.weight !== ''
                    && this.post.length !== ''
                    && this.post.track_code !== ''
                    && this.post.delivery_type !== ''
                    && this.post.Code !== ''
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
</style>
