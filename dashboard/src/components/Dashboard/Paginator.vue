<template>

    <div class="uk-flex uk-flex-between">
        <div class="uk-text-left"><button :disabled="data.prev_page_url == null" class="uk-button uk-button-text uk-text-large" @click.prevent="prev()" ><i class="fas fa-arrow-left" ></i></button></div>
        <div class="uk-text-center uk-text-bold uk-text-large">{{data.data.length}} items</div>
        <div class="uk-text-right"><button :disabled="data.next_page_url == null" class="uk-button uk-button-text uk-text-large" @click.prevent="next()"><i class="fas fa-arrow-right" ></i></button></div>
    </div>
 
</template>

<script>
    export default {
        props: {
            data:{},
            field:null,
        },

        data() {
            return {
                page: 1,
            };
        },

        methods: { 
            prev(){
                this.page = this.page - 1;
                this.$emit('childToParent', this.page);
            },

            next(){
                this.page = this.page + 1;
                this.$emit('childToParent', this.page);
            }
        }, 
        
    };
</script>
