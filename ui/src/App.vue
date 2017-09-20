<template>
    <div id="app">

        <el-row class="layout" type="flex">
            <el-col :span="4" :md="4" :lg="3" :xs="6" class="layout-menu-left">
                <div class="layout-logo-left">
                    <span>分销管理系统</span>
                </div>
                <el-menu :router="true" theme="dark" :unique-opened="true" :default-active="activeMenu" :default-openeds="activeMenuOpen">
                    <el-submenu :index="idx.toString()" v-for="(item,idx) in list" :key="item.id" v-if="item.status == 1">
                        <template slot="title">
                            <i class="el-icon-message"></i>{{item.menu_name}}</template>
                        <el-menu-item :index="c.link" v-for="(c,c_idx) in item.child" :key="c.id">{{c.menu_name}}</el-menu-item>
                    </el-submenu>

                </el-menu>

            </el-col>
            <el-col :span="20" :md="20" :lg="21" :xs="18">
                <div class="layout-header">
                    <div class="layout-header-left"></div>
                    <div class="login-info">
                        <span>欢迎您，{{nickname}}</span>
                        <a href="javascript:;" @click="loginOut">退出</a>
                    </div>
                </div>
                <div class="layout-breadcrumb">
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                        <el-breadcrumb-item v-for="item in $store.state.breadcrumb" :key="item">{{item}}</el-breadcrumb-item>
                    </el-breadcrumb>
                </div>
                <div class="layout-content">
                    <div class="layout-content-main" :style="{'min-height':minHeight}">
                        <transition :name="transitionName">
                            <router-view class="child-view"></router-view>
                        </transition>
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
    watch: {
        '$route'(to, from) {
            const toDepth = to.path.split('/').length
            const fromDepth = from.path.split('/').length
            this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'
        }
    },
    data() {
        return {
            //activeMenuOpen: ['0'],
            transitionName: 'slide-left',
            list: [],
            nickname: '',
            breadcrumb: {
                l1: "",
                l2: ''
            },


        }
    },
    methods: {
        //取商品分类
        get_cat() {
            let url = '/admin/goodscat/get_list',
                vm = this;
            this.apiGet(url).then(function(res) {
                if (res.code) {
                    vm.setCatList(res.data.list)

                } else {
                    vm.handleError(res)
                }

            })
        },
        //退出登录
        loginOut() {
            let url = '/admin/Login/log_out', vm = this;
            function _login_out(vm, url) {
                vm.apiGet(url).then(function(res) {
                    if (res.code) {
                        window.location = '/admin/login.html'
                    }
                })
            }

            this.$confirm('是否退出登录?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                //_login_out(vm, url)
                window.location = '/admin/Login/log_out'

            }).catch(() => {
            });
        }
    },
    computed: {
        "minHeight"() {
            return (window.innerHeight - 144) + 'px'
        },
        activeMenu() {
            var _path = this.$route.path;
            _path = _path.split('/')
            console.log('/' + _path[1], 9999)
            return '/' + _path[1];
        },
        activeMenuOpen() {
            var _path = this.$route.path, _array = '10';
            _path = _path.split('/')
           _path =  _path[1];
            if (this.list.length && _path != '/') {
                let data = this.list, _reg = new RegExp(_path)
                for (let i = 0; i < data.length; i++) {
                    let _break = false;
                    for (let k = 0; k < data[i].child.length; k++) {
                        if (_reg.test(data[i].child[k].menu_link)) {
                            _array = i.toString();
                            _break = true;

                            break;
                        }
                    }
                    if (_break) {
                        break;
                    }
                }

                // console.log(`计算菜单`, _array)
            }

            return [_array]

        }
    },
    created() {
        //初始化，获取菜单信息
        let vm = this;
        let url = '/admin/login/get_menu'
        this.apiGet(url).then(function(res) {
            if (res) {
                let data = JSON.parse(res);
                for (let i = 0; i < data.length; i++) {
                    for (let k = 0; k < data[i].child.length; k++) {
                        data[i].child[k].link = data[i].child[k].menu_link.replace('/admin/index/#', '')
                    }
                }
                vm.list = data;

                vm.setNavlist(data)
            }
        })
        //用户信息
        this.apiGet('/admin/Login/get_user_info').then(function(res) {
            if (res.code) {
                vm.nickname = res.data.nickname;
            }
        })
        this.get_cat();




    }

}
</script>

