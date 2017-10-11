const state ={
    DEV : window.location.port == '8080',
    ShowNav:true,
    search:{
        keyword:'',
        loading:false,
        sort:true
    },
    list:[],
    hList:[],
    title:'首页',
    cart:[],
    checked_address:{},
    
}
export default state;