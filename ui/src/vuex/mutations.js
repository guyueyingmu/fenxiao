const mutations ={
    setBreadcrumb (state,val) {
        state.breadcrumb = val;
        window.Global.setTitle(val[val.length- 1])
      }
}

export default mutations;