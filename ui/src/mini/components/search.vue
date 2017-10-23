<template>
    <div class="search-page">
        <div class="search show">
            <div class="search-box">
                <input type="text"  id="keyword" v-model="search.keyword" @keyup.enter="onSearch"  placeholder="请输入关键词搜索" class="needsclick ui-input" v-focus="isIndex">
                <i class="iconfont icon-chuyidong" @click="search.keyword = ''" v-if="search.keyword.length"></i>
                <span class="ui-btn ui-btn-search" @click="onSearch">
                    <i class="iconfont icon-sousuo"></i>搜索</span>
            </div>
        </div>

        <div class="search-history" v-if="hList.length > 0">
            <div class="header" >
                <span class="title">最近搜索：</span>
                <a herf="javascript:;" @click="clear()">清空历史</a>
            </div>
            <div class="h-list" >
                <span class="label" v-for="item in hList" :key="item" @click="getSearch(1,item)">{{item}}</span>
            </div>
        </div>

        <div class="header-sort-mask" @click="closeDialog" v-if="showCat ==true"></div>
        <div class="header-sort">
            <div class="item" @click="showCat = !showCat" :class="{'active':showCat || search.cat_idx }">
                <i class="iconfont icon-fenlei"></i>
                <span v-if="cat_list.length > 1">{{search.cat_idx?cat_list[search.cat_idx].cat_name:'分类'}}</span>
            </div>
            <div class="item sort" :class="{'active':search.sort,'skin':usedSort}" @click="onSort">
                <i class="iconfont icon-paixu"></i> 价格</div>
            <transition name="cat">
                <div class="class-dialog" v-show="showCat">
                    <div class="scroll" v-if="cat_list.length > 0">
                        <scroller>
                            <span v-for="(i,cat_idx) in cat_list" :key="i.id" :class="{'active':cat_idx == search.cat_idx}" @click="selectCat(cat_idx)">{{i.cat_name}}
                                <i class="iconfont icon-dagou" v-if="cat_idx == search.cat_idx"></i>
                            </span>
                        </scroller>

                    </div>
                </div>
            </transition>
        </div>
        <div>

            <ul class="thumb-box"  v-infinite-scroll="loadMore" :infinite-scroll-disabled="sloading" :infinite-scroll-immediate-check="false" :infinite-scroll-distance="10" infinite-scroll-listen-for-event="cheackLoadMore">
                <li v-for="item in search.list" :key="item.id">
                    <div class="thumb" @click="goto('/detail/id/'+item.id)"><img v-lazy="item.good_img"></div>
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
          <div class="spinner" v-if="sloading">
            <mt-spinner  :size="18" color="#26a2ff"></mt-spinner>
        </div>
        <div class="nodata-line" v-else-if="pages.total_page == pages.current_page && search.list.lenght > 0">没有更多数据了</div>

        <div class="nodata" v-if="search.list.length < 1 && search.loading == false ">
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

    data() {
        return {
            isIndex:true,
            showCat: false,
            usedSort: false,
            sloading:false,
            search: {
                keyword: '',
                loading: false,
                sort: false,
                cat_idx: 0,
                list: [],

            },
            hList: [],
            cat_list: [
                { cat_name: '全部', id: 0 }
            ]
        }
    },
    methods: {
          loadMore() {
              return
            console.log('load')
            if (this.sloading) {  }

            let page = parseInt(this.pages.current_page, 10) || 1;
            if (page < this.pages.total_page ) {
                this.getSearch(page + 1);
            }

        },
        onSearch() {
        
            var obj = document.getElementById('keyword')
            obj.blur();
            let _list = this.hList;
            if (this.search.keyword && this.hList.indexOf(this.search.keyword) == -1) {
                if (_list.length >= 10) {
                    _list.shift()
                }
                _list.push(this.search.keyword)

                if (_list) {
                    _list = JSON.stringify(_list);
                    window.localStorage.setItem('__SearchHistory__', _list);
                }
            }
            this.getSearch()

     


        },
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
            this.usedSort = true;
            this.search.sort = !this.search.sort;
            this.getSearch()

        

        },
        closeDialog() {
            this.showCat = false

            // if (this.search.keyword) {
            this.getSearch()
            //  }
        },
        selectCat(cat_idx) {

            this.search.cat_idx = cat_idx;
            let vm = this
            setTimeout(() => {
                vm.closeDialog()
            }, 300)
        },
        clear() {
            window.localStorage.removeItem('__SearchHistory__');
            this.hList = []

        },
        init() {
            this.getList()
        },
        getList() {
            let _list = window.localStorage.getItem('__SearchHistory__');
            if (_list) {
                _list = JSON.parse(_list);
                this.hList = _list;
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
                        vm.search.keyword = ''
                        let cat_idx = 0;
                        for (let i = 0; i < res.data.length; i++) {
                            if (cat_id == res.data[i].id) {
                                cat_idx = (i + 1)
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
        getSearch(page,keyword) {
          
            page = page || 1;
            let url = '/mini/Good/get_list?page='+page,
                vm = this, data = {
                    keyword: keyword || this.search.keyword
                };
            if (keyword) {
                this.search.keyword = keyword
            }
            if (this.search.cat_idx) {
                data.cat_id = this.cat_list[this.search.cat_idx].id
            }
            if (this.usedSort) {
                if (this.search.sort == false) {
                    data.price_order = 'desc'
                } else {
                    data.price_order = 'asc'
                }
            }
            if(this.$route.name == 'search3'){
                data.list_type = this.$route.params.list_type;
            }


            this.apiGet(url, data).then(function(res) {
                if (res.code) {
                    vm.pages = res.data.pages;
                    
                    if (parseInt(res.data.pages.current_page,10) < 2) {
                        vm.search.list = res.data.list;
                    } else {
                        let _list = vm.search.list;
                        vm.search.list = _list.concat(res.data.list)
                    }
                    setTimeout(() => {
                        vm.sloading = false;
                        vm.$emit('cheackLoadMore')
                    }, 200)



                } else {
                    vm.handleError(res)
                }

            })
        }


    },
    created() {
        if(this.$route.name == 'search2'){
            this.isIndex = false
        }
        if(this.$route.name == 'search3'){
            this.isIndex = false
            this.getSearch();
        }
        this.init();
        this.setTitle('搜索')
        this.get_cat();

    },



}

</script>
