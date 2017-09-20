
/**
  * http.js v0.0.1
  * (c) 2017 Nick
  * @license MIT
  */
  'use strict';
/*  */
import {
  mapActions
} from "vuex";
const apiMethods = {
  data() {
    return {
      loading: false,
      pages: {
        current_page: 1,
        total: 10,
        total_page: 1,
        limit: 20,
      },
      pickerOptions: {
        shortcuts: [
            {
                text: '今天',
                onClick(picker) {
                    const end = new Date();
                    const start = new Date();
                    start.setTime(start.getTime());
                    picker.$emit('pick', [start, end]);
                }
            },
            {
                text: '最近三天',
                onClick(picker) {
                    const end = new Date();
                    const start = new Date();
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 3);
                    picker.$emit('pick', [start, end]);
                }
            },
            {
                text: '最近一周',
                onClick(picker) {
                    const end = new Date();
                    const start = new Date();
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                    picker.$emit('pick', [start, end]);
                }
            }, {
                text: '最近一个月',
                onClick(picker) {
                    const end = new Date();
                    const start = new Date();
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                    picker.$emit('pick', [start, end]);
                }
            }, {
                text: '最近三个月',
                onClick(picker) {
                    const end = new Date();
                    const start = new Date();
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                    picker.$emit('pick', [start, end]);
                }
            }]
    },
    value7: '',
    }
  },
  methods: {
    //统一全局方法引用  见 "./vuex/actions.js"
    ...mapActions({
      setTitle: "setTitle",
      setBreadcrumb: 'setBreadcrumb',
      setCatList: 'setCatList',
      setNavlist: 'setNavlist'
    }),
    //currentPage 改变时会触发
    handleCurrentChange(current_paged) {
      if (this.isSearch) {
        if (typeof this.onSearch == 'function') {
          this.onSearch(current_paged)
        }
      } else {
        if (typeof this.get_list == 'function') {
          this.get_list(current_paged)
        }
      }
    },
    //统一跳转
    goto(url) {
      this.$router.push(url)
    },
    //统一GET数据
    apiGet(url, data) {
      // const load = this.$loading()
      return new Promise((resolve, reject) => {

        this.$http.get(url, {
          params: data
        }).then((response) => {
          resolve(response.body);
          // load.close();
        }, (response) => {
          reject(response);
          this.serverError(response);
          // load.close();
        });
      });

    },
    //统一POST数据
    apiPost(url, data) {
      // const load = this.$loading()
      return new Promise((resolve, reject) => {

        this.$http.post(url, data)
          .then((response) => {
            resolve(response.body);
            // load.close();
          }, (response) => {
            reject(response);
            this.serverError(response);
            // load.close();
          });
      });
    },
    //统一服务器出错处理
    serverError(err) {
      this.$alert('服务器出错，错误码：' + err.status + ',\n' + 'url：' + err.url, '警告', {
        type: 'error'
      });
    },
    //统一异常处理
    handleError(res) {
      this.$message.error(res.msg);

    }
  }
};

export default apiMethods;
