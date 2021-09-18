<template>

<div>
    <div>
        <component v-bind:is="component" />
    </div>
</div>

</template>

<script>
    
    import Amad from './store_themes/Amad/index.vue'

    export default {

        components: {
            Amad,
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

