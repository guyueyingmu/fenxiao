const apiMethods = {
    methods: {
        apiGet(url, data) {
            const load = this.$loading()
            return new Promise((resolve, reject) => {
              
                this.$http.get(url, data).then((response) => {
                    resolve(response.body);
                    load.close();
                }, (response) => {
                    reject(response);
                    this.serverError(load,response);
                    load.close();
                });
            });

        },
        apiPost(url, data) {
            const load = this.$loading()
            return new Promise((resolve, reject) => {
              
                this.$http.post(url, data)
                    .then((response) => {
                        resolve(response.body);
                        load.close();
                    }, (response) => {
                        reject(response);
                        this.serverError(load,response);
                        load.close();
                    });
            });
        },
        serverError(err) {
            this.$alert('请求超时，请检查网络,' + 'serverError:' + err.status);
        },
        handleError(res) {
            this.$message.error(res.msg);

        }
    }
};

export default apiMethods;
