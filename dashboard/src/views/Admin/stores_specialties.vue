<template>

    <div class="uk-margin">
        <div class="uk-padding-small" uk-grid>

            <div class="uk-width-2-3@m">
                <div class="uk-card uk-card-default uk-margin-card uk-padding-small uk-text-right">

                    <div class="uk-text-large uk-text-right">
                        تخصصات المتاجر
                    </div>

                    <vcl-table class="uk-padding-large" v-if="!isLoaded" :rows="5" :columns="2"></vcl-table>
                    <table v-else class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-right uk-table-responsive">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody v-for="(order) in orders.data" :key="order.id">
                            <tr>
                                <td class="text-center">
                                    <button :disabled="miniload" @click.prevent="Delete(order.id)" class="btn btn-outline-danger del-icon">
                                        <span class="fa fa-trash-o"></span>
                                    </button>
                                </td>
                                <td>{{ order.specialty }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="uk-width-1-3@m">
                <div class="uk-card uk-card-default uk-margin-card uk-padding-small uk-text-right">
                    <div class="uk-text-right"> أضافة تخصص </div>
                    <br>
                    <form class="uk-grid-small" uk-grid>

                        <div class="uk-width-1-1">
                            <div class="uk-form-controls">
                                <input class="uk-input" id="form-stacked-text" type="text" v-model="specialty">
                                <validator :errors="val_errors" field="text" />
                            </div>
                        </div>

                        <div class="uk-width-1-1 uk-text-center">
                            <button :disabled="!AddVal || miniload" class="uk-button uk-button-danger" @click.prevent="Add()">تأكيد</button>
                        </div>

                    </form>
                    
                </div>
            </div>
        </div>
    </div>

</template>


<script>
    export default {

        data(){
          return {
            orders: {},
            specialty: '',
            miniload: false,
            isLoaded: false,
          }
        },

        created(){
            this.fetchArticles();
        },

        methods: {

            fetchArticles(){
                this.axios.get(process.env.VUE_APP_URL+`/api/stores_specialties`) .then(res => { this.isLoaded = true; this.orders = res.data; })
                .catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                });
            },

            Add() {
                this.miniload = true;

                let formData = new FormData();
                formData.append('specialty', this.specialty);

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/add_specialties`, formData)
                    .then(res => {
                    this.miniload = false;
                    this.fetchArticles();
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.miniload = false;
                });
            },

            Delete(id) {
                this.miniload = true;

                let formData = new FormData();
                formData.append('id', id);

                this.axios.post(process.env.VUE_APP_URL+`/api/Admin/delete_specialties`, formData)
                    .then(res => {
                    this.miniload = false;
                    this.fetchArticles();
                }).catch(res => {
                    let toast = this.$toasted.show("Error 500", { type : 'error', theme: "bubble",  position: "bottom-right", duration : 2000 });
                    this.miniload = false;
                });
            },
            
        },

        computed:{
            AddVal(){
                return this.specialty !== ''
            }
        }

    };
</script>