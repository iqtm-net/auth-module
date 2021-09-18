<template>

    <div>
        <div v-for="(val) in errors[field]" :key="val.id">
            <div class="uk-text-danger uk-text-left uk-text-small">* {{val}}</div>
        </div>
    </div>
 
</template>

<script>
    export default {

        data() {
            return {
            };
        },

        methods: {
        }, 
    };
</script>
