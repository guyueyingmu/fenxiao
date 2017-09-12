const Global = {
    DEV : window.location.port == '8080',
    setTitle :function(str){
        window.document.title = str;
    } 

}
window.Global = Global;
export default Global;