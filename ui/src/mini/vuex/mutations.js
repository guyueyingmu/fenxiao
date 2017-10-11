const mutations = {

  setTitle(state, str) {
    window.document.title = str;
    state.title= str;
  },
  ShowNav(state, str) {
    state.ShowNav= str;
  },
  hList(state, str) {
    state.hList= str;
  },
  setCart(state, str) {
    state.cart= str;
  },
  setAddress(state, str) {
    state.checked_address= str;
  }
}

export default mutations;
