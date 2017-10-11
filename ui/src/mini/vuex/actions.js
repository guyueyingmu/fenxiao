const actions = {

    setTitle({ commit }, val) {
        commit('setTitle', val)
    },
    setKeyword({ commit }, val) {
        commit('setKeyword', val)
    },
    ShowNav({ commit }, val) {
        commit('ShowNav', val)
    },
    setHList({ commit }, val) {
        commit('hList', val)
    },
    setCart({ commit }, val) {
        commit('setCart', val)
    },
    setAddress({ commit }, val) {
        commit('setAddress', val)
    },
    setSearchList({ commit }, val) {
        commit('setSearchList', val)
    },
    

}

export default actions;