import './bootstrap'
import { createApp } from "vue"
import router from './router'
import App from './App.vue'
import "./assets/scss/app.scss"
import '~bootstrap/dist/css/bootstrap.css'
// import VueTheMask from 'vue-the-mask'
// import VuePersianDatetimePicker from 'vue3-persian-datetime-picker'

const app = createApp(App);
// app.use(VueTheMask)
app.use(router);

// app.use(VuePersianDatetimePicker, {
//     name: 'date-picker',
//     props: {
//       color: '#9FC054',
//       autoSubmit: true,
//     }
// }, 'DatePicker')

app.mount("#app");