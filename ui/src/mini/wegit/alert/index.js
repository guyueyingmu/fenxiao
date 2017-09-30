import Alert from './index.vue'

export default  {　　　　
    install: function (Vue) { 

        Vue.component('alert', Alert);　
        Vue.prototype.$msg = function(str){
            console.log(this.$alert)
        }
    }
}
