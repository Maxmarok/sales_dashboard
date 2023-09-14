import { createStore } from 'vuex'
import cookies from 'vue-cookies'

// Create a new store instance.
const store = createStore({
  state () {
    return {
      user: cookies.get('user'),
      token: cookies.get('token'),
    }
  },
  mutations: {
    saveUser (state, data) {
      
      state.user = data.user;
      state.token = data.token;
      cookies.set('user', state.user);
      cookies.set('token', state.token);
    }
  }
})

export default store