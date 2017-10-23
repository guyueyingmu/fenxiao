<template>
    <div id="app">

        <div class="layout flex">
            <div class="layout-menu-left">
                <div class="my_nav_warp">
                    <div class="my_nav">
                        <div class="layout-logo-left">
                            <span>分销管理系统</span>
                        </div>
                        <el-menu :router="true" theme="dark" :unique-opened="false" :default-active="activeMenu" :default-openeds="activeMenuOpen">
                            <el-submenu :index="idx.toString()" v-for="(item,idx) in list" :key="item.id" v-if="item.status == 1">
                                <template slot="title">
                                    <i class="el-icon-message"></i>{{item.menu_name}}</template>
                                <el-menu-item :index="c.link" v-for="(c,c_idx) in item.child" :key="c.id">{{c.menu_name}}</el-menu-item>
                            </el-submenu>

                        </el-menu>
                    </div>
                </div>

            </div>
            <div v-loading="$store.state.c_loading" class="layout-right">
                <div class="layout-main-wrap">
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
                            <router-view class="child-view"> </router-view>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <el-dialog title="警告" :visible.sync="$store.state.RoseDialogVisible" size="tiny" :close-on-click-modal="false">
            <span>
                <i class="el-icon-warning" style="color:#F7BA2A;font-size:40px;    vertical-align: middle;"></i> 您没有访问该页面的权限！</span>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="$store.state.RoseDialogVisible = false">确 定</el-button>
            </span>
        </el-dialog>

        <el-dialog title="警告" :visible.sync="$store.state.LoginDialogVisible" size="tiny" :close-on-click-modal="false" :close="closeLogin">
            <span>
                <i class="el-icon-warning" style="color:#F7BA2A;font-size:40px;    vertical-align: middle;"></i> 您登录已超时，请重新登录！</span>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="closeLogin">确 定</el-button>
            </span>
        </el-dialog>

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
            list: [],
            nickname: '',
            breadcrumb: {
                l1: "",
                l2: ''
            },
            c_loading: false
        }
    },

    methods: {
        closeLogin() {
            window.location = '/admin/Login/log_out'
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
        },
        get_cat() {
            console.log(this.$store.state.cat_list.length)
            if (this.$store.state.cat_list.length < 1) {
                let url = '/admin/goodscat/get_list',
                    vm = this;
                this.apiGet(url).then(function(res) {
                    if (res.code) {
                        for(let _item of res.data.list){
                            _item.id = _item.id.toString();
                        }
                        vm.setCatList(res.data.list)


                    } else {
                        vm.handleError(res)
                    }

                })
            }
        },
    },
    computed: {
        "minHeight"() {
            return (window.innerHeight - 144) + 'px'
        },
        activeMenu() {
            var _path = this.$route.path;
            if (/goods_add/.test(_path) || /goods_edit/.test(_path) || /order_id/.test(_path) || /user_id/.test(_path)) {
                _path = _path.split('/')
                _path = '/' + _path[1];
            }

            return _path
        },
        activeMenuOpen() {
            let a = [];
            for (let i = 0; i < this.list.length; i++) {
                a.push(i.toString())
            }

            return a

        },

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
                window.localStorage.setItem('__Menu__', JSON.stringify(data))
                vm.setNavlist(data)

            }
        })
        //用户信息
        this.apiGet('/admin/Login/get_user_info').then(function(res) {
            if (res.code) {
                vm.nickname = res.data.nickname;
                vm.get_cat();
            }
        })




    }

}
</script>

