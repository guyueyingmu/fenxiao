const mutations = {
  setBreadcrumb(state, val, menu) {
    state.breadcrumb = val;
    state.menu = menu;
    window.document.title = val[val.length - 1]
  },
  setCatList(state, val) {
    state.cat_list = val;
  },
  setMenu(state, val) {
    state.myMenu = val;
  },
  setTitle(state, str) {
    window.document.title = str;

  }
}

export default mutations;
