<template>

<div>
    <div v-if="StoreFound">
        <buy-item></buy-item>
    </div>
</div>

</template>

<script>
    import BuyItem from './store_themes/Amad/buy_item.vue'

    export default {

        components: {
            'buy-item': BuyItem
        },

        data(){
            return {

            }
        },

        async created() {
            
        },

        methods: {

        },
        
        computed: {
            
        }, 

    };
</script>

