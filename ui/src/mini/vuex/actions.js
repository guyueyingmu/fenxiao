const actions ={

      setTitle({commit},val){
          commit('setTitle',val)
      },
      ShowNav({commit},val){
          commit('ShowNav',val)
      },
      setHList({commit},val){
          commit('hList',val)
      },
      setCart({commit},val){
        commit('setCart',val)
    }
    
}

export default actions;