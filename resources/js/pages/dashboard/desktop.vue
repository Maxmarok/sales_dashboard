<script setup>
import {onMounted, ref} from 'vue'
import PageHeader from '@/components/PageHeader.vue'
const title = "Рабочий стол"
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
const data = ref([])
const profit = ref([])
const consume = ref([])
const days = ref([])

const series = ref([
    {
        name: "Выручка",
        data: profit,
    },

    {
        name: "Расходы",
        data: consume,
    },
])

const chartOptions = ref({
    chart: {
        zoom: {
            enabled: false
        },
        toolbar: {
            show: false,
        }
    },
    colors: ['#1cbb8c', '#ff3d60'],
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: [3, 3],
        curve: 'smooth',
    },
    xaxis: {
        categories: days,
    },
})

const getData = async () => {
    await axios.get('/api/v1/dashboard')
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
        data.value.sales.forEach((item) => {
            let i = data.value.reports.findIndex((x => x.day === item.day))

            profit.value.push({
                x: item.date_short,
                y: Math.round(item.forPay, 2)
            })
            
            if(i !== -1) {
                let report = data.value.reports[i]
               
                consume.value.push({
                    x: item.date_short,
                    y: Math.round(report.penalty_sum + report.delivery_rub_sum, 2)
                })

            } else {
                consume.value.push({
                    x: item.day, 
                    y: 0
                })
            }
        })

        // data.value.reports.forEach((item) => {
        //     // if(!days.value.includes(x.day)) days.value.push(x.day)
        //     // consume.value.push(Math.round(x.penalty_sum + x.delivery_rub_sum, 2))

        //     consume.value.push({
        //         x: item.day, 
        //         y: Math.round(item.penalty_sum + item.delivery_rub_sum, 2)
        //     })
        // })

        //console.log(days.value, values.value)
    })
})
</script>
<template>
<div>
    <PageHeader :title="title" :items="items" />
    <div class="card">
        <div class="card-body">
        <h4 class="card-title mb-4">Выручка по дням за текущий месяц</h4>
        <div>
            <div id="line-column-chart" class="apex-charts" dir="ltr"></div>
                <apexchart
                class="apex-charts"
                height="280"
                dir="ltr"
                :series="series"
                :options="chartOptions"
                ></apexchart>
            </div>
        </div>
    </div>
</div>
</template>