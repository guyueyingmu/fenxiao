<template>
    <div>

        <div class="order_info_main" v-if="order_info" v-loading="loading">
            <el-row class="order_info_main_grid" :gutter="50">
                <el-col :span="12">
                    <h4 class="pen_title">订单信息</h4>
                    <div>订单编号：<span>{{order_info.order_number}}</span></div> 
                   <div> 下单时间：<span>{{order_info.add_time}}</span></div> 
                    <div>下单用户：<span>{{order_info.nickname}}</span> </div> 
                    <div>用户手机：<span>{{order_info.phone_number}}</span></div> 
                    <hr>
                    <div>订单总额：<span>{{order_info.total_amount}}</span> </div> 
                    <div>订单状态：<span>{{order_info.order_status_txt}}</span></div>
                    <div> 订单来源：<span>{{order_info.order_from_txt}}</span> </div>
                    <hr>
                    <div>积分扣减：<span>{{order_info.minus_credits}}</span> </div>
                    <div>订单分成处理：<span>{{order_info.distribution_status_txt}}</span></div>
                    <hr>
                    <div>订单取消原因：<span>{{order_info.cancel_reason_txt}}</span></div>
                    <div> 订单取消时间：<span>{{order_info.cancel_time}}</span></div>
                    <hr>
                    <div> 订单完成时间：<span>{{order_info.finish_time}}</span> </div> 
                </el-col>
                <el-col :span="12">
                    <h4 class="pen_title">支付信息</h4>
                   <div>支付状态：<span>{{order_info.pay_status_txt}}</span> </div>
                   <div> 支付时间： <span>{{order_info.pay_time}}</span> </div>
                   <div> 支付交易号： <span>{{order_info.pay_trade_num}}</span> </div>
                   <div> 支付备注： <span>{{order_info.pay_note}}</span> </div>
                   <hr>
                   <div> 退款时间： <span>{{order_info.refund_time}}</span></div>
                   <div> 退款交易号： <span>{{order_info.refund_trade_num}}</span> </div>
                   <div> 退款备注： <span>{{order_info.refund_none}}</span></div>
                   </el-col>
                </el-col>
            </el-row>

            <el-row class="order_info_main_grid" :gutter="50">
                <el-col :span="12">
                    <h4 class="pen_title">配送信息</h4>
                    <div v-if="order_info.order_consignment"> 
                        <div>发货员：<span>{{order_info.order_consignment.consignment_user}}</span></div> 
                        <div>发货时间：<span>{{order_info.order_consignment.consignment_time}}</span> </div> 
                        <div>配送方式：<span>{{order_info.order_consignment.deliver_method_txt}}</span> </div> 
                        <div>快递编号：<span>{{order_info.order_consignment.tracking_number}}</span> </div> 
                        <hr>
                        <div>收货人：<span>{{order_info.consignee_name}}</span></div> 
                        <div>收货人电话：<span>{{order_info.consignee_phone}}</span></div> 
                        <div>收货人地址： <span>{{order_info.consignee_address}}</span></div> 
                    </div>
                  <div v-else>
                      <div style="color:#888;line-height:4;margin-bottom:50px">没有配送信息</div>
                  </div>

                </el-col>
                <el-col :span="12">
                    <h4 class="pen_title">服务信息</h4>
                    <div v-if="order_info.order_service">
                        <div>服务员：<span>{{order_info.order_service.service_user}}</span></div> 
                        <div>服务时间：<span>{{order_info.order_service.service_time}}</span> </div> 
                        <div>备注：<span>{{order_info.order_service.note}}</span></div> 
                    </div>
                       <div v-else>
                      <div style="color:#888;line-height:4;margin-bottom:50px">没有服务信息</div>
                  </div>
              
                </el-col>
                </el-col>
            </el-row>

            <h4 class="pen_title">订单商品</h4>
            <el-table :data="order_info.orders_goods" border style="width: 100%">
                <el-table-column prop="good_id" label="商品ID" width="120"></el-table-column>
                <el-table-column prop="good_img" label="商品图标" width="100">
                    <template slot-scope="scope">
                        <img :src="scope.row.good_img" width="40" heigth="40" alt="">
                    </template>
                </el-table-column>
                <el-table-column prop="good_title" label="商品名称"></el-table-column>
                <el-table-column prop="cat_name" label="商品分类" width="120"> </el-table-column>
                <el-table-column prop="credits" label="扣减积分" width="120"> </el-table-column>
                <el-table-column prop="price" label="购买单价" width="120"> </el-table-column>
                <el-table-column prop="buy_num" label="购买数量"></el-table-column>
            </el-table>
            <div style="height:180px;"></div>
        </div>

        <div class="table_nodata" v-else>
            没有数据
        </div>
    </div>
</template>

<script>
import http from "@/assets/js/http";

export default {
  mixins: [http],
  data() {
    return {
      order_info: {}
    };
  },
  watch: {
    $route: "initData"
  },
  methods: {
    initData() {
      this.order_info = {};
      this.get_order_info(this.$route.params.order_id);
    },

    //取数据
    get_order_info(order_id) {
      let url = "/admin/order/get_order_info?order_id=" + order_id,
        vm = this;
      vm.loading = true;
      this.apiGet(url).then(function(res) {
        if (res.code) {
          vm.order_info = res.data;
        } else {
          vm.handleError(res);
        }
        vm.loading = false;
      });
    }
  },
  //组件初始化
  created() {
    this.get_order_info(this.$route.params.order_id);
    this.setBreadcrumb(["订单", "订单详情"]);
  }
};
</script>
