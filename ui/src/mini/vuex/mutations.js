const mutations = {

  setTitle(state, str) {
    window.document.title = str;
    state.title = str;
  },
  setKeyword(state, str) {
    state.search.keyword = str
  },
  setSloading(state, str) {
    state.search.loading = str
  },
  ShowNav(state, str) {
    state.ShowNav = str;
  },
  hList(state, str) {
    state.hList = str;
  },
  setCart(state, str) {
    state.cart = str;
  },
  setAddress(state, str) {
    state.checked_address = str;
  },
  setSearchList(state, str) {
    state.list = str
  }
}

export default mutations;
