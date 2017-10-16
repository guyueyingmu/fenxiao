<template>
    <div class="search-page">
        <div class="search-history">
            <div class="header" v-if="$store.state.hList.length > 0">
                <span class="title">最近搜索：</span>
                <a herf="javascript:;" @click="clear">清空历史</a>
            </div>
            <div class="h-list" v-if="$store.state.hList.length > 0">
                <span class="label" v-for="item in $store.state.hList" :key="item" @click="getSearch(item)">{{item}}</span>
            </div>
        </div>
        <div class="header-sort-mask" @click="closeDialog" v-if="showCat ==true"></div>
        <div class="header-sort">
            <div class="item" @click="showCat = !showCat" :class="{'active':showCat || $store.state.search.cat_idx }">
                <i class="iconfont icon-fenlei"></i>
                <span v-if="cat_list.length > 1">{{$store.state.search.cat_idx?cat_list[$store.state.search.cat_idx].cat_name:'分类'}}</span>
            </div>
            <div class="item sort" :class="{'active':$store.state.sort,'skin':sort}" @click="onSort">
                <i class="iconfont icon-paixu"></i> 价格</div>
            <transition name="cat">
                <div class="class-dialog" v-show="showCat">
                    <div class="scroll" v-if="cat_list.length > 0">
                        <scroller>
                            <span v-for="(i,cat_idx) in cat_list" :key="i.id" :class="{'active':cat_idx == $store.state.search.cat_idx}" @click="selectCat(cat_idx)">{{i.cat_name}}
                                <i class="iconfont icon-dagou" v-if="cat_idx == $store.state.search.cat_idx"></i>
                            </span>
                        </scroller>

                    </div>
                </div>
            </transition>
        </div>
        <div v-if="$store.state.list.length > 0  && $store.state.search.loading == false ">

            <ul class="thumb-box">
                <li v-for="item in $store.state.list" :key="item.id">
                    <div class="thumb" @click="goto('/detail/id/'+item.id)"><img :src="item.good_img||'static/mini/img/grey.gif'" onerror="this.src=`static/mini/img/grey.gif`;this.error=null"></div>
                    <div class="title" @click="goto('/detail/id/'+item.id)">
                        {{item.good_title}}
                    </div>
                    <div class="info">
                        <span class="price" v-if="item.good_type == 4 || item.good_type == 5">积分
                            <em>{{item.credits}}</em>
                        </span>
                        <span class="price" v-else>￥
                            <em>{{item.price}}</em>
                        </span>
                        <i v-if="item.good_type == 1" class="iconfont icon-gouwuche1" @click="add_cart(item.id);"></i>
                    </div>
                </li>
            </ul>
        </div>
        <div style="padding:1em 0" v-loading="$store.state.search.loading"></div>

        <div class="nodata" v-if="$store.state.list.length < 1 && $store.state.search.loading == false ">
            <i class="iconfont icon-tongyongmeiyoushuju"></i>
            <div>没有找到数据</div>
        </div>

    </div>
</template>
<script>
import http from '@/assets/js/http'

export default {
    name: 'search',
    mixins: [http],
    watch: {
        '$route'() {
            this.init();

        },

    },
    data() {
        return {
            showCat: false,
            list: [],
            sort: false,
            cat_list: [
                { cat_name: '全部', id: 0 }
            ]
        }
    },
    methods: {
        //加入购物车
        add_cart(good_id) {
            let url = '/mini/Cart/add', vm = this, data = { good_id: good_id };
            this.apiPost(url, data).then(function(res) {
                if (res.code) {
                    vm.$msg(res.msg);
                } else {
                    vm.handleError(res)
                }
            })
        },
        onSort() {
            this.sort = true;
            this.$store.state.sort = !this.$store.state.sort;

            if (this.$store.state.search.keyword) {
                this.getSearch()
            }

        },
        closeDialog() {
            this.showCat = false

            if (this.$store.state.search.keyword) {
                this.getSearch()
            }
        },
        selectCat(cat_idx) {

            this.$store.state.search.cat_idx = cat_idx;
            let vm = this
            setTimeout(() => {
                vm.closeDialog()
            }, 300)
        },
        clear() {
            window.localStorage.removeItem('__SearchHistory__');
            this.sethList([])
        },
        init() {
            this.getList()
            setTimeout(function() {
                document.body.scrollTop = 0;
            }, 500)
        },
        getList() {
            let _list = window.localStorage.getItem('__SearchHistory__');
            if (_list) {
                _list = JSON.parse(_list);
                this.sethList(_list)
            }
        },
        get_cat() {
            let url = '/mini/Home/get_cat_list?page=1',
                vm = this;

            this.apiGet(url, { limit: 7 }).then(function(res) {
                if (res.code) {
                    vm.cat_list = vm.cat_list.concat(res.data);
                    if (vm.$route.name == 'search2') {
                        let cat_id = vm.$route.params.cat_id;
                        let cat_idx = 0;
                        for (let i = 0; i < res.data.length; i++) {
                            if (cat_id == res.data[i].id) {
                                cat_idx = (i +1)
                                break;
                            }
                        }
                        vm.selectCat(cat_idx);
                        vm.getSearch()
                    }
                } else {
                    vm.handleError(res)
                }
            })
        },
        getSearch(keyword) {
            let url = '/mini/Good/get_list?page=1',
                vm = this, data = {
                    keyword: keyword || this.$store.state.search.keyword
                };
            if (keyword) {
                this.$store.state.search.keyword = keyword
            }
            if (this.$store.state.search.cat_idx) {
                data.cat_id = this.cat_list[this.$store.state.search.cat_idx].id
            }
            if (this.sort) {
                if (this.$store.state.sort == false) {
                    data.price_order = 'desc'
                } else {
                    data.price_order = 'asc'
                }
            }

            vm.$store.state.search.loading = true;
            this.apiGet(url, data).then(function(res) {
                if (res.code) {
                    vm.setSearchList(res.data.list)
                } else {
                    vm.handleError(res)
                }
                setTimeout(() => {
                    vm.$store.state.search.loading = false;
                }, 400)
            })
        }


    },
    created() {
        this.init();
        this.setTitle('搜索')
        this.get_cat();




    }


}

</script>
