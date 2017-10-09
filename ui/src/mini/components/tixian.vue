<template>
    <div>
        <ul class="ui-fixd">
            <li class="med">
                <div class="f">
                    <div><span class="grey">提现人：</span>张三</div>
                    <div><span class="grey">提现申请金额：</span>￥600</div>
                    <div><span class="grey">提现申请时间：</span>2017-8-9 12:15:50</div>
                </div>
                <div><span class="ui-label">待处理</span></div>
            </li>
            <li class="med">
                <div class="f">
                    <div><span class="grey">提现人：</span>张三</div>
                    <div><span class="grey">提现申请金额：</span>￥600</div>
                    <div><span class="grey">提现申请时间：</span>2017-8-9 12:15:50</div>
                </div>
                <div><span class="ui-label active">已同意</span></div>
            </li>
        </ul>
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
                        <div class="mint-msgbox-message">{{d.message}}</div>
                        <div class="mint-msgbox-input"><input :placeholder="d.inputPlaceholder" v-focusd v-model="d.inputValue" type="number" >
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
                dialog: false,
                d: {
                    message: '可提现金额：￥899.6',
                    title: '提现',
                    inputValue: '500',
                    inputPlaceholder: '请输入金额',
                }
            }
        },
        methods: {
            get_list() {
                let url = '/mini/Home/get_cat_list?page=1',
                    vm = this;
                this.apiGet(url, {}).then(function(res) {
                    if (res.code) {
                        vm.list = res.data;
                    } else {
                        vm.handleError(res)
                    }
                })
            },
            onTixian() {
                this.dialog = true;
            },
            onSubmit() {
                this.dialog = false;
            }
        },
        mounted() {
            this.setTitle('佣金提现')
            // this.get_list();
    
        }
    }
</script>
