const apiMethods = {
    methods: {
        apiGet(url, data) {
            return new Promise((resolve, reject) => {
                const load = this.$loading()
                this.$http.get(url, data).then((response) => {
                    load.close();
                    resolve(response.body);
                }, (response) => {
                    reject(response);
                    this.serverError(load,response);
                });
            });

        },
        apiPost(url, data) {
            return new Promise((resolve, reject) => {
                const load = this.$loading()
                this.$http.post(url, data)
                    .then((response) => {
                        load.close();
                        resolve(response.body);
                    }, (response) => {
                        reject(response);
                        this.serverError(load,response);
                    });
            });
        },
        serverError(err) {
            load.close();
            conosle.log('请求超时，请检查网络,' + 'serverError:' + err.status);
        },
        handleError(res) {
            load.close();
   
            this.$message.error(res.info);

        }
    }
};

export default apiMethods;
