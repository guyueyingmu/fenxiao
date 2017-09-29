<template>
    <div class="home">

        <!--首页轮播图片尺寸  750 * 320-->
        <swiper :options="swiperOption">
            <swiper-slide v-for="slide in swiperSlides" :key="slide">
                <img :src="slide">
            </swiper-slide>
            <div class="swiper-pagination" slot="pagination"></div>
        </swiper>

        <!--导航-->
        <div class="nav-container">
            <ul>
                <li>
                    <a href="recommend/index.html">
                        <i class="nav-icon item-1"><img src="static/mini/img/home/icon/1.png" width="100%"></i>
                        <span>热门活动</span>
                    </a>
                </li>
                <li>
                    <a href="groupBuy/index.html">
                        <i class="nav-icon item-2"><img src="static/mini/img/home/icon/2.png" width="100%"></i>
                        <span>团购热卖</span>
                    </a>
                </li>
                <li>
                    <a href="shopList/index.html">
                        <i class="nav-icon item-3"><img src="static/mini/img/home/icon/3.png" width="100%"></i>
                        <span>商户列表</span>
                    </a>
                </li>
                <li>
                    <a href="shopList/detail.html">
                        <i class="nav-icon item-4"><img src="static/mini/img/home/icon/4.png" width="100%"></i>
                        <span>电影院场</span>
                    </a>
                </li>
                <li>
                    <a href="parking/index.html">
                        <i class="nav-icon item-5"><img src="static/mini/img/home/icon/5.png" width="100%"></i>
                        <span>停车缴费</span>
                    </a>
                </li>
                <li>
                    <a href="userCenter/index.html">
                        <i class="nav-icon item-6"><img src="static/mini/img/home/icon/6.png" width="100%"></i>
                        <span>个人中心</span>
                    </a>
                </li>
                <li>
                    <a href="myDiscount/coupon.html">
                        <i class="nav-icon item-7"><img src="static/mini/img/home/icon/7.png" width="100%"></i>
                        <span>我的券包</span>
                    </a>
                </li>
                <li>
                    <a href="introduction/index.html">
                        <i class="nav-icon item-8"><img src="static/mini/img/home/icon/8.png" width="100%"></i>
                        <span>更多</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="page-title recommend ">
            <span class="title">推荐商品</span>
            <span class="more">更多
                <i class="iconfont icon-arrow"></i>
            </span>
        </div>
        <ul class="thumb-box">
            <li>
                <div class="thumb" @click="goto('/detail')"><img src="static/mini/img/demo/2.png"></div>
                <div class="title" @click="goto('/detail')">
                    诺基亚6 (Nokia6) 4GB+64GB 黑色 全网通 双卡双待 移动联通诺基亚6 (Nokia6) 4GB+64GB 黑色 全网通 双卡双待 移动联通
                </div>
                <div class="info">
                    <span class="price">￥
                        <em>390.00</em>
                    </span>
                    <i class="iconfont icon-gouwuche1"></i>
                </div>
            </li>
            <li>
                <div class="thumb" @click="goto('/detail')"><img src="static/mini/img/demo/1.png"></div>
                <div class="title" @click="goto('/detail')">
                    德国 进口牛奶 欧德堡（Oldenburger）超高温处理全脂纯牛奶超高温处理全脂纯牛奶
                </div>
                <div class="info">
                    <span class="jifen">积分 390</span>
                </div>
            </li>
            <li>
                <div class="thumb" @click="goto('/detail')"><img src="static/mini/img/demo/3.png"></div>
                <div class="title" @click="goto('/detail')">
                    维达(Vinda) 抽纸 超韧3层130抽软抽*24包(小规格) 整箱销售
                </div>
                <div class="info">
                    <span class="price">￥
                        <em>390.00</em>
                    </span>
                    <i class="iconfont icon-gouwuche1"></i>
                </div>
            </li>
            <li>
                <div class="thumb" @click="goto('/detail')"><img src="static/mini/img/demo/4.png"></div>
                <div class="title" @click="goto('/detail')">
                    联想(Lenovo)小新潮7000 13.3英寸超轻薄窄边框笔记本电脑(i
                </div>
                <div class="info">
                    <span class="price">￥
                        <em>5999.00</em>
                    </span>
                    <i class="iconfont icon-gouwuche1"></i>
                </div>
            </li>

        </ul>
    </div>
</template>
<script>

require('swiper/dist/css/swiper.css')
import { swiper, swiperSlide } from 'vue-awesome-swiper'
import http from '@/assets/js/http'
export default {
    name: 'home',
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
                paginationClickable: true,
                mousewheelControl: true,
                observeParents: true,
                autoHeight: true,

            },
            swiperSlides: [
                'static/mini/img/home/banner/1.jpg',
                'static/mini/img/home/banner/2.jpg',
                'static/mini/img/home/banner/3.jpg',
                'static/mini/img/home/banner/4.jpg',
            ]
        }
    },

    mounted() {
        this.setTitle('首页')


    },

}

</script>
