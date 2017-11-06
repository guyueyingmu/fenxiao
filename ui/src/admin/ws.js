// const ip = window.location.hostname;
const ip = "mall.minbbo.com";
const ws = new WebSocket("ws://" + ip + ":8282");


ws.onmessage = function (e) {
  // json数据转换成js对象
  var data = eval("(" + e.data + ")");
  var type = data.type || "";
  switch (type) {
    case "init":
       console.log('run bind_ws')
    //   let url = "/admin/Kefu/bind";
    //   vm.current_client_id = data.client_id;
    //   vm.apiPost(url, {
    //       client_id: data.client_id
    //     })
    //     .then(res => {
    //       console.log(res.msg)
    //     });
      break;
    case "msg":
      that.msg()
      break;
    default:
  }
}




export default ws;
