<template>

<div>
    <div v-if="StoreFound">
        <view-branch></view-branch>
    </div>
</div>

</template>

<script>
    import ViewBranch from './store_themes/Amad/view_branch.vue'

    export default {

        components: {
            'view-branch': ViewBranch
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

