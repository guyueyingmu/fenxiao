<style lang="less">
.slef-card.liv {
    width: 180px;
    margin-bottom: 0;
    padding: 0;
    &:after {
        content: normal;
    }
}
</style>

<template>
    <div>
        <div class="user">
            <div class="userBox">
                <div class="photo">
                    <!-- static/mini/img/demo/avt.jpg -->
                    <a href="javascript:;"><img :src="info.img_url" width="100%"> </a>
                </div>
                <div class="inners">
                    <div class="name">{{info.nickname}}</div>
                    <div class="cardMsg" v-if="info.distribution_level == 2">
                        <span class="infor-tit">营业总额：</span>
                        <span class="infor-inte">
                            <a href="javascript:;">￥{{info.business_total_amount}}</a>
                        </span>
                    </div>
                </div>

                <ul class="slef-card liv" v-if="info.distribution_level != 2">
                    <li>
                        <a href="javascript:;" @click="goto('/jifen')">
                            <span class="num">{{info.credits||0}}</span>
                            <span class="tit">积分</span>
                        </a>
                    </li>
                    <li>
                        <div class="qindao" v-if="!qiandao" @click="onQiandao">
                            <em>立即签到</em>
                        </div>
                        <a href="javascript:;" v-else @click="goto('/qiandao')">
                            <span class="num">{{info.sign_total|| 0}}天</span>
                            <span class="tit">已签到</span>
                        </a>
                    </li>
                </ul>

                <div class="qrcode" v-if="info.distribution_level == 2">
                    <i class="iconfont icon-erweima"></i>
                    <div>分销</div>
                </div>
            </div>
            <ul class="slef-card" v-if="info.distribution_level == 2">
                <li>
                    <a href="javascript:;">
                        <span class="num">￥{{info.earn_total||0}}</span>
                        <span class="tit">佣金</span>
                    </a>
                </li>
                <li>

                    <a href="javascript:;" @click="goto('/jifen')">
                        <span class="num">{{info.credits||0}}</span>
                        <span class="tit">积分</span>
                    </a>
                </li>
                <li>

                    <div class="qindao" v-if="!qiandao" @click="onQiandao">
                        <em>立即签到</em>
                    </div>
                    <a href="javascript:;" v-else @click="goto('/qiandao')">
                        <span class="num">{{info.sign_total|| 0}}天</span>
                        <span class="tit">已签到</span>
                    </a>
                </li>
            </ul>
        </div>
        <div v-if="info.distribution_level != 2" class="space15"></div>
        <ul class="ui-links l-t userServer-list">
            <li @click="goto('/order')">

                <div class="title">
                    <i class="iconfont icon-dingdan"></i> 我的订单</div>
                <div class="des">
                    <span>查看全部订单</span>
                    <i class="iconfont icon-arrow"></i>
                </div>
            </li>

            <li @click="goto('/shouhou')">
                <div class="title">
                    <i class="iconfont icon-shouhoufuwu"></i> 售后记录</div>
            </li>

            <li @click="goto('/address')">
                <div class="title">
                    <i class="iconfont icon-dizhi"></i> 收货地址</div>
            </li>
            <li @click="goto('/favorite')">
                <div class="title">
                    <i class="iconfont icon-wodeshoucang"></i> 我的收藏</div>
            </li>
            <li @click="goto('/history')">
                <div class="title">
                    <i class="iconfont icon-wodezujiline"></i> 我的足迹</div>
            </li>
            <li v-if="info.distribution_level == 0" @click="goto('/daili')">
                <div class="title">
                    <i class="iconfont icon-3"></i> 我要代理</div>
            </li>

            <li>
                <div class="title">
                    <i class="iconfont icon-zhanghao1"></i> 帐号</div>
            </li>
            <!-- <li>
                            <div class="title"><i class="iconfont icon-logout"></i> 退出</div>
                        </li> -->
        </ul>
        <div style="height:20px;"></div>
        <div class="qiandao-animate">
            <transition name="qd2">
                <div class="m" v-show="qiandao_an">
                    <div class="t">+60</div>
                    <div>签到成功！</div>
                </div>
            </transition>

        </div>

    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    name: 'userCenter',
    mixins: [http],
    data() {
        return {
            info: {},
            qiandao: false,
            qiandao_an:false
        }
    },
    methods: {
        //签到
        onQiandao() {
            this.qiandao_an = true
            this.qiandao = true
            let vm = this;
            setTimeout(() => {
               vm.qiandao_an = false
            }, 1000)

        },
        get_info() {
            let url = '/mini/Home/center_info',
                vm = this;

            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.info = res.data;
                } else {
                    vm.handleError(res)
                }
            })
        },
    },
    created() {
        this.setTitle('会员中心')
        this.get_info();
    },
}

</script>
