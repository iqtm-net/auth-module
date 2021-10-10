<template>
    <div>
      <v-content>
          <v-container fill-height>
              <v-layout align-center justify-center>
                  <v-flex class="login-form text-xs-center">

                      <div class="display-1 mb-2 text-center">
                        <v-layout align-center justify-center>
                            <v-img class="mt-4" max-width="80" src="../assets/hodhod.png" ></v-img>
                        </v-layout>
                        <br>
                        <div class="Cairo">لوحة الماسح الضوئي</div>
                      </div>

                      <v-card class="mt-5" light>
                          <v-card-text>
                              <div class="subheading text-center Cairo">
                                ادخل رمز المندوب السري الخاص بك للمتابعة
                              </div>
                              <v-form ref="form" v-model="valid">
                                  <v-text-field class="centered-input Cairo text-center" v-model="deliver_unique_code" :rules="rules" :loading="checking" light prepend-icon label="رمز المندوب"></v-text-field>
                              </v-form>
                          </v-card-text>
                      </v-card>
                      <!-- <div v-if="options.isLoggingIn">
                        Don't have an account?<v-btn light="light" @click="options.isLoggingIn = false">Sign up</v-btn>
                      </div> -->
                  </v-flex>
              </v-layout>
          </v-container>
          
        </v-content>
    </div>
</template>

 <script>

        export default {

            name: "login",

            data() {
                return {
                    valid: true,
                    deliver_unique_code: '',
                    deliver_data: {},
                    val_errors: false,
                    notConfirmed: false,
                    checking:false,
                    OnlyAdminsAndReceivers:false,
                    loading:false,
                    rules: [
                      value => { return !this.val_errors || 'الرمز غير صحيح'; }
                    ],
                }
            },

            created(){
                Vue.$localStorage.clear()
            },

            methods: {

                CheckCode() {
                    
                    this.checking = true;
                    let formData = new FormData();
                    formData.append('unique_code', this.deliver_unique_code);

                    this.axios.post(process.env.VUE_APP_URL+`/api/Deliver/check_deliver_code`, formData)
                        .then(res => {
                            
                            Vue.$localStorage.set('deliver_data', res.data.account)
                            Vue.$localStorage.set('token', res.data.token)
                            Vue.$localStorage.set('table_type', res.data.table_type)
                            Vue.$localStorage.set('value', res.data.value)

                            this.$router.push({ name: 'Panel'})
                            this.checking = false;
                        })
                       .catch(err => {
                            this.checking = false;
                            this.val_errors = true;
                            this.$refs.form.validate()
                        })
                },

                
            },

            computed: {
                isValid() {
                    return this.deliver_unique_code !== ''
                }
            },

            watch:{
                deliver_unique_code(value){
                //   this.checking = (value.length > 6) ? true : false;
                    (value.length < 7) ? this.val_errors = false : false;
                    (value.length == 7) ? this.CheckCode() : false;
                }
            }
        };
</script>

<style lang="scss" scoped>

.login-form{
  max-width: 500px
}

input{
  text-align: center !important;
}
.Cairo{
  font-family: Cairo !important;
}
</style>