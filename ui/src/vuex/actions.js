const actions ={
    setBreadcrumb ({commit},val) {
        commit('setBreadcrumb',val)
      },
      setCatList ({commit},val) {
        commit('setCatList',val)
      },
      setNavlist ({commit},val) {
        commit('setNavlist',val)
      },

      setTitle({commit},val){
          commit('setTitle',val)
      }
    
}

export default actions;