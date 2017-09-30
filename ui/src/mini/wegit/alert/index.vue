<template>
    <div class="alert">
        alert
        {{tit}}
    </div>
</template>
<script>
export default {
    name: 'alert',
    props: ['value'],
    data() {
        return {
            tit: ''
        }
    },
    methods: {
        show(str) {
            this.str
        }

    }
}

</script>
