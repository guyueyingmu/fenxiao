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
      addTalkBox({commit},val){
          commit('addTalkBox',val)
      },
      removeTalkBox({commit},val){
          commit('removeTalkBox',val)
      }
    
}

export default actions;