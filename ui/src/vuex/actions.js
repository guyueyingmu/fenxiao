const actions ={
    setBreadcrumb ({commit},val) {
        commit('setBreadcrumb',val)
      },
      setCatList ({commit},val) {
        commit('setCatList',val)
      },
      setMenu ({commit},val) {
        commit('setMenu',val)
      },
      setTitle({commit},val){
          commit('setTitle',val)
      }
    
}

export default actions;