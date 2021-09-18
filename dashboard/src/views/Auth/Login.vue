<template>

    <div class="container-fluid">
        <div class="row no-gutter">
            <!-- The image half -->
            <div class="col-md-6 d-none d-md-flex bg-image"></div>


            <!-- The content half -->
            <div class="col-md-6 bg-color">
                <div class="login d-flex align-items-center py-5">

                    <!-- Demo content-->
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-10 col-xl-7 mx-auto">
                                <h3 class="display-4 uk-text-white">Control Panel</h3>
                                <p class="mb-4 uk-text-white">Enter your phone number and password to continue !</p>
                                <form @submit.prevent="createPost(post)">
                                    <div class="form-group mb-3">
                                        <input id="inputEmail" type="number" v-model="post.phone_number" placeholder="phone number" required="" autofocus="" class="form-control rounded-pill border-0 shadow-sm px-4">
                                    </div>
                                    <div class="form-group mb-3">
                                        <input id="inputPassword" type="password" v-model="post.password" placeholder="Password" required="" class="form-control rounded-pill border-0 shadow-sm px-4 text-primary">
                                    </div>
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input id="customCheck1" type="checkbox" checked class="custom-control-input">
                                        <label for="customCheck1" class="custom-control-label uk-text-white">Remember password</label>
                                    </div>
                                    <button type="submit" class="btn btn-bg btn-block text-uppercase mb-2 rounded-pill shadow-sm uk-text-white">Sign in</button>
                                    <!-- <div class="text-center d-flex justify-content-between mt-4"><p>Snippet by <a href="https://bootstrapious.com/snippets" class="font-italic text-muted"> 
                                            <u>Boostrapious</u></a></p></div> -->
                                </form>
                            </div>
                        </div>
                    </div><!-- End -->

                </div>
            </div><!-- End -->

        </div>
    </div>


</template>

 <script>

        import VueSession from 'vue-session'
        Vue.use(VueSession)

        export default {

            name: "login",

            data() {
                return {
                    post: {
                        web: 'web'
                    },
                    val_errors: false,
                    notConfirmed: false,
                    OnlyAdminsAndReceivers:false,
                    loading:false
                }
            },

            methods: {

                // Store_session(token, table_type, value, account_data){

                //     let formData = new FormData();

                //     formData.append('token', token);
                //     formData.append('table_type', table_type);
                //     formData.append('value', value);
                //     formData.append('account_data', account_data);

                //     this.axios.post(process.env.VUE_APP_URL+`/api/vue_session_store`, formData);
                // },

                createPost(post) {

                    this.loading = true;

                    this.axios.post(process.env.VUE_APP_URL+`/api/login_in`, post)
                        .then(res => {
                        
                            this.axios.get(process.env.VUE_APP_URL+`/api/me`, { headers: { 'Authorization': 'Bearer ' + res.data.token } })
                            .then(res2 => {
                                
                                // this.Store_session(res.data.token, res.data.table_type, res.data.value, res2.data.data[0]);

                                this.$session.start()
                                this.$session.set('token', res.data.token)
                                this.$session.set('table_type', res.data.table_type)
                                this.$session.set('account_data', res2.data.data[0])

                                if (res.data.table_type == "admins") {
                                    window.location.href = "/center";
                                }

                                if(res.data.table_type == "receivers"){
                                    window.location.href = "/orders";
                                }

                                if(res.data.table_type == "companies"){
                                    window.location.href = "/orders";
                                }

                            });
                        })
                        .catch(err => {
                            this.$fire({
                                title: "Invalid phone number or password",
                                type: "error",
                                timer: 3000
                            }).then(r => {
                                this.loading = false;
                                this.val_errors = true;
                            });
                            
                        })
                }
            },
            
            computed: {
                isValid() {
                    return this.post.phone_number !== '' && this.post.password !== ''
                }
            }
        };
</script>

<style lang="scss" scoped>
/*
*
* ==========================================
* CUSTOM UTIL CLASSES
* ==========================================
*
*/
.login,
.image {
  min-height: 100vh;
}

.bg-image {
  background-image: url('../../assets/hodhod.png');
  background-size: cover;
  background-position: center center;
}

.bg-color{
    background-image: linear-gradient(#fc4236, #fc4236ad 60%, #fc42368c);
}

.btn-bg{
    background: #fc42368c;
}


</style>