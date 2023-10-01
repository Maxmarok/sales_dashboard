import { createStore } from 'vuex'
import createPersistedState from "vuex-persistedstate"

const store = createStore({
  state () {
    return {
      user: null,
      token: null,
    }
  },
  actions: {
    logout (context) {
      context.commit('saveUser', null)
      context.commit('saveToken', null)
      console.log(context.user)
    }
  },
  mutations: {
    saveUser (state, data) {
      state.user = data
    },
    saveToken (state, data) {
      state.token = data
    },
    saveStoreList (state, data) {
      state.storeList = data
    },
  },
  getters: {
    storeList (state, getters) {
      return state.storeList
    },
    auth (state, getters) {
      return state.user
    },
  },
  plugins: [createPersistedState()]
})

export default store