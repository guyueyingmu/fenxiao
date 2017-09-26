<template>
    <div id="app">
        <!--微信里隐藏 导航-->
        <header class="hd_nav" v-if="$is.WeiXin == false">
            <a href="javascript:history.go(-1)" class="back"></a>
            <h3 class="headerTitle">首页</h3>
        </header>
        <div :style="{'margin-top':$is.WeiXin == false?'58px':''}"></div>
        <div class="search" :class="{'show':showSearch}">

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

            <router-view class="child-view" :style="{'margin-bottom':$store.state.ShowNav?'58px':''}">

            </router-view>
        </transition>
        <nav>
            <div class="nva-item">
                <i class="iconfont icon-shouye"></i>
                首页
            </div>
            <div class="nva-item">
                <i class="iconfont icon-fenlei1"></i>
                分类
            </div>
            <div class="nva-item">
                <i class="iconfont icon-gouwuche"></i>
                购物车
            </div>
            <div class="nva-item">
                <i class="iconfont icon-gerenzhongxin1"></i>
                个人中心
            </div>

        </nav>
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
    }
}
</script>

