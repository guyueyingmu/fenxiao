<template>
    <div id="app">
        <!--微信里隐藏 导航-->
        <header class="hd_nav" v-if="$is.WeiXin == false">
            <a href="javascript:history.go(-1)" class="back"></a>
            <h3 class="headerTitle">{{$store.state.title}}</h3>
        </header>
        <div :style="{'margin-top':$is.WeiXin == false?'58px':''}"></div>
        <div class="search" :class="{'show':showSearch}" v-if="$route.name == 'home' || $route.name == 'search'">
            <div class="search-p" @click="onSearch" v-if="showSearch == false">
                <i class="iconfont icon-sousuo"></i>请输入关键词搜索</div>
            <div class="search-box" v-if="showSearch == true">

                <input type="text" id="keyword" placeholder="请输入关键词搜索" class="ui-input" @keyup.enter="search" v-model="$store.state.search.keyword" v-focus="showSearch">
                <i class="iconfont icon-chuyidong" v-if="$store.state.search.keyword.length > 0" @click="$store.state.search.keyword = ''"></i>
                <span class="ui-btn ui-btn-search" @click="search">
                    <i class="iconfont icon-sousuo"></i>搜索</span>
            </div>
        </div>
        
            <router-view class="child-view" :style="{'margin-bottom':showNav?'58px':''}">

            </router-view>
     
        <nav v-if="showNav == true">
            <div class="nva-item" :class="{'active':$route.name == 'home'}" @click="goto('/')">
                <i class="iconfont icon-shouye"></i>
                <i class="iconfont icon-shouye-s alt"></i>
                首页
            </div>
            <div class="nva-item" @click="goto('/class')" :class="{'active':$route.name == 'class'}">
                <i class="iconfont icon-fenlei1" :class="{'alt':$route.name == 'class'}"></i>
                分类
            </div>
            <div class="nva-item" @click="goto('/cart')" :class="{'active':$route.name == 'cart'}">
                <i class="iconfont icon-gouwuche"></i>
                <i class="iconfont icon-gouwuche2 alt"></i>
                购物车
            </div>
            <div class="nva-item" @click="goto('/userCenter')" :class="{'active':$route.name == 'userCenter'}">
                <i class="iconfont icon-gerenzhongxin1"></i>
                <i class="iconfont icon-gerenzhongxin alt"></i>
                个人中心
            </div>

        </nav>
        <div id="goto_top" v-show="gotoTop" @click="onGotoTop">
            <i class="iconfont icon-huidaodingbu" title="回到顶部"></i>
        </div>
        <div id="goto_home" v-if="showNav == false">
            <i class="iconfont icon-shouye" @click="goto('/')" title="回到首页"></i>
        </div>

    </div>
</template>

<script>

import http from './assets/js/http'
import FastClick from '../../static/mini/lib/fastclick'

export default {
    name: 'app',
    mixins: [http],

    watch: {
        '$route'(to, from) {
            const toDepth = to.path.split('/').length
            const fromDepth = from.path.split('/').length
            this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'
            if (to.name == 'search') {
                this.showSearch = true;
            } else {
                this.showSearch = false
            }
        },
        'keyword'(n, o) {
            this.setKeyword(n)

        }
    },
    data() {
        return {
            transitionName: 'slide-left',
            showSearch: false,
            keyword: "",
            gotoTop: false

        }
    },
    computed: {
        showNav() {
            let name = this.$route.name;
            if (name == 'home' || name == null || name == 'cart' || name == 'class' || name == 'userCenter') {
                return true
            } else {
                return false;
            }
        }
    },
    methods: {
        onGotoTop() {
            document.body.scrollTop = 0;
        },
        onSearch() {
            this.showSearch = true;
            this.goto('/search')
        },
        search() {
            if (!this.$store.state.search.keyword) {
                return
            }
            var obj = document.getElementById('keyword')
            obj.blur();
            let _list = this.$store.state.hList;
            if (this.$store.state.hList.indexOf(this.$store.state.search.keyword) == -1) {
                if (_list.length >= 10) {
                    _list.shift()
                }
                _list.push(this.$store.state.search.keyword)

                if (_list) {
                    _list = JSON.stringify(_list);
                    window.localStorage.setItem('__SearchHistory__', _list);
                }
            }

            let url = '/mini/Good/get_list?page=1',
                vm = this, data = {
                    keyword: this.$store.state.search.keyword
                };
            vm.$store.state.search.loading = true;
            this.apiGet(url, data).then(function(res) {
                if (res.code) {
                    vm.setSearchList(res.data.list)

                } else {
                    vm.handleError(res)
                }
                setTimeout(() => {
                    vm.$store.state.search.loading = false;
                }, 400)
            })


        },
    },
    mounted() {
        let vm = this;
        var fn = function(event) {
            var top = document.body.scrollTop;

            if (top >= 100) {
                vm.gotoTop = true
            } else {
                vm.gotoTop = false
            }
        }
        document.addEventListener('scroll', fn, false)

        if (this.$is.IPhone && !this.$is.PC) {
            FastClick.attach(document.body);
        }

    }
}
</script>

