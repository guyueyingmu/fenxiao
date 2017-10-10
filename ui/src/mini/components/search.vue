<template>
    <div class="search-page">
        <div class="search-history">
            <div class="header">
                <span class="title">最近搜索：</span>
                <a herf="javascript:;" @click="clear" v-if="$store.state.hList.length > 0">清空历史</a>
            </div>
            <div class="h-list">
                <span class="label" v-for="item in $store.state.hList" :key="item" @click="search(item)">{{item}}</span>
            </div>
        </div>
        <div class="header-sort-mask" @click="closeDialog" v-if="showCat ==true"></div>
        <div class="header-sort">
            <div class="item" @click="showCat = !showCat" :class="{'active':showCat || cat_id }">
                <i class="iconfont icon-fenlei"></i> {{cat_id?cat_list[cat_id].cat_name:'分类'}}</div>
            <div class="item sort" :class="{'active':sort}" @click="onSort">
                <i class="iconfont icon-paixu"></i> 排序</div>
            <transition name="cat">
                <div class="class-dialog" v-show="showCat">
                    <div class="scroll">
                        <scroller>
                            <span v-for="i in cat_list" :key="i.id" :class="{'active':cat_id == i.id}" @click="selectCat(i.id)">{{i.cat_name}}
                                <i class="iconfont icon-dagou" v-if="cat_id == i.id"></i>
                            </span>
                        </scroller>

                    </div>
                </div>
            </transition>
        </div>

     
        <ul class="thumb-box">
            <li>
                <div class="thumb"  @click="goto('/detail')"><img src="static/mini/img/demo/2.png"></div>
                <div class="title"  @click="goto('/detail')">
                    诺基亚6 (Nokia6) 4GB+64GB 黑色 全网通 双卡双待 移动联通诺基亚6 (Nokia6) 4GB+64GB 黑色 全网通 双卡双待 移动联通
                </div>
                <div class="info">
                    <span class="price">￥<em>390.00</em></span>
                    <i class="iconfont icon-gouwuche1"></i>
                </div>
            </li>
            <li>
                <div class="thumb" @click="goto('/detail')"><img src="static/mini/img/demo/1.png"></div>
                <div class="title" @click="goto('/detail')">
                    德国 进口牛奶 欧德堡（Oldenburger）超高温处理全脂纯牛奶超高温处理全脂纯牛奶
                </div>
                <div class="info">
                    <span class="jifen">积分 390</span>
                </div>
            </li>
            <li>
                <div class="thumb" @click="goto('/detail')"><img src="static/mini/img/demo/3.png"></div>
                <div class="title" @click="goto('/detail')">
                    维达(Vinda) 抽纸 超韧3层130抽软抽*24包(小规格) 整箱销售
                </div>
                <div class="info">
                    <span class="price">￥<em>390.00</em></span>
                    <i class="iconfont icon-gouwuche1"></i>
                </div>
            </li>
            <li>
                <div class="thumb" @click="goto('/detail')"><img src="static/mini/img/demo/4.png"></div>
                <div class="title" @click="goto('/detail')">
                    联想(Lenovo)小新潮7000 13.3英寸超轻薄窄边框笔记本电脑(i
                </div>
                <div class="info">
                    <span class="price">￥<em>5999.00</em></span>
                    <i class="iconfont icon-gouwuche1"></i>
                </div>
            </li>

        </ul>

          <div class="nodata">
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

        }
    },
    data() {
        return {
            showCat: false,
            list: [],
            cat_id: '',
            sort: false,
            cat_list: [
                { cat_name: '全部', id: 0 },
                { cat_name: '美白', id: 1 },
                { cat_name: '保养', id: 2 },
                { cat_name: '补水', id: 3 },
                { cat_name: '去角质', id: 4 },
                { cat_name: '精华素', id: 5 },
                { cat_name: '分类6', id: 6 },
                { cat_name: '分类7', id: 7 },
                { cat_name: '分类7', id: 8 },
                { cat_name: '分类7', id: 9 },
            ]
        }
    },
    methods: {
        onSort() {
            this.sort = !this.sort
        },
        closeDialog() {
            this.showCat = false
        },
        selectCat(id) {
            this.cat_id = id;
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


    },
    created() {
        this.init();
        this.setTitle('搜索')

    }


}

</script>
