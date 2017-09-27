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
            <div style="height:50px;" v-if="showSearch == true"></div>
            <div class="search-box" v-if="showSearch == true">

                <input type="text" placeholder="请输入关键词搜索" class="ui-input" @keyup.enter="search" v-model="searchForm.keyword" v-focus="showSearch">
                <i class="iconfont icon-chuyidong" v-if="searchForm.keyword.length > 0" @click="searchForm.keyword = ''"></i>
                <span class="ui-btn ui-btn-search" @click="search">
                    <i class="iconfont icon-sousuo"></i>搜索</span>
            </div>
        </div>
        <transition :name="transitionName">

            <router-view class="child-view" :style="{'margin-bottom':showNav?'58px':''}">

            </router-view>
        </transition>
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
            <div class="nva-item"  @click="goto('/cart')" :class="{'active':$route.name == 'cart'}">
                <i class="iconfont icon-gouwuche"></i>
                <i class="iconfont icon-gouwuche2 alt"></i>
                购物车
            </div>
            <div class="nva-item"   @click="goto('/userCenter')" :class="{'active':$route.name == 'userCenter'}">
                <i class="iconfont icon-gerenzhongxin1"></i>
                <i class="iconfont icon-gerenzhongxin alt"></i>
                个人中心
            </div>

        </nav>
        <div id="goto_top" v-show="gotoTop"><i class="iconfont icon-huidaodingbu" title="回到顶部"></i> </div>
        <div id="goto_home" v-if="showNav == false"><i class="iconfont icon-shouye" @click="goto('/')" title="回到首页"></i> </div>
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
            if (to.name == 'search') {
                this.showSearch = true;
            } else {
                this.showSearch = false
            }
        }
    },
    data() {
        return {
            transitionName: 'slide-left',
            showSearch: false,
            searchForm: {
                keyword: ''
            },
            gotoTop:false

        }
    },
    computed:{
        showNav(){
            let name = this.$route.name;
            if(name == 'home'|| name == null || name == 'cart' || name == 'class' || name == 'userCenter'){
                return true
            }else{
                return false;
            }
        }
    },
    methods: {
        onSearch() {
            this.showSearch = true;
            this.goto('/search')
        },
        search() {
            let _list = this.$store.state.hList;

            if (this.$store.state.hList.indexOf(this.searchForm.keyword) == -1) {
                if (_list.length >= 10) {
                    _list.shift()
                } 
                    _list.push(this.searchForm.keyword)

                if (_list) {
                    _list = JSON.stringify(_list);
                    window.localStorage.setItem('__SearchHistory__', _list);
                }
            }


        }
    },
    mounted(){
        let vm  = this;
         var fn = function (event) {
             var top = document.body.scrollTop;

            if(top >= 100){
               vm.gotoTop = true
            }else{
               vm.gotoTop = false
            }
        }
          document.addEventListener('scroll',fn,false)
    }
}
</script>

