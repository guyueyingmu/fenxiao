import { mapActions } from "vuex";
const apiMethods = {
    data(){
        return{
            loading:false,
            pages: {
                current_page: 1,
                total: 10,
                total_page: 1,
                limit:20,
            },
        }
    },
    methods: {
         //统一全局方法引用  见 "./vuex/actions.js"
        ...mapActions({
            setTitle:"setTitle",
            setBreadcrumb: 'setBreadcrumb',
            setCatList:'setCatList',
            setMenu:'setMenu',
            setNavlist:'setNavlist'
        }),
        //统一跳转
        goto(url) {
            this.$router.push(url)
        },
        //统一GET数据
        apiGet(url, data) {
            // const load = this.$loading()
            return new Promise((resolve, reject) => {
              
                this.$http.get(url, {params:data}).then((response) => {
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
            this.$alert('服务器出错，错误码：' + err.status +',\n' + 'url：'+err.url,'警告',{type:'error'});
        },
        //统一异常处理
        handleError(res) {
            this.$message.error(res.msg);

        }
    }
};

export default apiMethods;
