import './bootstrap'
import { createApp } from "vue"
import router from './router'
import store from './store'
import App from './App.vue'
import "./assets/scss/app.scss"
import '~bootstrap/dist/css/bootstrap.css'
import VueTheMask from 'vue-the-mask'
import VueCookies from 'vue-cookies'
import VueSweetalert2 from 'vue-sweetalert2'

const app = createApp(App)
app.use(VueTheMask)
app.use(router)
app.use(store)
app.use(VueCookies)
app.use(VueSweetalert2)
app.mount("#app")