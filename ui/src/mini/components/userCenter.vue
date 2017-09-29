<template>
    <div>
        <div class="user">
            <div class="userBox">
                <div class="photo">
                    <a href="javascript:;"><img src="static/mini/img/demo/avt.jpg" width="100%"> </a>
                </div>
                <div class="inners">
                    <div class="name">赵三多</div>
                    <div class="cardMsg">
                        <span class="infor-tit">营业总额：</span>
                        <span class="infor-inte">
                            <a href="javascript:;">￥108909</a>
                        </span>
                    </div>
                </div>
                <div class="qrcode">
                    <i class="iconfont icon-erweima"></i>
                    <div>分销</div>
                </div>
            </div>
            <ul class="slef-card">
                <li>
                    <a href="javascript:;">
                        <span class="num">￥1082.00</span>
                        <span class="tit">佣金</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:;">
                        <span class="num">200</span>
                        <span class="tit">积分</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:;">
                        <span class="num">12天</span>
                        <span class="tit">已签到</span>
                    </a>
                </li>
            </ul>
        </div>

        <ul class="ui-links l-t userServer-list">
            <li >
                
                <div class="title"><i class="iconfont icon-dingdan"></i> 我的订单</div>
                <div class="des">
                    <span>查看全部订单</span>
                    <i class="iconfont icon-arrow"></i>
                </div>
            </li>

            <li>
                <div class="title"><i class="iconfont icon-shouhoufuwu"></i> 售后记录</div>
            </li>

            <li>
                <div class="title"><i class="iconfont icon-dizhi"></i> 收货地址</div>
            </li>
            <li>
                <div class="title"><i class="iconfont icon-wodeshoucang"></i> 我的收藏</div>
            </li>
            <li>
                <div class="title"><i class="iconfont icon-wodezujiline"></i> 我的足迹</div>
            </li>
            <li>
                <div class="title"><i class="iconfont icon-3"></i> 我要代理</div>
            </li>

            <li>
                <div class="title"><i class="iconfont icon-zhanghao1"></i> 帐号</div>
            </li>
            <li>
                <div class="title"><i class="iconfont icon-logout"></i> 退出</div>
            </li>
        </ul>
        <div style="height:20px;"></div>

    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    name: 'userCenter',
    mixins: [http],
    data() {
        return {
            info:{}
        }
    },
    methods: {
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
    mounted() {
        this.setTitle('会员中心')
        this.get_info();
    },
}

</script>
