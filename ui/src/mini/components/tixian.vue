<template>
    <div v-loading.win = "loading">
        <div v-show="list.length > 0" ref="ss" v-infinite-scroll="loadMore" :infinite-scroll-disabled="sloading" :infinite-scroll-immediate-check="false" :infinite-scroll-distance="10" infinite-scroll-listen-for-event="cheackLoadMore">
            <ul class="ui-fixd">
                <li class="med" v-for="(item,idx) in list" :key="idx">
                    <div class="f">
                        <div><span class="grey">提现人：</span>{{item.user_name}}</div>
                        <div><span class="grey">提现申请金额：</span>￥{{item.amount}}</div>
                        <div><span class="grey">提现申请时间：</span>{{item.add_time}}</div>
                    </div>
                    <div><span class="ui-label" :class="{'active': item.status == 2}">{{item.status_txt}}</span></div>
                </li>
                <!-- <li class="med">
                    <div class="f">
                        <div><span class="grey">提现人：</span>张三</div>
                        <div><span class="grey">提现申请金额：</span>￥600</div>
                        <div><span class="grey">提现申请时间：</span>2017-8-9 12:15:50</div>
                    </div>
                    <div><span class="ui-label active">已同意</span></div>
                </li> -->
            </ul>
        </div>
        <div class="spinner" v-if="sloading">
            <mt-spinner  :size="18" color="#26a2ff"></mt-spinner>
        </div>
        <div class="nodata-line" v-else-if="pages.total_page == pages.current_page">没有更多数据了</div>

        <div class="nodata" v-if="list.length < 1 && sloading == false">
            <i class="iconfont icon-tongyongmeiyoushuju"></i>
            <div>您还没有提现记录~</div>
        </div>

        <div class="space"></div>
        <div class="btn-wrap">
            <div class="btn-fixed">
                <button type="button" class="ui-btn ui-btn-block ui-btn-l2" @click="onTixian">申请提现</button>
            </div>
        </div>

        <div class="dialog" v-if="dialog">
            <div class="mint-msgbox-wrapper" style="position: absolute; z-index: 2099;">
                <div class="mint-msgbox">
                    <div class="mint-msgbox-header">
                        <div class="mint-msgbox-title">{{d.title}}</div>
                    </div>
                    <div class="mint-msgbox-content">
                        <div class="mint-msgbox-message">可提现金额：￥{{d.account}}</div>
                        <div class="mint-msgbox-input"><input :placeholder="d.inputPlaceholder" v-focusd v-model="d.inputValue" type="number">
                        </div>
                        <div class="space"></div>
                    </div>
                    <div class="mint-msgbox-btns">
                        <button class="mint-msgbox-btn mint-msgbox-cancel " @click="dialog = false">取消</button>
                        <button class="mint-msgbox-btn mint-msgbox-confirm " @click="onSubmit">确定</button></div>
                </div>
            </div>
            <div class="v-modal" style="z-index: 2000;"></div>
        </div>
    </div>
</template>
<script>
    import http from '@/assets/js/http'
    export default {
        name: 'tixian',
        mixins: [http],
        data() {
            return {
                list: [],
                pages: {},                
                sloading: false,
                dialog: false,
                d: {
                    account: 0,
                    title: '提现',
                    inputValue: '500',
                    inputPlaceholder: '请输入金额',
                }
            }
        },
        methods: {
            //获取可提现金额
            get_account(){
                let url = '/mini/Home/center_info',
                    vm = this;
                this.apiGet(url, {}).then(function(res) {
                    if (res.code) {
                        vm.d.account = vm.d.inputValue = res.data.account_balance;
                    } else {
                        vm.handleError(res)
                    }
                })
            },
            loadMore() {
                if (this.sloading) { return }
                let page = parseInt(this.pages.current_page, 10) || 1;
                if (page < this.pages.total_page) {
                    this.get_list(page + 1);
                }

            },
            get_list(page) {
                page = page || 1;
                let url = '/mini/Withdraw/get_list?page=' + page,
                    vm = this;
                vm.sloading = true
                this.apiGet(url, {}).then(function(res) {
                    if (res.code) {
                        vm.pages = res.data.pages;
                        if (vm.list.length == 0) {
                            vm.list = res.data.list;


                        } else {
                            let _list = vm.list;
                            vm.list = _list.concat(res.data.list)


                        }
                        setTimeout(() => {
                            vm.sloading = false;
                            vm.$emit('cheackLoadMore')
                        }, 200)
                    } else {
                        vm.handleError(res)
                    }
                })
            },
            onTixian() {
                this.dialog = true;
            },
            onSubmit() {
                let url = '/mini/Withdraw/add_apply',
                    vm = this;
                    this.loading = true;
                this.apiGet(url, {withdraw_amount : this.d.inputValue}).then(function(res) {
                    vm.loading = false;
                    if (res.code) {
                        vm.$msg(res.msg);
                        vm.dialog = false;
                        vm.list.unshift(res.data);
                    } else {
                        vm.dialog = false;
                        vm.handleError(res)
                    }
                })                
            }
        },
        created() {
            this.setTitle('佣金提现')
            this.get_list();
            this.get_account();
        }
    }
</script>
