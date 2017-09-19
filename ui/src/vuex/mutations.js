const mutations = {
  setBreadcrumb(state, val, menu) {
    state.breadcrumb = val;
    state.menu = menu;
    window.document.title = val[val.length - 1]
  },
  setCatList(state, val) {
    state.cat_list = val;
  },
  setNavlist(state, val) {
    state.nav_list = val;
  },
  setMenu(state, val) {
      console.log(val)
    if (val) {
      let _array = val.split('-')
      state.myMenu.b = [_array[0]]
    }
    state.myMenu.a = val.toString();


  },
  setTitle(state, str) {
    window.document.title = str;

  }
}

export default mutations;
