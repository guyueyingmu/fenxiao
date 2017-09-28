<template>
    <div class="rate">
        <i class="iconfont icon-11" @click="select(i)" :class="{'active':v >= i }" v-for="i in 5" :key="i"></i> <span v-if="text"> {{label}}</span>
    </div>
</template>
<script>
export default {
    name: 'Rate',
    props: {
        value: {
            type: Number
        },
        readonly: {
            type: Boolean
        },
        text:{
            type:Boolean
        }
    },
    data() {
        return {
            v: 1,
            label:''

        }
    },
    methods: {
        select(i) {
            if (!this.readonly) {
                this.v = i;
                this.$emit('input', this.v)
               this.setLabel(i)
            }
        },
        setLabel(i){
              if(i > 3){
                    this.label = '好评'
                }else if(i == 3){
                    this.label = '中评'
                }else if(i < 3 && i > 0){
                    this.label = '差评'
                }else if(i == 0){
                    this.label = ''
                }
        }
    },
    mounted() {
        this.v = this.value ? parseInt(this.value) : 0;
        this.setLabel(this.v)


    },
}

</script>
