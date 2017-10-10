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
let area = require('../../../static/mini/area.json');

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
            temp: {},
            start: true
        }
    },
    created() {
        this.pro = this.data;
        this.city = this.pro[0]['child'];
        this.county = this.city[0]['child'];

    },
    watch: {
        value(n, o) {
            this.init()

        }
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
                province: this.pro[this.f.p].name,
                city: this.city[this.f.c].name,
                area: this.county[this.f.cc].name,
                str: this.pro[this.f.p].name + ' ' + this.city[this.f.c].name + ' ' + this.county[this.f.cc].name
            };
            if(!this.start){
                this.$emit("input", re);
            }
        },
        init: function() {
            let n = this.value;
            var vm = this;
            var _d = vm.pro;
            for (var i = 0; i < _d.length; i++) {

                if (_d[i].name == n.province) {
                    vm.f.p = i;
                    city_cb(i);
                    break;
                }
            }

            function city_cb(ii) {
                vm.city = vm.pro[ii]['child'];
                var _d = vm.city;
                for (var i = 0; i < _d.length; i++) {

                    if (_d[i].name == n.city) {
                        vm.f.c = i;
                      setTimeout(()=>{
                          cc_cb(i)
                      },0)
                        break;
                    }
                }
            }

            function cc_cb(ii) {
                vm.county = vm.city[ii]['child'];
                var _d = vm.county;

                for (var i = 0; i < _d.length; i++) {

                    if (_d[i].name == n.area) {
                        vm.f.cc = i;
                        vm.start =false;
                        break;
                    }
                }
            }
        }
    },
}

</script>
