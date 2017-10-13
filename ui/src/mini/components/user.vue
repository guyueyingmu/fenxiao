<template>
    <div>
        <ul class="ui-fixd">
            <li class="med">
                <div class="">头像</div>
                <img src="" width="50" height="50" alt="">
            </li>
            <li>
                <div class="">呢称</div>
                <div class="gery">张三</div>
            </li>
            <li>
                <div class="">性别</div>
                <div class="gery">男</div>
            </li>
            <li>
                <div class="">已绑手机</div>
                <div><span class="gery">13809087868 </span> <span class="ui-btn ui-btn-small">重新绑定</span></div>
            </li>

        </ul>

    </div>
</template>
<script>
import http from '@/assets/js/http'

export default {
    name: 'class',
    mixins: [http],
    data() {
        return {
            info: {}

        }
    },
    methods: {
        get_cat() {
            let url = '/mini/Home/get_cat_list?page=1',
                vm = this;

            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.cat_list = res.data;
                    reload()
                } else {
                    vm.handleError(res)
                }
            })
        },
    },
    created() {
        this.setTitle('帐号')

    }


}

</script>
