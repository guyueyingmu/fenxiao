<template>
    <div class="detail">
        <!--商品图片尺寸  660 * 520-->
        <swiper :options="swiperOption" :style="{height:`296px`}">
            <swiper-slide v-for="(slide,idx) in good_info.banner" :key="idx">
                <img :src="slide.img_url">
            </swiper-slide>
            <div class="swiper-pagination" slot="pagination"></div>
        </swiper>
        <div class="header">
            <h2 class="title">{{good_info.good_title}}</h2>
            <div class="info">
                <span class="price">￥
                    <em>{{good_info.price}}</em>
                </span>
                <span class="share">
                    <i class="iconfont icon-fenxiang"></i>我要分享</span>
            </div>
        </div>

        <div class="tool">
            <div class="item btn">
                <i class="iconfont icon-kefu2"></i>
                <em>在线客服</em>
            </div>
            <div class="item btn">
                <i class="iconfont icon-xin"></i>
                <!-- 收藏成功 -->
                <i class="iconfont icon-shoucang-on" v-if="good_info.is_collect == 1"></i>

                <em v-else>收藏</em>
            </div>
            <div class="item btn myCart" @click="goto('/cart')">
                <i class="iconfont icon-gouwuche"></i>
                <em>购物车</em>
                <i class="num">{{good_info.cart_total}}</i>
            </div>
            <div class="item add">加入购物车</div>
            <div class="item buy" @click="goto('confirm')">立即购买</div>

        </div>

        <div class="tags">
            <span :class="{'active':tagActive == 0}" @click="switchTags(0)">商品介绍</span>
            <span class="a-line"></span>
            <span :class="{'active':tagActive == 1}" @click="switchTags(1)">用户评论
                <i v-if="comment_pages.total">({{comment_pages.total}})</i>
            </span>
        </div>

        <div class="content minHeight200" v-show="tagActive == 0" v-html="good_info.detail">

            <!-- <img src="static/mini/img/demo/detail/d0.jpg">
                                                                        <img src="static/mini/img/demo/detail/d1.jpg">
                                                                        <img src="static/mini/img/demo/detail/d2.jpg">
                                                                        <img src="static/mini/img/demo/detail/d3.jpg">
                                                                        <img src="static/mini/img/demo/detail/d4.jpg">
                                                                        <img src="static/mini/img/demo/detail/d5.jpg">
                                                                        <img src="static/mini/img/demo/detail/d6.jpg"> -->
        </div>

        <div class="comment-list minHeight200" v-show="tagActive == 1">

            <div class="main" v-if="comment_list.length > 0">
                <div class="tagsLabel">
                    <span class="active">全部</span>
                    <span>好评</span>
                    <span>中评</span>
                    <span>差评</span>
                </div>
                <ul class="ui-comment" v-loading="loadComment">
                    <li v-for="(item,idx) in comment_list" :key="idx" v-infinite-scroll="loadMore" :infinite-scroll-immediate-check="false" infinite-scroll-disabled="loadComment" infinite-scroll-distance="50">
                        <div class="row">
                            <div class="pic"><img :src="item.img_url"> </div>
                            <div class="name">{{item.nickname}}</div>
                            <div class="time">{{item.add_time}}</div>
                        </div>
                        <div>
                            <rate :value="item.stars" :readonly="true" :text="true"></rate>
                        </div>

                        <div class="des">{{item.content}}</div>
                        <div class="imgList" v-if="item.imgs">
                            <img v-for="img in item.imgs" :key="img" :src="img">
                        </div>
                    </li>

                </ul>

            </div>
            <div class="noComment" v-else>
                <i class="iconfont icon-zanwuxinxi"></i>
                暂无评论
            </div>

        </div>
        <div style="height:80px;background:#fff"></div>

    </div>
</template>
<script>

import http from '@/assets/js/http'
import Rate from './rate'

import { swiper, swiperSlide } from 'vue-awesome-swiper'

require('swiper/dist/css/swiper.css')
export default {
    name: 'detail',
    mixins: [http],
    components: {
        swiper,
        swiperSlide,
        Rate
    },

    data() {
        return {
            swiperOption: {
                autoplay: 3500,
                setWrapperSize: true,
                pagination: '.swiper-pagination',
                paginationType: 'fraction',
                paginationClickable: true,
                mousewheelControl: true,
                observeParents: true,


            },
            tagActive: 0,
            loadComment: false,
            comment_list: [],
            comment_pages: {},
            good_info: {},
            good_id: 0,
            timer: null
        }
    },
    methods: {
        switchTags(idx) {
            this.tagActive = idx;
            if (idx == 1 && this.comment_list.length < 1) {
                this.loadComment = true;
                let vm = this;
                vm.get_comment(1);

            }
        },
        //我的足迹  保存
        setHistory(data) {
            let _canPush = true;
            if (data) {

                let olddata = window.localStorage.getItem("__history__")
                if (olddata) {
                    olddata = JSON.parse(olddata)
                    if (olddata) {
                        for (let item of olddata) {
                            if (item.id == data.id) {
                                _canPush = false;
                                break;
                            }
                        }
                        if (_canPush) {
                            if (olddata.length < 10) {
                                olddata.splice(0, 0, data);
                            }

                        }
                    }

                } else {
                    olddata = []
                    olddata.splice(0, 0, data);
                }


                window.localStorage.setItem("__history__", JSON.stringify(olddata))
            }

        },
        //获取商品信息
        get_info(id) {
            let url = '/mini/Good/detail?id=' + id,
                vm = this;
            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.good_info = res.data;
                    let _obj = {
                        id: res.data.id,
                        good_img: res.data.good_img,
                        good_title: res.data.good_title,
                        price: res.data.price,
                    }
                    vm.setHistory(_obj)

                } else {
                    vm.handleError(res)
                }
            })
        },
        loadMore() {

            let page = parseInt(this.comment_pages.current_page, 10);
            let vm = this;
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                console.log('infinite load page:', page)
                if (page < vm.comment_pages.total_page) {
                    vm.get_comment(page + 1);
                }
            }, 1000)
        },
        //评论过滤
        onSearch() {
            let searchData = this.formInline
            this.get_comment(1, searchData)
        },


        //获取评论信息
        get_comment(page, searchData) {
            page = page || 1;
            this.loadComment = true;
            let url = '/mini/Comment/get_list?good_id=' + this.good_id + '&page=' + page,
                vm = this;

            this.apiGet(url, searchData).then(function(res) {
                vm.loadComment = false
                if (res.code) {
                    vm.comment_pages = res.data.pages;
                    if (page < 2) {
                        vm.comment_list = res.data.list;
                    } else {
                        let _list = vm.comment_list;
                        _list = _list.concat(res.data.list)
                        vm.comment_list = _list;
                    }

                } else {
                    vm.handleError(res)
                }
            })
        }
    },
    created() {
        this.setTitle('商品详情')
        this.good_id = this.$route.params.id;
        this.get_info(this.good_id);
        //  this.get_comment(1);

    },
}

</script>
