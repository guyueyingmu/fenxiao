<template>
    <div class="reg">
        <div class="top_tips">
            <i class="iconfont icon-tanhao"></i> 请您绑定手机号码，以便更好的为您服务</div>
        <div class="form-item">
            <input type="tel" v-model="form.tel" v-focus placeholder="请输入您的手机号码" maxlength="11">
            <span class="form-err-tips" v-show="form.tel.length && getCodeDisabled == 1">手机号不正确</span>
        </div>
        <div class="form-item">
            <div class="f-l">
                <input type="tel" maxlength="4" placeholder="请输入验证码" v-model="form.c">
            </div>
            <div class="f-r"> <img src="" alt="" width="92" height="42"> </div>

        </div>
        <div class="form-item">
            <div class="f-l">
                <input type="tel" maxlength="4" :disabled="getCodeDisabled == 1" placeholder="请输入您的验证码" v-model="form.code">
            </div>
            <div class="f-r">
                <button type="button" :disabled="getCodeDisabled == 1 || codeDisabled == true" class="ui-btn ui-btn-getCode" @click="getCode">{{codeText}}</button>
            </div>

        </div>
        <div class="form-item">
            <button type="button" :disabled="form_disabled == 1" class="ui-btn ui-btn-block ui-btn-l2">{{sub_text}}</button>
        </div>
        <div class="form-item">
            <div>
                <input type="checkbox" v-model="form.agree" class="ui-checkbox" id="agree">
                <label for="agree">已阅读并同意</label>
                <a href="#" class="skin">《爱悦妍服务协议》</a>
            </div>
        </div>

    </div>
</template>
<script>
import http from '@/assets/js/http'
export default {
    mixins: [http],
    mounted() {
        this.setTitle('绑定手机')
        document.body.style.background = '#fff'

    },
    data() {
        return {
            sub_text: '立即绑定',
            codeDisabled: false,
            form: {
                tel: '',
                code: '',
                agree: '',
                c: ''
            },
            codeText: '获取验证码',
            t: ''
        }
    },
    computed: {
        getCodeDisabled: function() {
            if (/^1[3-9]\d{9}$/.test(this.form.tel)) {
                return 0
            } else {
                return 1
            }

        },
        form_disabled: function() {
            if (/^1[3-9]\d{9}$/.test(this.form.tel) && this.form.code.length == 4) {
                if (this.form.agree) {
                    this.sub_text = '立即绑定'
                    return 0;
                } else {
                    this.sub_text = '请同意服务协议'
                    return 1
                }

            } else {
                return 1
            }
        }
    },
    methods: {
        getCode() {
            let that = this;
            var cb = function() {
                that.codeDisabled = true;
                var n = 59;

                clearInterval(that.t)
                var msg = '60s重试'
                that.t = setInterval(function() {
                    if (n > 0) {
                        n = parseInt(n) - 1;
                        that.codeText = n + 's重试'
                    } else {
                        clearInterval(that.t)
                        n = 59;
                        that.codeText = '获取验证码'
                        that.codeDisabled = false;
                    }
                }, 100)
            }
            this.getCodeAjax(cb);
        },
        getCodeAjax(cb) {
            let vm = this, url = "";
            cb()
            this.apiGet(url).then((res) => {
                if (res.code) {
                    if (typeof cb == 'function') {
                        cb()
                    }
                } else {

                }
            })
        }
    }

}

</script>
