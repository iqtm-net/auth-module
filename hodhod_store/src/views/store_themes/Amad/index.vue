<template>
    <div ref="bdy">
        
        <!-- Search Wrapper Area Start -->
        <div class="search-wrapper section-padding-100">

            <div class="search-close">
                <i class="fa fa-close" aria-hidden="true"></i>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="search-content">
                            <form action="#" method="get">
                                <input type="search" name="search" id="search" placeholder="Type your keyword...">
                                <button type="submit"><img src="/images/store_themes/amad/img/core-img/search.png" alt=""></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- ##### Main Content Wrapper Start ##### -->
        <div class="main-content-wrapper d-flex clearfix">  

            <!-- Mobile Nav (max width 767px)-->
            <div class="mobile-nav">

                <!-- Navbar Toggler -->
                <div v-if="!mob_header" class="amado-navbar-toggler" @click.prevent="header_toggle">
                    <span></span><span></span><span></span>
                </div>

                <!-- Close Icon -->
                <div v-if="mob_header" class="nav-close" @click.prevent="header_close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                </div>

                <!-- Navbar Brand -->
                <div class="amado-navbar-brand">
                    <a href="/"><img :src="OrderData.theme_logo" alt=""></a>
                </div>

            </div>

            <!-- Header Area Start -->
            <header v-if="mob_header" class="header-area clearfix">

                <!-- Logo -->
                <div class="logo uk-visible@m">
                    <a href="/"><img :src="OrderData.theme_logo" alt=""></a>
                </div>

                <!-- Amado Nav -->
                <nav class="amado-nav uk-flex uk-flex-center">
                    <ul>
                        <li class="active">
                            <a href="/" class="uk-text-cairo">الرئيسية</a>
                        </li>
                        <li v-for="branch in Store_Branches" v-bind:key="branch.id">
                            <a :href=" `/branch/${branch.id}`" class="uk-text-cairo" >{{branch.branch}}</a>
                        </li>
                    </ul>
                </nav>

            </header>
            
            <!-- Product Catagories Area Start -->
            <div class="products-catagories-area" ref="scroll_d">
                
                <!-- Slider -->
                <div class="uk-margin-top uk-width-1-1 uk-visible@m">
                    
                    <div class="untitled" style="height: 26vmax; overflow: hidden;">
                        
                        <div class="untitled__slides">
                            <div class="untitled__slide" v-for="(data,index) in data.suggested_items" v-bind:key="index">
                                <div class="untitled__slideContent uk-width-1-1 uk-grid-collapse" uk-grid>

                                    <div class="uk-width-1-3 single-products-catagory clearfix">
                                        <a :href="`/buy/${data.id}`">
                                            <img :src="data.image">
                                        </a>
                                    </div>

                                    <div class="uk-width-2-3 single-products-catagory clearfix slider_r_side"> 
                                        <div class="slider_bg"></div>
                                        <div class="slider_content uk-padding uk-text-right">
                                            <h1>{{data.item}}</h1>
                                            <span class="hint-star star uk-width-1-1">
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star-o" aria-hidden="true"></i>
                                            </span>
                                            
                                            <p class="information uk-margin-top uk-text-truncate" style="direction: rtl;">{{data.description}}</p>
                                            
                                            <div class="uk-text-black uk-margin-bottom"><span class="size"> <span v-for="(size,index) in data.sizes" v-bind:key="index">{{size}}</span> </span> الاحجام </div>
                                        
                                            <div class="uk-text-black uk-margin-bottom uk-flex uk-flex-right">
                                                <div v-for="(color,index) in data.colors" v-bind:key="index" v-bind:style="`background-color:${color};`" class="colors" style="width: 17px; height: 17px;"></div>
                                                <div> الالوان </div> 
                                            </div>

                                            <div class="control">
                                                <button class="btn"> 
                                                <span class="price">{{data.price}} IQD</span>
                                                <!-- <span class="shopping-cart"><i class="fa fa-shopping-cart" aria-hidden="true"></i></span> -->
                                                <span @click.prevent="GoToBuy(data.id)" class="buy uk-text-cario">شراء</span>
                                                <!-- <a :href="`/buy/${data.id}`">شراء</a> -->
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="untitled__shutters"></div>
                    </div>
                </div>

                <br><br>

                <!-- Iiems listing -->
                <div class="uk-margin-top uk-grid-large uk-child-width-1-2@m uk-child-width-1-3@l" uk-grid>
                    <div v-for="(data,index) in Store_Items" v-bind:key="index">
                        <div class="el-wrapper">

                            <div class="box-up">
                                <img class="img" :src="data[1].image" alt="">
                                <div class="img-info">

                                    <div class="info-inner">
                                        <!-- <span class="p-name uk-text-cario">{{data[1].item}}</span> -->
                                        <!-- <span class="p-company">Yeezy</span> -->
                                    </div>

                                    <div class="a-size uk-text-cario">

                                        <div class="uk-margin-bottom">{{data[1].description.substring(0,300)}}</div>

                                        <div class="uk-margin-bottom uk-text-black"><span class="size"> <span v-for="(size,index) in data[1].sizes" v-bind:key="index">{{size}}</span> </span> الاحجام </div>

                                        <div class="uk-flex uk-flex-center">
                                            <div v-for="(color,index) in data[1].colors" v-bind:key="index" v-bind:style="`background-color:${color};`" class="colors"></div>
                                            <div class="uk-text-black"> الالوان </div> 
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div class="uk-padding-small" uk-grid>
                                <div class="uk-width-auto">
                                    <span class="hint-star star uk-text-large" style="display:initial">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star-o" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <div class="uk-width-expand">
                                    <div class="uk-margin uk-text-cario uk-text-large uk-text-right">{{data[1].item}}</div>
                                </div>
                            </div>
                            

                            <div class="box-down">
                                
                                <div class="h-bg"> <div class="h-bg-inner"></div> </div>
                                
                                <a class="cart" :href="`/buy/${data[1].id}`">
                                    <span class="price">{{data[1].price}} IQD</span>
                                    <span class="add-to-cart">
                                        <span class="txt uk-text-bold uk-text-medium uk-text-cario" @click.prevent="GoToBuy(data[1].id)">شراء</span>
                                    </span>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- listing loading bar -->
                <div v-if="!items_res" class="uk-width-1-1 uk-margin-top uk-flex uk-flex-center" align="center">
                    <img src="/images/store_themes/amad/animation.gif" />
                </div>
            </div>

        </div>

        <br><br>

        <!-- ##### Footer Area Start ##### -->
        <footer class="footer_area clearfix">
            <div class="container">
                <div class="row align-items-center">

                    <!-- Single Widget Area -->
                    <div class="col-12 col-lg-4">
                        <div class="single_widget_area">

                            <!-- Logo -->
                            <div class="footer-logo mr-50">
                                <a href="/"><img :src="OrderData.theme_logo" alt=""></a>
                            </div>

                            <!-- Copywrite Text --> 
                            <p class="copywrite">
                                This template is made with <i class="fa fa-heart-o" aria-hidden="true"></i>by <a href="https://ihodhod.com" target="_blank">HodHod Platform</a>
                            </p>
                        </div>
                    </div>

                    <!-- Single Widget Area -->
                    <div class="col-12 col-lg-8">
                        <div class="single_widget_area">
                            <!-- Footer Menu -->
                            <div class="footer_menu" ref="footer">
                                <nav class="navbar navbar-expand-lg justify-content-end">
                                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#footerNavContent" aria-controls="footerNavContent" aria-expanded="false" aria-label="Toggle navigation"><i class="fa fa-bars"></i></button>
                                    <div class="collapse navbar-collapse" id="footerNavContent">
                                        <ul class="navbar-nav ml-auto">
                                            <li v-for="branch in data.store_footer_branches" v-bind:key="branch.id" class="nav-item">
                                                <a class="nav-link" :href="`/branch/${branch.id}`" >{{branch.branch}}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </footer>
    
    </div>
</template>


<script>
    
    export default {

        components: {
        },

        data(){
            return {
                Code:'',
                StoreFound:false,
                data:{},
                OrderData:{},
                Store_Items:[],
                Store_Branches:{},
                item_data: {},

                SelectedItem:{},
                is_valid:false,
                MiniLoading:false,
                is_not_valid:false,
                items_res:false,
                items_res_length: null,
                items_pag: 0,
                mob_header : false,
            }
        },

        async created() {
            this.main();
            this.get_branche_items_pag();
        },

        methods: {
            GoToBuy(id){
                window.location.href = "/buy/"+id;
            },

            main(){
                var store_name = window.location.host.split(".");

                this.axios.get(process.env.VUE_APP_URL+`/api/check_shared_link_code/${store_name[0]}`)
                .then(res => {      
                    this.data = res.data.data;
                    this.OrderData = res.data.data.store_infos;
                    this.Store_Branches = res.data.data.store_branches;
                    this.StoreFound = true;
                })
                .catch(res => {
                    JSON.stringify(res);
                    this.$router.push({ path: '/404' })
                });

                if(this.$route.params.Order_id !== undefined){
                    this.axios.get(process.env.VUE_APP_URL+`/api/view_item/${this.$route.params.Order_id}`)
                    .then(res => {      
                        this.item_data = res.data.data;
                    })
                    .catch(res => {
                        JSON.stringify(res);
                        this.$router.push({ name: '404' })
                    });
                }
            },

            get_branche_items_pag(){
                
                var store_name = window.location.host.split(".");
                this.items_pag = this.items_pag + 1;
                this.items_res = false;

                this.axios.get(process.env.VUE_APP_URL+`/api/get_branche_items_pag/${store_name[0]}/All?page=${this.items_pag}`)
                .then(res => {   
                    
                    var v = this;
                    let storeitems = new Map(res.data.data);
                    storeitems.forEach(function(value, key){
                        v.Store_Items.push([ key , value ]); 
                    });
                    this.items_res = true;
                    this.items_res_length = res.data.data.length;
                })
                .catch(res => {
                    JSON.stringify(res);
                    this.$router.push({ path: '/404' })
                }); 
            },

            pag_increaser(){
                this.items_pag = this.items_pag + 1;
            },

            scroll() {
                window.onscroll = () => {

                    var content = this.$refs.scroll_d;
                    
                    if ((window.innerHeight + document.documentElement.scrollTop) > content.offsetHeight+40 && this.items_res_length > 0) {
                        // console.log('bla3mbla');
                        this.get_branche_items_pag();
                    }
                }
            },

            header_toggle(){
                this.mob_header = true;
            },

            header_close(){
                this.mob_header = false;
            },

        },

        mounted () {
            this.scroll();
            if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                this.mob_header = false;
            } else {
                this.mob_header = true;
            }
        }

    };
</script>

<style lang="scss">

    @import './css/core-style.css';
    @import './style.css';
    @import './scss/card.scss';
    @import './scss/slider.scss';

    // @import "./scss/style.scss";
    // @import"../scss/_variables.scss";
    // @import"../scss/_theme_color.scss";
    // @import"../scss/_mixin.scss"
    .products-catagories-area{
        z-index: 1;
        padding: 0px 30px;
    }
</style> 