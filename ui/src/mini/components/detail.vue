<template>
    <div class="detail">
        <!--商品图片尺寸  660 * 520-->
        <swiper :options="swiperOption" :style="{height:setHeight}">
            <swiper-slide v-for="(slide,idx) in good_info.banner" :key="idx">
                <img :src="slide.img_url">
            </swiper-slide>
            <div class="swiper-pagination" slot="pagination"></div>
        </swiper>
        <div class="header">
            <h2 class="title">{{good_info.good_title}}</h2>
            <div class="info">
                <span class="price" v-if="good_info.good_type == 4 || good_info.good_type == 5">积分
                    <em>{{good_info.credits}}</em>
                </span>
                <span class="price" v-else>￥
                    <em>{{good_info.price}}</em>
                </span>
                <span class="share" @click="onshare()">
                    <i class="iconfont icon-fenxiang"></i>我要分享</span>
            </div>
        </div>

        <div class="tool">
            <div class="item btn" @click="goto('/talk/good_id/'+good_info.id)">
                <i class="iconfont icon-kefu2"></i>
                <em>在线客服</em>
            </div>
            <div class="item btn">
                <!-- 收藏成功 -->
                <i class="iconfont icon-shoucang-on" v-if="good_info.is_collect == 1" @click="del_collect(good_info.id)"></i>
                <i v-else class="iconfont icon-xin" @click="add_collect(good_info.id)"></i>
                <em>收藏</em>
            </div>
            <div class="item btn myCart" @click="goto('/cart')">
                <i class="iconfont icon-gouwuche"></i>
                <em>购物车</em>
                <i class="num" v-if="good_info.cart_total">{{good_info.cart_total}}</i>
            </div>
            <div class="item add" @click="add_cart(good_info.id);" v-if="good_info.good_type == 1">加入购物车</div>
            <div class="item buy" @click="go_buy(good_info.id)">立即购买</div>

        </div>

        <div class="tags">
            <span :class="{'active':tagActive == 0}" @click="switchTags(0)">商品介绍</span>
            <span class="a-line"></span>
            <span :class="{'active':tagActive == 1}" @click="switchTags(1)">用户评论({{good_info.comment_total}})</span>
        </div>

        <div class="content minHeight200" v-show="tagActive == 0" v-html="good_info.detail">
        </div>
        <div class="comment-list minHeight200" v-show="tagActive == 1" v-loading="loadComment">

            <div class="main">
                <div class="tagsLabel">
                    <span :class="{'active':search.keyword == 0}" @click="onSearch(0)">全部</span>
                    <span :class="{'active':search.keyword == 1}" @click="onSearch(1)">好评</span>
                    <span :class="{'active':search.keyword == 2}" @click="onSearch(2)">中评</span>
                    <span :class="{'active':search.keyword == 3}" @click="onSearch(3)">差评</span>
                </div>
                <ul class="ui-comment" v-if="comment_list.length > 0" v-infinite-scroll="loadMore" infinite-scroll-disabled="loadComment" :infinite-scroll-immediate-check="false" infinite-scroll-distance="10">
                    <li v-for="(item,idx) in comment_list" :key="idx">
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
                <div class="noComment" v-else>
                    <i class="iconfont icon-zanwuxinxi"></i>
                    暂无评论
                </div>
            </div>

        </div>
        <div class="ui-showShare" v-show="showShare" @click="showShare = false">

        </div>
        <div style="height:100px;background:#fff"></div>

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
    computed: {
        setHeight() {
            return window.innerWidth / 1.269 + 'px'
        }
    },

    data() {
        return {
            showShare:false,
            swiperOption: {
                autoplay: 3500,
                setWrapperSize: true,
                pagination: '.swiper-pagination',
                paginationType: 'fraction',
                paginationClickable: true,
                mousewheelControl: true,
                observeParents: true,
                autoHeight: true,

            },

            tagActive: 0,
            loadComment: false,
            comment_list: [],
            comment_pages: {},
            good_info: {},
            good_id: 0,
            search: {
                keyword: 0,
            },
            timer: null
        }
    },
    methods: {
        switchTags(idx) {
            this.tagActive = idx;
            if (idx == 1 && this.comment_list.length < 1) {

                let vm = this;
                vm.get_comment(1);

            }
        },
        //立即购买
        onshare() {
            this.showShare = true
        },
        go_buy(good_id) {
            if(this.good_info.user_phone_number){
                let params = [{ good_id: good_id, good_count: 1 }]
                this.setCart(params)
                this.goto('/confirm');
            }else{
                this.goto('/reg');
            }            
        },
        //加入购物车
        add_cart(good_id) {
            let url = '/mini/Cart/add', vm = this, data = { good_id: good_id };
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.good_info.cart_total = parseInt(vm.good_info.cart_total) + 1;
                    vm.$msg(res.msg);
                } else {
                    vm.handleError(res)
                }
            })
        },
        //加入收藏
        add_collect(good_id) {
            let url = '/mini/Collect/add', vm = this, data = { good_id: good_id };
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.good_info.is_collect = 1;
                    vm.$msg(res.msg);
                } else {
                    vm.handleError(res)
                }
            })
        },
        //取消收藏
        del_collect(good_id) {
            let url = '/mini/Collect/del', vm = this, data = { good_id: good_id };
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.good_info.is_collect = 0;
                    vm.$msg(res.msg);
                } else {
                    vm.handleError(res)
                }
            })
        },
        //获取商品信息
        get_info(id) {
            let url = '/mini/Good/detail?id=' + id,
                vm = this;

            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.good_info = res.data;

                    //分享配置
                    window.wx.config({
                        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                        appId: vm.good_info.share_ticket.appId, // 必填，公众号的唯一标识
                        timestamp: vm.good_info.share_ticket.timestamp, // 必填，生成签名的时间戳
                        nonceStr: vm.good_info.share_ticket.nonceStr, // 必填，生成签名的随机串
                        signature: vm.good_info.share_ticket.signature, // 必填，签名，见附录1
                        jsApiList: [
                            'onMenuShareTimeline',
                            'onMenuShareAppMessage'
                        ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                    });

                    window.wx.ready(function(){
                        // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
                        var title = vm.good_info.good_name;
                        var desc = vm.good_info.good_title;
                        var link = location.href;
                        var imgUrl = 'http://'+location.host+vm.good_info.good_img;
                        var dataUrl = '';
                        var type = '';
                        window.wx.onMenuShareAppMessage({
                            title: title, // 分享标题
                            desc: desc, // 分享描述
                            link: link, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                            imgUrl: imgUrl, // 分享图标
                            type: type, // 分享类型,music、video或link，不填默认为link
                            dataUrl: dataUrl, // 如果type是music或video，则要提供数据链接，默认为空
                            success: function () { 
                                // 用户确认分享后执行的回调函数
                                vm.share_credits();
                            },
                            cancel: function () { 
                                // 用户取消分享后执行的回调函数
                            }
                        });
                        window.wx.onMenuShareTimeline({
                            title: title, // 分享标题
                            link: link, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                            imgUrl: imgUrl, // 分享图标
                            success: function () { 
                                // 用户确认分享后执行的回调函数
                                vm.share_credits();
                            },
                            cancel: function () { 
                                // 用户取消分享后执行的回调函数
                            }
                        });
                    });
                } else {
                    vm.handleError(res)
                }
            })
        },
        //分享成功，给用户添加相应的积分
        share_credits() {
            let url = '/mini/Home/share_credits', vm = this, data = { good_id: good_id };
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.$msg(res.msg);
                } else {
                    vm.handleError(res)
                }
            })
        },
        loadMore() {
            let page = parseInt(this.comment_pages.current_page, 10);
            clearTimeout(this.timer)
            this.timer = setTimeout(() => {
                if (page < this.comment_pages.total_page) {
                    this.get_comment(page + 1);
                }
            }, 500)


        },
        //搜索
        onSearch(keyword) {
            this.search.keyword = keyword;
            let searchData = { keyword: this.search.keyword }
            this.get_comment(1, searchData)
        },
        //获取评论信息
        get_comment(page, searchData) {
            this.loadComment = true;
            page = page || 1;
            let url = '/mini/Comment/get_list?good_id=' + this.good_id + '&page=' + page,
                vm = this;

            this.apiGet(url, searchData).then(function(res) {
                vm.loadComment = false;
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
    mounted() {
        this.setTitle('商品详情')
        this.good_id = this.$route.params.id;
        this.get_info(this.good_id);
        //  this.get_comment(1);

        
    },
}

</script>
