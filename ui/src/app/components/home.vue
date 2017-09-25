<template>
    <div>

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
                        <i class="nav-icon item-1"><img src="static/app/home/icon/1.png" width="100%"></i>
                        <span>热门活动</span>
                    </a>
                </li>
                <li>
                    <a href="groupBuy/index.html">
                        <i class="nav-icon item-2"><img src="static/app/home/icon/2.png" width="100%"></i>
                        <span>团购热卖</span>
                    </a>
                </li>
                <li>
                    <a href="shopList/index.html">
                        <i class="nav-icon item-3"><img src="static/app/home/icon/3.png" width="100%"></i>
                        <span>商户列表</span>
                    </a>
                </li>
                <li>
                    <a href="shopList/detail.html">
                        <i class="nav-icon item-4"><img src="static/app/home/icon/4.png" width="100%"></i>
                        <span>电影院场</span>
                    </a>
                </li>
                <li>
                    <a href="parking/index.html">
                        <i class="nav-icon item-5"><img src="static/app/home/icon/5.png" width="100%"></i>
                        <span>停车缴费</span>
                    </a>
                </li>
                <li>
                    <a href="userCenter/index.html">
                        <i class="nav-icon item-6"><img src="static/app/home/icon/6.png" width="100%"></i>
                        <span>个人中心</span>
                    </a>
                </li>
                <li>
                    <a href="myDiscount/coupon.html">
                        <i class="nav-icon item-7"><img src="static/app/home/icon/7.png" width="100%"></i>
                        <span>我的券包</span>
                    </a>
                </li>
                <li>
                    <a href="introduction/index.html">
                        <i class="nav-icon item-8"><img src="static/app/home/icon/8.png" width="100%"></i>
                        <span>更多</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</template>
<script>

require('swiper/dist/css/swiper.css')
import { swiper, swiperSlide } from 'vue-awesome-swiper'

export default {
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
                'static/app/home/banner/1.jpg',
                'static/app/home/banner/2.jpg',
                'static/app/home/banner/3.jpg',
                'static/app/home/banner/4.jpg',
            ]
        }
    },

    mounted() {


    },

}

</script>
