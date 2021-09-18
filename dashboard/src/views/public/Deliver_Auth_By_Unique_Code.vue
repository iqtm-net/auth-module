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
                                <h3 class="display-4 uk-text-white">لوحة الماسح الضوئي</h3>
                                <p class="mb-4 uk-text-white">مرحبا ! الرجاء ادخال رمز المفتاح السري الخاص بك للمتابعة</p>
                                <form @submit.prevent="CheckCode()">
                                    <div class="form-group mb-3">
                                        <input id="inputEmail" type="text" v-model="deliver_unique_code" placeholder="ادخل الرمز في هذا الحقل" required="" autofocus="" class="form-control rounded-pill border-0 shadow-sm px-4 uk-text-center">
                                    </div>
                                    <button :disabled="!isValid || loading" type="submit" class="uk-width-1-1 btn btn-bg btn-block text-uppercase mb-2 rounded-pill shadow-sm uk-text-white">دخول</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End -->

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
                    deliver_unique_code: '',
                    deliver_data: {},
                    val_errors: false,
                    notConfirmed: false,
                    OnlyAdminsAndReceivers:false,
                    loading:false
                }
            },

            methods: {

                CheckCode() {

                    //laravel
                    this.loading = true; // for loading view

                    let formData = new FormData();
                    formData.append('unique_code', this.deliver_unique_code);

                    this.axios.post(process.env.VUE_APP_URL+`/api/Deliver/check_deliver_code`, formData)
                        .then(res => {
                            
                            this.$session.start()
                            this.$session.set('deliver_data', res.data.account)
                            this.$session.set('token', res.data.token)
                            this.$session.set('table_type', res.data.table_type)
                            this.$session.set('value', res.data.value)

                            this.$router.push({ name: 'deliver_panel'})
                            this.loading = false;
                            
                        })
                        .catch(err => {
                            this.$fire({
                                title: "رمز المفتاح السري غير صحيح",
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
                    return this.deliver_unique_code !== ''
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