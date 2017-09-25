<template>
    <div>
    <!--轮播图-->
    <div class="swiper-container home">
        <div class="swiper-wrapper">
            <!--首页轮播图片尺寸  750 * 320-->
            <div class="swiper-slide"><img src="static/app/home/banner/1.jpg" ></div>
            <div class="swiper-slide"><img src="static/app/home/banner/2.jpg" ></div>
            <div class="swiper-slide"><img src="static/app/home/banner/3.jpg" ></div>
            <div class="swiper-slide"><img src="static/app/home/banner/4.jpg" ></div>
            <div class="swiper-slide"><img src="static/app/home/banner/5.jpg" ></div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
          <!--导航-->
    <div class="nav-container">
        <ul>
            <li>
                <a href="recommend/index.html">
                    <i class="nav-icon item-1"><img src="static/app/home/icon/1.png" width="100%" ></i>
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
                    <span>商场简介</span>
                </a>
            </li>

        </ul>
    </div>
    </div>
</template>
<script>

import style from '../../../static/app/css/swiper.min.css'
export default {
    created() {
       
    }

}

</script>
