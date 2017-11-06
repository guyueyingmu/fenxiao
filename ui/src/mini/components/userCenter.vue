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
                    <a href="javascript:;"><img v-lazy="info.img_url" width="100%"> </a>
                </div>
                <div class="inners">
                    <div class="name">{{info.nickname}}</div>
                    <div class="cardMsg" v-if="info.distribution_level == 2 && info.user_center_sale_total_show == 1">
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
                        <div class="qindao" v-if="!info.sign_in" @click="onQiandao">
                            <em>立即签到</em>
                        </div>
                        <a href="javascript:;" v-else @click="goto('/qiandao')">
                            <span class="num">{{info.sign_total|| 0}}天</span>
                            <span class="tit">已签到</span>
                        </a>
                    </li>
                </ul>

                <div class="qrcode" v-if="info.distribution_level == 2" @click="qrcode = true">
                    <i class="iconfont icon-erweima"></i>
                    <div>分销</div>
                </div>
            </div>
            <ul class="slef-card" v-if="info.distribution_level == 2">
                <li>
                    <a href="javascript:;" @click="goto('/tixian')">
                        <span class="num">￥{{info.account_balance||0}}</span>
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

                    <div class="qindao" v-if="!info.sign_in" @click="onQiandao">
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

            <li @click="goto('/yongjin')">
                <div class="title">
                    <i class="iconfont icon-yongjin"></i> 佣金记录</div>
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

            <li @click="goto('/user')">
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
                    <div class="t">积分 +{{sign_in_num}}</div>
                    <div>签到成功！</div>
                </div>
            </transition>
        </div>
        <div class="qrcode-dialog" v-if="qrcode" @click="qrcode = false">
            <img :src="info.dis_qrcode">
            <div class="tit">{{info.nickname}}的推广二维码</div>
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
            qiandao_an: false,
            qrcode: false,
            sign_in_num: 0,
        }
    },
    methods: {
        //签到
        onQiandao() {
            let url = '/mini/Home/signin',
                vm = this;

            this.apiGet(url, {}).then(function(res) {
                if (res.code) {
                    vm.sign_in_num = res.data;
                    vm.qiandao_an = true;
                    vm.info.sign_in = true;
                    vm.info.sign_total ++;
                    vm.info.credits = parseInt(vm.info.credits) + parseInt(vm.sign_in_num);
                    setTimeout(() => {
                        vm.qiandao_an = false
                    }, 1000)
                } else {
                    vm.handleError(res)
                }
            })
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
