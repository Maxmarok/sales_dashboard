import { createStore } from 'vuex'
import createPersistedState from "vuex-persistedstate"

const store = createStore({
  state () {
    return {
      user: null,
      token: null,
      storeList: null,
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
    }
  },
  plugins: [createPersistedState()]
})

export default store