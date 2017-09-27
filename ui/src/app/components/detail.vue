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
            <div class="item"><i class="iconfont icon-kefu2"></i> <em>在线客服</em></div>
            <div  class="item"><i class="iconfont icon-xin"></i> <em>收藏</em></div>
            <div  class="item"><i class="iconfont icon-gouwuche"></i> <em>购物车</em></div>
            <div  class="item add">加入购物车</div>
            <div  class="item buy">立即购买</div>


        </div>

        <div class="tags">
            <span class="active">商品介绍</span>  <span class="a-line"></span>
            <span>用户评论</span>
        </div>
        
        <div class="content">
            <img src="static/app/demo/detail/d0.jpg" >
            <img src="static/app/demo/detail/d1.jpg" >
            <img src="static/app/demo/detail/d2.jpg" >
            <img src="static/app/demo/detail/d3.jpg" >
            <img src="static/app/demo/detail/d4.jpg" >
            <img src="static/app/demo/detail/d5.jpg" >
            <img src="static/app/demo/detail/d6.jpg" >

        </div>


    </div>
</template>
<script>

import http from '@/assets/js/http'
import { swiper, swiperSlide } from 'vue-awesome-swiper'
require('swiper/dist/css/swiper.css')
export default {
    name: 'detail',
    mixins: [http],
    components: {
        swiper,
        swiperSlide
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
                'static/app/demo/detail/2.png',
                'static/app/demo/detail/1.png',
                'static/app/demo/detail/3.png',
            ]
        }
    },
    mounted() {
        this.setTitle('商品详情')

    },
}

</script>
