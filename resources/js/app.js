import './bootstrap'
import { createApp } from "vue"
import router from './router'
import store from './store'
import App from './App.vue'
import '~bootstrap/dist/css/bootstrap.css'
import "./assets/scss/app.scss"
import VueTheMask from 'vue-the-mask'
import VueCookies from 'vue-cookies'
import VueSweetalert2 from 'vue-sweetalert2'
import VueApexCharts from "vue3-apexcharts"
import VuePersianDatetimePicker from 'vue3-persian-datetime-picker'

import moment from 'moment'
import ru from 'moment/locale/ru'

const app = createApp(App)
app.use(VueTheMask)
app.use(store)
app.use(VueCookies)
app.use(VueSweetalert2)
app.use(VueApexCharts)

axios.interceptors.request.use(function (config) {
    const token = store.state.token;
    config.headers.Authorization = 'Bearer ' + token;
    return config;
});

app.use(router)

moment.updateLocale('ru', ru)

app.use(VuePersianDatetimePicker, {
    name: 'date-picker',
    props: {
      color: '#9FC054',
      autoSubmit: true,
      locale: 'ro',
    }
}, 'DatePicker')

app.mount("#app")