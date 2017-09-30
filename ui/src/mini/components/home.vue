<template>
    <div class="home">

        <!--首页轮播图片尺寸  750 * 320-->
        <swiper :options="swiperOption">
            <swiper-slide v-for="(slide,k) in swiperSlides" :key="k">
                <img :src="slide.img_url">
            </swiper-slide>
            <div class="swiper-pagination" slot="pagination"></div>
        </swiper>

        <!--导航-->
        <div class="nav-container">
            <ul>
                <li v-for="(cat, k) in cat_list" :key="k">
                    <a href="javascript:;" @click="goto('/search/cat_id/'+cat.id)">
                        <i class="nav-icon item-1"><img :src="cat.cat_img" width="100%"></i>
                        <span>{{cat.cat_name}}</span>
                    </a>
                </li>
                <!-- <li>
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
                </li> -->
                <li>
                    <a href="/#/class">
                        <i class="nav-icon item-8"><img src="static/mini/img/home/icon/8.png" width="100%"></i>
                        <span>更多</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="page-title recommend ">
            <span class="title">推荐商品</span>
            <span class="more" @click="goto('/search')">更多
                <i class="iconfont icon-arrow"></i>
            </span>
        </div>
        <ul class="thumb-box">
            <li v-for="(good,k) in good_list" :key="k">
                <div class="thumb" @click="goto('/detail/id/'+good.id)"><img :src="good.good_img"></div>
                <div class="title" @click="goto('/detail/id/'+good.id)">
                    {{good.good_title}}
                </div>
                <div class="info">
                    <span class="price">￥
                        <em>{{good.price}}</em>
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
                // 'static/mini/img/home/banner/1.jpg',
                // 'static/mini/img/home/banner/2.jpg',
                // 'static/mini/img/home/banner/3.jpg',
                // 'static/mini/img/home/banner/4.jpg',
            ],
            cat_list: [],
            good_list: []
        }
    },
    methods: {
        get_banner() {
            let url = '/mini/Home/banner',
                vm = this;

            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.swiperSlides = res.data;
                } else {
                    vm.handleError(res)
                }
            })
        },
        get_cat() {
            let url = '/mini/Home/get_cat_list?page=1',
                vm = this;

            this.apiGet(url, {limit: 7}).then(function(res) {
                if (res.code) {
                    vm.cat_list = res.data;
                } else {
                    vm.handleError(res)
                }
            })
        },
        get_good() {
            let url = '/mini/Good/get_list?page=1',
                vm = this;

            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.good_list = res.data.list;
                } else {
                    vm.handleError(res)
                }
            })
        },
    },

    mounted() {
        this.setTitle('首页')

        this.get_banner();
        this.get_cat();
        this.get_good();

    },

}

</script>
