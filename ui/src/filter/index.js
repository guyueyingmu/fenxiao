const filter = function (vue) {
  const payStatus_list = [
    '未支付',
    '已支付',
    '已退费',
    '支付失败',
    '退费失败'

  ]

  const orderStatus_list = [
    '待处理',
    '已服务',
    '已发货',
    '已取消',
    '已完成'
  ]

  const payMethod_list = [
    '微信支付',
    '线下支付',
    '积分支付',
  ]

  const orderFrom_list = [
    '微信'
  ]


  vue.filter('payStatus', function (value) {
    let v = ''
    if (value) {
      value = parseInt(value, 10)
      v = payStatus_list[value + 1]
    }
    return v
  })


  vue.filter('orderStatus', function (value) {

    let v = ''
    if (value) {
      value = parseInt(value, 10)
      v = orderStatus_list[value + 1]
    }
    return v
  })

  vue.filter('payMethod', function (value) {
    let v = ''
    if (value) {
      value = parseInt(value, 10)
      v = payMethod_list[value + 1]
    }
    return v

  })


  vue.filter('orderFrom', function (value) {
    let v = ''
    if (value) {
      value = parseInt(value, 10)
      v = orderFrom_list[value + 1]
    }
    return v
  })

}

export default filter
