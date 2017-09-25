<template>
    <div id="app">
        <transition :name="transitionName">

            <router-view class="child-view"> </router-view>

        </transition>
    </div>
</template>

<script>

import http from './assets/js/http'
export default {
    name: 'app',
    mixins: [http],
    watch: {
        '$route'(to, from) {
            const toDepth = to.path.split('/').length
            const fromDepth = from.path.split('/').length
            this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'
        }
    },
    data() {
        return {

            //activeMenuOpen: ['0'],
            transitionName: 'slide-left',
            list: [],
            nickname: '',
            breadcrumb: {
                l1: "",
                l2: ''
            },


        }
    },
    methods: {
        closeLogin() {
            window.location = '/admin/Login/log_out'
        },

        //退出登录
        loginOut() {
            let url = '/admin/Login/log_out', vm = this;
            function _login_out(vm, url) {
                vm.apiGet(url).then(function(res) {
                    if (res.code) {
                        window.location = '/admin/login.html'
                    }
                })
            }

            this.$confirm('是否退出登录?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                //_login_out(vm, url)
                window.location = '/admin/Login/log_out'

            }).catch(() => {
            });
        }
    },
    computed: {
        "minHeight"() {
            return (window.innerHeight - 144) + 'px'
        },
        activeMenu() {
            var _path = this.$route.path;
            if (/goods_add/.test(_path) || /goods_edit/.test(_path) || /order_id/.test(_path) || /user_id/.test(_path)) {
                _path = _path.split('/')
                _path = '/' + _path[1];
            }

            return _path
        },
        activeMenuOpen() {
            var _path = this.$route.path, _array = '10';
            _path = _path.split('/')
            _path = '/' + _path[1];
            if (this.list.length && _path != '/') {
                let data = this.list, _reg = new RegExp(_path)
                for (let i = 0; i < data.length; i++) {
                    let _break = false;
                    for (let k = 0; k < data[i].child.length; k++) {
                        if (_reg.test(data[i].child[k].menu_link)) {
                            _array = i.toString();
                            _break = true;

                            break;
                        }
                    }
                    if (_break) {
                        break;
                    }
                }

                // console.log(`计算菜单`, _array)
            }

            return [_array]

        }
    },
    created() {

        //初始化，获取菜单信息
        let vm = this;
        let url = '/admin/login/get_menu'
        this.apiGet(url).then(function(res) {
            if (res) {
                let data = JSON.parse(res);
                for (let i = 0; i < data.length; i++) {
                    for (let k = 0; k < data[i].child.length; k++) {
                        data[i].child[k].link = data[i].child[k].menu_link.replace('/admin/index/#', '')
                    }
                }
                vm.list = data;
                window.localStorage.setItem('__Menu__', JSON.stringify(data))
                vm.setNavlist(data)

            }
        })
        //用户信息
        this.apiGet('/admin/Login/get_user_info').then(function(res) {
            if (res.code) {
                vm.nickname = res.data.nickname;
            }
        })




    }

}
</script>

