
import Vue from 'vue'
import Vuex from 'vuex'
import mutations from './mutations'
import actions from './actions'
import state from './state'
Vue.use(Vuex)

const getters = {
    // GOODTYPE: state => state.GOODTYPE,
    // nav_list: state => state.nav_list
  }

const store = new Vuex.Store({
  state,
  actions,
  mutations,
  getters
});
export default store;