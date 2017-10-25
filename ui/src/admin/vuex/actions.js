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
      },
      setRose({commit},val){
          commit('setRose',val)
      },
      setTalkBox({commit},val){
          commit('setTalkBox',val)
      }
    
}

export default actions;