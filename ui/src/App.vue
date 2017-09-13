<template>
    <div id="app">

        <el-row class="layout" type="flex">
            <el-col :span="4" :md="4" :lg="3" :xs="6" class="layout-menu-left">
                <div class="layout-logo-left"></div>
                <el-menu default-active="0-0" :default-openeds="[0]" class="el-menu-vertical-demo" @open="handleOpen" @close="handleClose" theme="dark">
                    <el-submenu :index="idx.toString()" v-for="(item,idx) in list" :key="item.id" v-if="item.status == 1">
                        <template slot="title">
                            <i class="el-icon-message"></i>{{item.menu_name}}</template>
                        <el-menu-item :index="idx.toString()+'-'+c_idx.toString()" v-for="(c,c_idx) in item.child" :key="c.id" @click="go(c.menu_link,idx,c_idx)">{{c.menu_name}}</el-menu-item>
                    </el-submenu>

                </el-menu>

            </el-col>
            <el-col :span="20" :md="20" :lg="21" :xs="18">
                <div class="layout-header">
                    <div class="layout-header-left"></div>
                    <div class="login-info">
                        <img src="" width="40" height="40" alt=""> 欢迎您，李明明
                        <a href="#"> 退出</a>
                    </div>
                </div>
                <div class="layout-breadcrumb">
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item :to="{ path: '/' }">首页{{$store.state.count}}</el-breadcrumb-item>
                        <el-breadcrumb-item v-for="item in $store.state.breadcrumb" :key="item">{{item}}</el-breadcrumb-item>
                    </el-breadcrumb>
                </div>
                <div class="layout-content">
                    <div class="layout-content-main" :style="{'min-height':minHeight}">
                        <router-view></router-view>
                    </div>
                </div>
            </el-col>
        </el-row>

    </div>
</template>

<script>
import http from './assets/js/http'
export default {
    name: 'app',
    mixins: [http],
    data() {
        return {
            list: [],
            mnue_default: '',
            breadcrumb: {
                l1: "",
                l2: ''
            },


        }
    },
    methods: {
        handleOpen(key, keyPath) {
            console.log(key, keyPath);
        },
        handleClose(key, keyPath) {
            console.log(key, keyPath);
        },
        go(url, idx, c_idx) {
            if (url) {
                this.breadcrumb = this.list[idx].menu_name;
                this.breadcrumb_child = this.list[idx].child[c_idx].menu_name;
                if (window.Global.DEV) {
                    let _url = url.replace('/admin/index/', '')
                    window.location = _url;
                } else {
                    window.location = url;
                }
            }
        },
        get_cat() {
            let url = '/admin/goodscat/get_list',
                vm = this;
            this.apiGet(url).then(function(res) {
                if (res.code) {
                   
                } else {
                    vm.handleError(res)
                }

            })

        }
    },
    computed: {
        "minHeight"() {
            return (window.innerHeight - 144) + 'px'
        }
    },
    created() {
        let vm = this;
        let url = '/admin/login/get_menu'
        this.apiGet(url).then(function(res) {
            if (res) {
                let data = JSON.parse(res);
                vm.list = data;
                vm.mnue_default = '0-0'
            }
        })
        this.get_cat();
    }

}
</script>

