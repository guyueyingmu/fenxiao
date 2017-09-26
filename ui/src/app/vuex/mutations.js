const mutations = {

  setTitle(state, str) {
    window.document.title = str;
  },
  ShowNav(state, str) {
    state.ShowNav= str;
  },
  hList(state, str) {
    state.hList= str;
  }
}

export default mutations;
