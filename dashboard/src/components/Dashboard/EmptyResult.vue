<template>

    <div>
        <div v-if="length == 0" align="center">
            <i class="fas fa-box-open" style="font-size: 20vw;"></i>
        </div>
    </div>
 
</template>

<script>
    export default {

        props: ['Data'], 

        data() {
            return {
                length: 0
            };
        },

        watch: {
            Data: function() {
                (this.Data.length) ? this.length = this.Data.length : 0;
                (this.Data.length == 0) ? this.length = 0 : false;
            } 
        },
    };
</script>

<style scoped> 
</style>
