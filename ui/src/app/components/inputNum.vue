<template>
    <div class="inputNumber">
        <em @click="jian">
            <i class="iconfont icon-jian"></i>
        </em>
        <input type="tel" v-model="v" disabled maxlength="3">
        <em @click="jia">
            <i class="iconfont icon-jia"></i>
        </em>
    </div>
</template>
<script>
export default {
    name: 'inputNumber',
    props: ['value'],
    data() {
        return {
            v: 1
        }
    },
    methods: {
        jia() {
            let _v = parseInt(this.v,10);
            if (_v < 100) {
                this.v = _v + 1;
                this.$emit('input', this.v)
            }
        },
        jian() {
             let _v = parseInt(this.v,10);
            if (_v > 1) {

                this.v = _v - 1;
                this.$emit('input', this.v)
            }
        }
    },
    mounted() {
        this.v = this.value;


    },
}

</script>
