<template>
    <div class="uiSelect">
        <div class="ui-select">
            <select v-model="f.p" @change="selpro">
                <option :value="i" v-for="(v,i) in pro" :key="v.id">{{v.name}}</option>
            </select>
        </div>
        <div class="ui-select">
            <select v-model="f.c" @change="selcity">
                <option :value="i" v-for="(v,i) in city" :key="v.id">{{v.name}}</option>
            </select>
        </div>
        <div class="ui-select" v-if="county.length>0">
            <select v-model="f.cc" @change="result">
                <option :value="i" v-for="(v,i) in county" :key="v.id">{{v.name}}</option>
            </select>
        </div>
    </div>
</template>
<script>
let area = require('../../../static/app/area.json');

export default {
    name: 'uiArea',
    props: ['value'],
    data: function() {
        return {
            data: area,
            pro: "",
            city: "",
            county: "",
            f: {
                p: 0,
                c: 0,
                cc: 0,
            },
            temp: {}
        }
    },
    mounted: function() {

        this.pro = this.data;
        if (!this.value) {
            this.city = this.pro[0]['child'];
            this.county = this.city[0]['child'];
        }
        this.init();

    },
    methods: {
        selpro: function() {
            this.city = this.pro[this.f.p]['child'];
            this.county = this.city[0]['child'];
            this.f.c = 0;
            this.f.cc = 0;
            this.result();
        },
        selcity: function() {
            this.county = this.city[this.f.c]['child'];
            this.f.cc = 0;
            this.result();
        },
        result: function() {
            var re = {
                province: { id: this.pro[this.f.p].id, name: this.pro[this.f.p].name },
                city: { id: this.city[this.f.c].id, name: this.city[this.f.c].name },
                area: { id: this.county[this.f.cc].id, name: this.county[this.f.cc].name },
                str:this.pro[this.f.p].name +' ' + this.city[this.f.c].name  +' ' + this.county[this.f.cc].name
            };
            this.$emit("input", re);
        },
        init: function() {
            let n = this.value;
            var vm = this;
            var _d = vm.pro;
            for (var i = 0; i < _d.length; i++) {

                if (_d[i].name == n.province) {
                    vm.f.p = i;
                    city_cb(i);
                    continue;
                }
            }

            function city_cb(ii) {
                vm.city = vm.pro[ii]['child'];
                var _d = vm.city;
                for (var i = 0; i < _d.length; i++) {

                    if (_d[i].name == n.city) {
                        vm.f.c = i;
                        cc_cb(i)
                        continue;
                    }
                }
            }

            function cc_cb(ii) {
                vm.county = vm.city[ii]['child'];
                var _d = vm.county;
                for (var i = 0; i < _d.length; i++) {
                    if (_d[i].name == n.area) {

                        vm.f.cc = i;
                        continue;
                    }
                }

            }
        }


    },



}

</script>
