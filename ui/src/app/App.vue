<template>
    <div id="app">
                  <!--微信里隐藏 导航-->
    <header class="hd_nav">
    <a href="javascript:history.go(-1)" class="iconfont icon-jiantou-copy"></a>
    <h3 class="headerTitle">首页</h3>
    </header>
        <transition :name="transitionName">
            <router-view class="child-view"> </router-view>
        </transition>
    </div>
</template>

<script>

import http from './assets/js/http'
export default {
    name: 'app',
    mixins: [http],
    watch: {
        '$route'(to, from) {
            const toDepth = to.path.split('/').length
            const fromDepth = from.path.split('/').length
            this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'
        }
    },
    data() {
        return {

            transitionName: 'slide-left',

        }
    },
    methods: {
    }
}
</script>

