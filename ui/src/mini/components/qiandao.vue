<template>
    <div>
        <div class="user">
            <div class="userBox">
                <div class="photo">
                    <img :src="info.img_url">
                </div>
                <div class="inners">
                    <div>我的积分：{{info.credits}}</div>
                    <div>已累计签到：
                        <span class="red">{{info.sign_total}}天</span>
                    </div>
                </div>
                <div class="qiandao-gz" @click="showTips">
                    <i class="iconfont icon-31yiwen"></i> 签到规则</div>
            </div>
        </div>

        <div class="page-title recommend ">
            <span class="title">积分商品</span>

        </div>
        <ul class="thumb-box" v-show="list.length > 0" v-infinite-scroll="loadMore" :infinite-scroll-disabled="sloading" :infinite-scroll-immediate-check="false" :infinite-scroll-distance="10" infinite-scroll-listen-for-event="cheackLoadMore">
            <li v-for="(good,k) in list" :key="k">
                <div class="thumb" @click="goto('/detail/id/'+good.id)"><img v-lazy="good.good_img"></div>
                <div class="title" @click="goto('/detail/id/'+good.id)">
                    {{good.good_title}}
                </div>
                <div class="info" style="padding-left:5px">积分
                    <span class="price">
                        <em>{{good.credits}}</em>
                    </span>
                    <i class="iconfont" ></i>
                </div>
            </li>
        </ul>

        <div class="spinner" v-if="sloading">
            <mt-spinner :size="18" color="#26a2ff"></mt-spinner>
        </div>
        <div class="nodata-line" v-else-if="pages.total_page == pages.current_page">没有更多数据了</div>

        <div class="nodata" v-if="list.length < 1 && sloading == false">
            <i class="iconfont icon-tongyongmeiyoushuju"></i>
            <div>还没有积分商品~</div>
        </div>

        <div class="qian-tips" v-show="tips">
            <div class="mask"></div>
            <transition name="qtips">
                <div class="m" v-show="tips">
                    <i class="iconfont icon-shibai" @click="closeTips"></i>
                    签到规则, 签到规则, 签到规则, 签到规则, 签到规则, 签到规则, 签到规则, 签到规则, 签到规则, 签到规则, 签到规则,
                </div>
            </transition>
        </div>

    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    name: 'yongjin',
    mixins: [http],
    data() {
        return {
            list: [],
            pages: {},
            sloading: false,
            tips: false,
            info: {},
        }
    },
    methods: {
        showTips() {
            this.tips = true;
        },
        closeTips() {
            this.tips = false;
        },
        loadMore() {
            if (this.sloading) { return }
            let page = parseInt(this.pages.current_page, 10) || 1;
            if (page < this.pages.total_page) {
                this.get_list(page + 1);
            }

        },
        get_list(page) {
            page = page || 1;
            let url = '/mini/Good/get_list?page=' + page,
                vm = this;
            vm.sloading = true
            this.apiGet(url, {list_type : 1}).then(function(res) {

                if (res.code) {

                    vm.pages = res.data.pages;
                    if (vm.list.length == 0) {
                        vm.list = res.data.list;


                    } else {
                        let _list = vm.list;
                        vm.list = _list.concat(res.data.list)


                    }
                    setTimeout(() => {
                        vm.sloading = false;
                        vm.$emit('cheackLoadMore')
                    }, 200)



                } else {
                    vm.handleError(res)
                }

            })
        },
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
        this.setTitle('签到');
        this.get_info();
        this.get_list();
    },

}
</script>
