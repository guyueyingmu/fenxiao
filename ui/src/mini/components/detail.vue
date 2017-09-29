<template>
    <div class="detail">
        <!--商品图片尺寸  660 * 520-->
        <swiper :options="swiperOption">
            <swiper-slide v-for="slide in swiperSlides" :key="slide">
                <img :src="slide">
            </swiper-slide>
            <div class="swiper-pagination" slot="pagination"></div>
        </swiper>
        <div class="header">
            <h2 class="title">SK-II"大眼眼霜"呵宠礼盒（微肌因眼霜15g+护肤精华露10ml+嫩肤清莹露30ml）（护肤套装 化妆品套装）</h2>
            <div class="info">
                <span class="price">￥
                    <em>390.00</em>
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
                <!-- <i class="iconfont icon-shoucang-on"></i>   -->
                
                <em>收藏</em>
            </div>
            <div class="item btn myCart" @click="goto('/cart')">
                <i class="iconfont icon-gouwuche"></i>
                <em>购物车</em>
                <i class="num">5</i>
            </div>
            <div class="item add">加入购物车</div>
            <div class="item buy" @click="goto('confirm')">立即购买</div>

        </div>

        <div class="tags">
            <span :class="{'active':tagActive == 0}" @click="switchTags(0)">商品介绍</span>
            <span class="a-line"></span>
            <span :class="{'active':tagActive == 1}" @click="switchTags(1)">用户评论(3)</span>
        </div>

        <div class="content minHeight200" v-show="tagActive == 0">
            <img src="static/mini/img/demo/detail/d0.jpg">
            <img src="static/mini/img/demo/detail/d1.jpg">
            <img src="static/mini/img/demo/detail/d2.jpg">
            <img src="static/mini/img/demo/detail/d3.jpg">
            <img src="static/mini/img/demo/detail/d4.jpg">
            <img src="static/mini/img/demo/detail/d5.jpg">
            <img src="static/mini/img/demo/detail/d6.jpg">
        </div>

        <div class="comment-list minHeight200" v-show="tagActive == 1"  v-loading.full="loadComment">
            <!-- <div class="noComment">
                                    <i class="iconfont icon-zanwuxinxi"></i>
                                    暂无评论
                                </div> -->

            <div class="main" v-if="comment_list.length > 0">
                <div class="tagsLabel">
                    <span class="active">全部</span>
                    <span>好评</span>
                    <span>中评</span>
                    <span>差评</span>
                </div>
                <ul class="ui-comment">
                    <li>
                        <div class="row">
                            <div class="pic"><img src="static/mini/img/demo/avt.jpg"> </div>
                            <div class="name">张三</div>
                            <div class="time">2017-3-23 08:36</div>
                        </div>
                        <div>
                            <rate :value="4" :readonly="true" :text="true"></rate>
                        </div>

                        <div class="des">服务很不错，技师手法力度得道，不会很痛，挺有效果的，推荐了其他妈咪一起过来</div>
                        <div class="imgList">
                            <img src="static/mini/img/demo/1.png">
                            <img src="static/mini/img/demo/3.png">
                            <img src="static/mini/img/demo/2.png">
                            <img src="static/mini/img/demo/4.png">
                        </div>
                    </li>
                    <li>
                        <div class="row">
                            <div class="pic"><img src="static/mini/img/demo/avt.jpg"> </div>
                            <div class="name">无法无天</div>
                            <div class="time">2017-3-23 08:36</div>
                        </div>
                        <div>
                            <rate :readonly="true" :text="true"></rate>
                        </div>

                        <div class="des">服务很不错，技师手法力度得道，不会很痛，挺有效果的，推荐了其他妈咪一起过来</div>
                    </li>
                    <li>
                        <div class="row">
                            <div class="pic"><img src="static/mini/img/demo/avt.jpg"> </div>
                            <div class="name">爱就是一个字</div>
                            <div class="time">2017-3-23 08:36</div>
                        </div>
                        <div>
                            <rate :readonly="true" :text="true"></rate>
                        </div>
                        <div class="des">服务很不错，技师手法力度得道，不会很痛，挺有效果的，推荐了其他妈咪一起过来</div>
                    </li>
                </ul>
              
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
            swiperSlides: [
                'static/mini/img/demo/detail/2.png',
                'static/mini/img/demo/detail/1.png',
                'static/mini/img/demo/detail/3.png',
            ],
            tagActive: 0,
            loadComment: false,
            comment_list:[]
        }
    },
    methods: {
        switchTags(idx) {
            this.tagActive = idx;
            if(idx == 1){
                   this.loadComment = true;
                   let vm  = this;
                   setTimeout(()=>{
                       vm.loadComment = false
                       vm.comment_list.push(1)
                   },1000)
            }
         

        }
    },
    mounted() {
        this.setTitle('商品详情')

    },
}

</script>
