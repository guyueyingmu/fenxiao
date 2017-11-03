const mutations = {
  setBreadcrumb(state, arg, menu) {
    state.breadcrumb = arg;
    state.menu = menu;
    window.document.title = arg[arg.length - 1]
  },
  setCatList(state, arg) {
    state.cat_list = arg;
  },
  setNavlist(state, arg) {
    state.nav_list = arg;
  },
  setTitle(state, arg) {
    window.document.title = arg;
  },
  setRose(state, arg) {
    state.RoseDialogVisible = arg;
  },
  addTalkBox(state, arg) {
    let _ayyay = JSON.parse(JSON.stringify(state.talkBoxArray)),
      isHas = false;
      arg.actived = true;
    for (let p = 0; p < state.talkBoxArray.length; p++) {
      if (_ayyay[p].user_id == arg.user_id) {
        _ayyay.splice(p, 1, arg)
        isHas = true;
        break;
      }
    }

    if(!isHas){
        _ayyay.push(arg)
    }
   

    state.talkBoxArray = _ayyay;
  },
  removeTalkBox(state, user_id){
    
    let _ayyay = JSON.parse(JSON.stringify(state.talkBoxArray))
    for (let p = 0; p < state.talkBoxArray.length; p++) {
        if (_ayyay[p].user_id == user_id) {
            state.talkBoxArray.splice(p, 1)

          break;
        }
      }
    //   state.talkBoxArray = _ayyay;
  }
}

export default mutations;
