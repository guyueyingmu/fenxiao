<template>
    <div>
      
            <div class="table_nodata">
                没有数据
            </div>

        </div>

   

    </div>
</template>

<script>
import http from '@/assets/js/http'

export default {
    mixins: [http],
    data() {
        return {
            list: []
        }
    },
    methods: {

        //取数据
        get_order_info(order_id) {
        
            let url = '/admin/order/get_order_info?order_id='+order_id,
                vm = this;
            vm.loading = true;
            this.apiGet(url, searchData).then(function(res) {
                if (res.code) {
                    vm.list = res.data.list;
                    vm.pages = res.data.pages
                } else {
                    vm.handleError(res)
                }
                vm.loading = false;
            })
        },

    },
    //组件初始化
    created() {
        this.get_order_info();
        this.setBreadcrumb(['订单', '订单详情'])
        this.setMenu('1-0');
    }

}
</script>
