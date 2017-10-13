<template>
    <div>
        <ul class="ui-fixd">
            <li class="med">
                <div class="">头像</div>
                <img :src="info.img_url" width="50" height="50" alt="">
            </li>
            <li>
                <div class="">呢称</div>
                <div class="gery">{{info.nickname}}</div>
            </li>
            <li>
                <div class="">性别</div>
                <div class="gery">{{info.sex == 1 ? '男' : info.sex == 2 ? '女' : '保密'}}</div>
            </li>
            <li>
                <div class="">已绑手机</div>
                <div v-if="info.phone_number"><span class="gery">{{info.phone_number}} </span> <span class="ui-btn ui-btn-small" @click="goto('/reg')">重新绑定</span></div>
                <div v-else><span class="gery">未绑定 </span> <span class="ui-btn ui-btn-small" @click="goto('/reg')">前往绑定</span></div>
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
        get_info() {
            let url = '/mini/Home/center_info',
                vm = this;

            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.info = res.data;
                } else {
                    vm.handleError(res)
                }
            })
        },
    },
    created() {
        this.setTitle('帐号')
        this.get_info();
    }


}

</script>
