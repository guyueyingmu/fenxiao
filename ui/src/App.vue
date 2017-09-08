<template>
    <div id="app">

        <el-row class="layout" type="flex">
            <el-col :span="4" :md="4" :lg="3" :xs="6" class="layout-menu-left">
                <div class="layout-logo-left"></div>
                <el-menu default-active="2" class="el-menu-vertical-demo" @open="handleOpen" @close="handleClose" theme="dark">
                    <el-submenu :index="idx.toString()" v-for="(item,idx) in list" :key="item.id">
                        <template slot="title">
                            <i class="el-icon-message"></i>{{item.label}}</template>
                        <el-menu-item :index="idx.toString()+'-'+c_idx.toString()" v-for="(c,c_idx) in item.child" :key="c.id">{{c.label}}</el-menu-item>
                    </el-submenu>

                </el-menu>

            </el-col>
            <el-col :span="20"  :md="20" :lg="21" :xs="18">
                <div class="layout-header">
                    <div class="layout-header-left"></div>
                    <div class="login-info">
                        <img src="" width="40" height="40" alt=""> 欢迎您，李明明
                        <a href="#"> 退出</a>
                    </div>
                </div>
                <div class="layout-breadcrumb">
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                        <el-breadcrumb-item>活动管理</el-breadcrumb-item>
                        <el-breadcrumb-item>活动列表</el-breadcrumb-item>
                        <el-breadcrumb-item>活动详情</el-breadcrumb-item>
                    </el-breadcrumb>
                </div>
                <div class="layout-content">
                    <div class="layout-content-main" :style="{'height':minHeight}">
                        <router-view></router-view>
                    </div>
                </div>
            </el-col>
        </el-row>

    </div>
</template>

<script>
export default {
    name: 'app',
    data() {
        return {
            list: [

                { id: 1, label: "商品", child: [{ id: 1, label: "商品列表" }, { id: 2, label: "商品分类" }] },
                { id: 2, label: "订单", child: [{ id: 1, label: "订单列表" }, { id: 2, label: "退款申请" }, { id: 3, label: "换货申请" }, { id: 4, label: "商品评论" }] },
                { id: 3, label: "用户", child: [{ id: 1, label: "商品列表" }, { id: 2, label: "商品分类" }] },
                { id: 4, label: "分销", child: [{ id: 1, label: "商品列表" }, { id: 2, label: "商品分类" }] },
                { id: 5, label: "系统", child: [{ id: 1, label: "商品列表" }, { id: 2, label: "商品分类" }] },
            ]
        }
    },

    methods: {
        handleOpen(key, keyPath) {
            console.log(key, keyPath);
        },
        handleClose(key, keyPath) {
            console.log(key, keyPath);
        }
    },
    computed: {
        "minHeight"() {
            return (window.innerHeight - 144) + 'px'
        }
    }
}
</script>

