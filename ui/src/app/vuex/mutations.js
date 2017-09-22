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
  setTitle(state, str) {
    window.document.title = str;
  },
  setRose(state, str) {
    state.RoseDialogVisible= str;
  }
}

export default mutations;
