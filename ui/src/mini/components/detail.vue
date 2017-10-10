<template>
    <div class="detail">
        <!--商品图片尺寸  660 * 520-->
        <swiper :options="swiperOption">
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
                 <!-- 收藏成功 -->
                <i class="iconfont icon-shoucang-on" v-if="good_info.is_collect == 1" @click="del_collect(good_info.id)"></i>  
                <i v-else class="iconfont icon-xin" @click="add_collect(good_info.id)"></i>
                
                <em>收藏</em>
            </div>
            <div class="item btn myCart" @click="goto('/cart')">
                <i class="iconfont icon-gouwuche"></i>
                <em>购物车</em>
                <i class="num">{{good_info.cart_total}}</i>
            </div>
            <div class="item add" @click="add_cart(good_info.id);">加入购物车</div>
            <div class="item buy" @click="go_buy(good_info.id)">立即购买</div>

        </div>

        <div class="tags">
            <span :class="{'active':tagActive == 0}" @click="switchTags(0)">商品介绍</span>
            <span class="a-line"></span>
            <span :class="{'active':tagActive == 1}" @click="switchTags(1)">用户评论({{good_info.comment_total}})</span>
        </div>

        <div class="content minHeight200" v-show="tagActive == 0">
            {{good_info.detail}}
            <!-- <img src="static/mini/img/demo/detail/d0.jpg">
                <img src="static/mini/img/demo/detail/d1.jpg">
                <img src="static/mini/img/demo/detail/d2.jpg">
                <img src="static/mini/img/demo/detail/d3.jpg">
                <img src="static/mini/img/demo/detail/d4.jpg">
                <img src="static/mini/img/demo/detail/d5.jpg">
                <img src="static/mini/img/demo/detail/d6.jpg"> -->
        </div>

        <div class="comment-list minHeight200" v-show="tagActive == 1"  v-loading.full="loadComment">

            <div class="main">
                <div class="tagsLabel">
                    <span :class="{'active':search.keyword == 0}" @click="onSearch(0)">全部</span>
                    <span :class="{'active':search.keyword == 1}" @click="onSearch(1)">好评</span>
                    <span :class="{'active':search.keyword == 2}" @click="onSearch(2)">中评</span>
                    <span :class="{'active':search.keyword == 3}" @click="onSearch(3)">差评</span>
                </div>
                <ul class="ui-comment" v-if="comment_list.length > 0" v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
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
                autoHeight: true,

            },
            // swiperSlides: [
            //     'static/mini/img/demo/detail/2.png',
            //     'static/mini/img/demo/detail/1.png',
            //     'static/mini/img/demo/detail/3.png',
            // ],
            tagActive: 0,
            loadComment: false,
            comment_list:[],
            comment_pages: {},
            good_info: {},
            good_id: 0,
            search: {
                keyword: 0,
            }
        }
    },
    methods: {
        switchTags(idx) {
            this.tagActive = idx;
            if(idx == 1 && this.comment_list.length < 1){
                   this.loadComment = true;
                   let vm  = this;
                   vm.get_comment(1);
                   setTimeout(()=>{
                       vm.loadComment = false
                   },1000)
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
                            console.log(olddata.length)
                            if (olddata.length < 10) {
                                olddata.splice(0, 0, data);
                            }

                        }
                    }

                } else {
                    olddata = []
                    olddata.splice(0, 0, data);
                }

                //if (olddata.length < 10) {
                    window.localStorage.setItem("__history__", JSON.stringify(olddata))
               // }
            }

        },
        //立即购买
        go_buy(good_id){
            let params = [{good_id:good_id, good_count:1}]
            this.setCart(params)
            this.goto('/confirm');
        },
        //加入购物车
        add_cart(good_id){
            let url = '/mini/Cart/add', vm = this, data = {good_id: good_id};
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
        add_collect(good_id){
            let url = '/mini/Collect/add', vm = this, data = {good_id: good_id};
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
        del_collect(good_id){
            let url = '/mini/Collect/del', vm = this, data = {good_id: good_id};
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
        get_info(id){
            let url = '/mini/Good/detail?id='+id,
                vm = this;

            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.good_info = res.data;
                } else {
                    vm.handleError(res)
                }
            })
        },
        loadMore(){
            let page = parseInt(this.comment_pages.current_page) + 1;
            if(page > this.comment_pages.total_page){
                return false;
            }else{
                if(this.comment_pages.current_page > 1){
                    this.get_comment(page);
                }
            }            
        },
        //搜索
        onSearch(keyword) {
            this.search.keyword = keyword;
            let searchData = {keyword: this.search.keyword}
            this.get_comment(1, searchData)
        },
        //获取评论信息
        get_comment(page, searchData){
            page = page || 1;
            let url = '/mini/Comment/get_list?good_id=' + this.good_id + '&page=' + page,
                vm = this;

            this.apiGet(url, searchData).then(function(res) {
     
                if (res.code) {
                    if(page < 2){
                       vm.comment_list = res.data.list;
                                 console.log(JSON.stringify(res.data.list,null,4))
                    }else{
                 
                        
                       let _list = vm.comment_list;
                       _list.concat(res.data.list)
                       console.log(JSON.stringify(_list,null,4))
                      vm.comment_list = _list;
                    }                    
                    vm.comment_pages = res.data.pages;
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
