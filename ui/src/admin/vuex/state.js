const state ={
    count : 1,
    DEV : window.location.port == '8080',
    breadcrumb:[],
    cat_list:[],
    nav_list:[],
    RoseDialogVisible:false,
    LoginDialogVisible:false,
    c_loading:true,
    GOODTYPE:[
        {label:'实物商品',id:"1",tip:'(微信支付 需要快递)'},
        {label:'虚拟商品',id:"2",tip:'(微信支付 无需快递)'},
        {label:'预约类商品',id:"3",tip:'(线下支付 无需快递)'},
        {label:'积分实物商品',id:"4",tip:'(积分兑换 需要快递)'},
        {label:'积分虚拟商品',id:"5",tip:'(积分兑换 无需快递)'},

    ]
 
}
export default state;