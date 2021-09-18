<template>

    <div class="">
        
        <div class="uk-padding-small" uk-grid>
            <div class="uk-width-2-3@m ">
                <div class="uk-child-width-1-1 uk-padding-large uk-padding-remove-bottom" uk-grid>
                    <!-- <div>
                        <form class="uk-search uk-search-default uk-width-1-1">
                            <a href="#" class="uk-search-icon-flip" uk-search-icon></a>
                            <input class="uk-search-input" v-debounce:2000ms="Searching" v-model="search_data" style="border-radius: 5px; background: white;" type="search" placeholder="بحث">
                        </form> 
                    </div> -->
                    <div>
                        <div class="uk-text-large uk-text-right">
                            القوالب
                        </div>
                    </div>
                </div>
                
                <br>

                <vcl-table class="uk-padding-large" v-if="!isLoaded" :rows="5" :columns="2"></vcl-table>

                <div v-else class="uk-child-width-1-2@s uk-child-width-1-2@l uk-padding-large uk-margin-remove-top uk-padding-remove-top" uk-grid>
                    <div v-for="theme in themes.data" :key="theme.id">
                        <div class="uk-card uk-card-default">
                            <div class="uk-card-header">
                                <viewer>
                                    <div class="uk-width-1-1 uk-height-small uk-margin-auto">
                                        <img style="width: -webkit-fill-available; height: -webkit-fill-available; object-fit: none;" :src="`${theme.image}`"> 
                                    </div>
                                </viewer>
                            </div>
                            <div class="uk-grid-match uk-grid-small uk-padding-small" uk-grid>
                                <!-- <div><i class="fas fa-pencil-alt edt_cat_op uk-text-muted mini-i" @click.prevent="SelectAccount(user)" uk-toggle="target: #Edit"></i></div> -->
                                <div class="uk-width-auto@m uk-text-left"><i class="fas fa-trash del_cat_op uk-text-muted" style="font-size: 34px;cursor:pointer" @click.prevent="Block(theme)"></i></div>
                                <div class="uk-width-expand@m uk-text-right uk-text-truncate"><h3 class="uk-card-title">{{theme.theme}}</h3></div>
                            </div>
                            <!-- <div class="uk-card-footer" align="right">
                                <a href="#" @click.prevent="SelectAccount(user)" class="uk-button uk-button-text" uk-toggle="target: #Notify"><span uk-icon="icon:mail; "></span></a>
                            </div> -->
                        </div>
                    </div>
                </div>

                <!-- <paginator class="uk-padding-large" v-on:childToParent="pagination" :data="users"></paginator> -->
            </div>
            <div class="uk-width-1-3@m uk-padding-large uk-padding-remove-left">
                <div class="uk-card uk-card-default uk-margin-card uk-padding-small uk-text-right">
                    <div class="uk-text-right"> أضافة قوالب </div>
                    <hr>
                    <form class="uk-grid-small" uk-grid>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label" for="form-stacked-text"> اسم القالب</label>
                        <div class="uk-form-controls">
                            <input class="uk-input" id="form-stacked-text" type="text" v-model="Add.theme">
                        </div>
                    </div>
                    <div class="uk-width-1-1">
                        <label class="uk-form-label" for="form-stacked-text">صورة</label>
                        <!-- <div class="uk-margin" uk-margin>
                            <div class="uk-width-1-1" align="center">
                                <button v-if="default_up_icon" @click.prevent="toggleShow" class="uk-button uk-button-default uk-width-1-1 uk-padding"><i class="fas fa-arrow-circle-up uk-text-large"></i></button>
                                <my-upload v-if="show" @crop-success="cropSuccess" langType="en" :width="300" :height="300" img-format="png"></my-upload>
                                <img :src="Add.image" style=" border-radius: 5px; cursor:pointer" @click.prevent="toggleShow">
                            </div>
                        </div> -->
                        <div id="my-strictly-unique-vue-upload-multiple-image uk-width-1-1" style="display: flex; justify-content: center;">
                            <vue-upload-multiple-image
                                @upload-success="uploadImageSuccess"
                                @before-remove="beforeRemove"
                                @edit-image="editImage"
                                @data-change="dataChange"
                                :data-images="images"
                            ></vue-upload-multiple-image>
                        </div>
                        {{images}}
                        <!-- {{Add.image}} -->
                    </div>
                    <div class="uk-width-1-1 uk-text-center">
                        <button :disabled="!Add || miniload" class="uk-button uk-button-danger" @click.prevent="AddTheme()">تأكيد</button>
                    </div>
                </form>
                </div>
                
            </div>
        </div>

    </div>

</template>

<script> 

    import VueUploadMultipleImage from 'vue-upload-multiple-image'
    import Viewer from 'v-viewer'
    Vue.use(Viewer)
    import 'viewerjs/dist/viewer.css'

    export default {
        components: {
            VueUploadMultipleImage
        },

        data() {
            return {
                default_up_icon:true,
                show:false,
                page:1,
                themes: {},
                search_data: '',
                edit:{
                    full_name:'',
                    phone_number:'',
                    car_license_number:'',
                },
                Add:{
                    theme:'',
                    image:'',
                },
                notify:{
                    id:'',
                    title:'',
                    body:'',
                },
                account:{},
                isLoaded:false,
                miniload: false,
            };
        },

        created(){
            this.get_themes();
        },

        methods: {

            // pagination(page) {
            //     this.page = page;
            //     this.get_themes();
            // },

            get_themes(){
                this.isLoaded = false,
                this.axios.get(process.env.VUE_APP_URL+`/api/stores_themes`)
                .then(res => {
                    this.themes = res.data;
                    this.isLoaded = true;
                });
            },


            Block(theme){
                this.$confirm(" حذف القالب" + " (" + theme.name + ") ").then(() => {

                    this.miniload = true;

                    let formData = new FormData();

                    formData.append('id', theme.id);

                    this.axios.post(process.env.VUE_APP_URL+`/api/Admin/deactivate_theme`, formData)
                        .then(res => {
                        if (res.status == 200) {
                            this.get_themes();
                            this.miniload = false;
                            let toast = this.$toasted.show("Done", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        } 
                    }).catch(res => {
                        let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                        this.miniload = false;
                    });

                });

            },

            AddTheme(){

                this.miniload = true;

                const config = {
                    headers: { 'content-type': 'multipart/form-data' }
                }

                let formData = new FormData();

                formData.append('theme', this.Add.theme);
                formData.append('image', this.Add.image);
            
                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/add_store_theme`, formData, config)

                .then(res => {

                    this.get_themes();
                    this.miniload = false;
                    // this.Add = {
                    //     theme:'',
                    //     image:'',
                    // };

                    let toast = this.$toasted.show("Done", { type : 'success', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    // this.beforeRemove();

                })
                .catch(res => {
                    this.val_errors = res.response.data.error;
                    this.miniload = false;
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            uploadImageSuccess(formData, index, fileList) {
                console.log(this.images)
                this.Add.image = fileList[0].path;
            },
            
            beforeRemove (index, done, fileList) {
                done()
            },

            editImage (formData, index, fileList) {
                console.log(this.images)
                this.Add.image = fileList[0].path;
                
            },

            dataChange (data) {
                console.log('asdasd')
                // this.Add.image = fileList[0].path;
            }

        },

        computed: {
            
            edit_val(){
                return this.edit.full_name !== ''
                && this.edit.phone_number !== ''
                && this.edit.email !== ''
            },
            
            notify_val(){
                return this.notify.title !== ''
                && this.notify !== ''
            },
            
            notify_val(){
                return this.Add.full_name !== ''
                && this.living !== ''
                && this.password !== ''
                && this.password_confirmation !== ''
                && this.car_type !== ''
                && this.car_license_number !== ''
                && this.active !== ''
            }
        },

        watch: {
            search_data(){
                this.isLoaded = false; 
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

#my-strictly-unique-vue-upload-multiple-image {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  /* margin-top: 60px; */
}

.uk-card-header{
    cursor: pointer;
    padding:0px !important;
}

</style>

