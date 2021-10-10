<template>

<div>
    <div>
        <component v-bind:is="component" />
    </div>
</div>

</template>

<script>
    
    import Amad from './store_themes/Amad/index.vue'
    import HodhodV1 from './store_themes/Amad/index.vue'

    export default {

        components: {
            Amad,
            HodhodV1
        },

        data(){
            return {
                component: null,
            }
        },

        async created() {
            this.component = this.$theme; 
        },

        methods: {

        },
        
        computed: {
            
        }, 

    };

</script>

