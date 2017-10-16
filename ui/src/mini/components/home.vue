<template>
    <div class="home">

        <!--首页轮播图片尺寸  750 * 320-->
        <swiper :options="swiperOption" :style="setbannerHeight">
            <swiper-slide v-for="(slide,k) in swiperSlides" :key="k" >
                <img v-lazy="slide.img_url">
            </swiper-slide>
            <div class="swiper-pagination" slot="pagination"></div>
        </swiper>

        <!--导航-->
        <div class="nav-container">
            <ul>
                <li v-for="(cat, k) in cat_list" :key="k">
                    <a href="javascript:;" @click="goto('/search/cat_id/'+cat.id)">
                        <i class="nav-icon item-1"><img v-lazy="cat.cat_img" width="100%"></i>
                        <span>{{cat.cat_name}}</span>
                    </a>
                </li>
              
                <li>
                    <a href="javascript:;" @click="goto('/class')">
                        <i class="nav-icon item-8"><img src="/static/mini/img/home/icon/8.png" width="100%"></i>
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
                <div class="thumb" @click="goto('/detail/id/'+good.id)"><img v-lazy="good.good_img"></div>
                <div class="title" @click="goto('/detail/id/'+good.id)">
                    {{good.good_title}}
                </div>
                <div class="info">
                    <span class="price" v-if="good.good_type == 4 || good.good_type == 5">积分
                        <em>{{good.credits}}</em>
                    </span>
                    <span class="price" v-else>￥
                        <em>{{good.price}}</em>
                    </span>
                    <i class="iconfont icon-gouwuche1" v-if="good.good_type == 1" @click="add_cart(good.id);"></i>
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
    computed:{
        setbannerHeight(){
            let a = window.innerWidth/2.34375;
           return {'height':a +'px'}
        }
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
              
               

            },
            swiperSlides: [ ],
            cat_list: [],
            good_list: []
        }
    },
    methods: {
        //加入购物车
        add_cart(good_id){
            let url = '/mini/Cart/add', vm = this, data = {good_id: good_id};
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.$msg(res.msg);
                } else {
                    vm.handleError(res)
                }
            })
        },
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

    created() {
        this.setTitle('首页')

        this.get_banner();
        this.get_cat();
        this.get_good();

    },

}

</script>
