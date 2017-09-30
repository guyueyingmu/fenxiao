<template>
    <div class="alert">
       alert{{tit}}
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
        
    },
    mounted() {
       this.tit = this.value 


    },
}

</script>
