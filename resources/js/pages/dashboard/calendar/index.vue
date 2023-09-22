<script setup>
import PageHeader from '@/components/PageHeader.vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import ruLocale from "@fullcalendar/core/locales/ru"
import { onMounted, ref } from 'vue';

const title = "Платежный календарь"
const items = [
    {
        text: "Forms",
        href: "/"
    },
    {
        text: "Forms Elements",
        active: true
    }
]

const data = ref();
const events = ref([])

const successColor = '#1cbb8c';
const dangerColor = '#ff3d60';

let calendarOptions = {
    plugins: [dayGridPlugin],
    initialView: 'dayGridMonth',
    locale: ruLocale,
    weekends: true,
    eventDisplay: 'block',
    eventBackgroundColor: 'rgb(251,251,251)',
    displayEventTime: false,
    events: events.value,
    eventOrder: 'order',
    eventDurationEditable: false,
    eventStartEditable: false,
    // titleFormat: {
    //     weekday: 'long',
    // }
}



const getData = async () => {
    await axios.get('/api/v1/calendar')
        .then((res) => {
            data.value = res.data
            return true
        })
}

const getValue = (num, sign = null) => {
    let str =  Math.round(num, 0).toLocaleString() + ' ₽'
    //if(num > 0) str = str + ' ₽'
    if(sign && num > 0) str = `${sign} ${str}`
    return str
}

onMounted(() => {
    getData().then(() => {
        let sales = []
        let reports = []
        let all = []

        data.value.sales.forEach((x) => {
            let date = new Date(x.day)
            let obj = {
                title: getValue(x.forPay, '+'), 
                value: x.forPay, 
                date: x.day, 
                start: date, 
                textColor: '#0d935b', 
                className: 'text-success',
                order: 1,
            }
            sales.push(obj)
        })

        events.value.push(...sales)

        data.value.reports.forEach((x) => {
            let date = new Date(x.day)
            let obj = {
                title: getValue(x.penalty_sum + x.delivery_rub_sum, '-'), 
                value: x.penalty_sum + x.delivery_rub_sum, 
                date: x.day, 
                start: date, 
                textColor: '#b91e1e', 
                className: 'text-danger',
                order: 2,
            }
            reports.push(obj)
        })

        events.value.push(...reports)

        sales.forEach((item) => {
            let i = all.findIndex((x => x.date == item.date))
 
            if(i == -1) {
                all.push({
                    title: getValue(item.value, '+'), 
                    value: item.value,
                    date: item.date, 
                    start: new Date(item.date), 
                    backgroundColor: successColor, 
                    textColor: '#fff', 
                    order: 3,
                })
            }
        })

        reports.forEach((item) => {
            let i = all.findIndex((x => x.date === item.date))

            if(i == -1) {
                all.push({
                    title: getValue(item.value, '-'), 
                    value: item.value, 
                    date: item.date, 
                    start: new Date(item.date), 
                    textColor: '#ffffff',
                    backgroundColor: dangerColor, 
                })
            } else {
                
                let sum = parseInt(all[i].value) - parseInt(item.value)
                let plus = sum > 0;
                all[i].textColor = '#fff'
                all[i].backgroundColor = plus ? successColor : dangerColor
                all[i].value = sum
                all[i].title = getValue(sum, (plus ? '+' : '-'))
            }
        })

        //console.log(all, sales, reports);

        //data.value.
       
        
        events.value.push(...all)
    })
})  

</script>
<template>
<div>
    <PageHeader :title="title" :items="items" />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="app-calendar">
              <FullCalendar
                ref="fullCalendar"
                :eventOrder="['eventOrder']"
                :options="calendarOptions"
              ></FullCalendar>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
</template>