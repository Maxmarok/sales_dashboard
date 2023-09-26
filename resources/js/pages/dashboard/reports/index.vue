<script setup>
import {onMounted, ref} from 'vue'
import Multiselect from "@vueform/multiselect"
import PageHeader from "@/components/PageHeader.vue"
import store from '@/store'
const title = "Отчет о движении денежных средств"
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

const options = ref({
    year: [
        '2023',
        '2022',
    ],
    lks: [
        {name: 'Тестовый'},
        {name: '123'},
    ]
})

const filter = ref({
    year: '2023',
    lk: []
})

const disabled = ref(false);

const data = ref([
    {sales: null},
    {penalty: null},
    {delivery: null},
    {consume: null}
]);
onMounted(() => {
    // setTimeout(() => {
    //     document.getElementById("preloader").style.display = "block";
    //     document.getElementById("status").style.display = "block";
    // })

    getData()
    // .then(() => {
    //     setTimeout(() => {
    //             document.getElementById("preloader").style.display = "none";
    //             document.getElementById("status").style.display = "none";
    //         }, 1000)
    // })
});

const getSelect = (option) => {
    console.log(option);
}

const getData = async () => {

    await axios.post(`/api/v1/movements`, {lk_id: 2, year: filter.value.year})
        .then((res) => {
            console.log(res);
            if(res.data) {
                data.value = res.data.data;
            }

            disabled.value = false;
        })
}

const getValue = (num, sign = null) => {
    let str =  Math.round(num, 0).toLocaleString() + ' ₽'
    //if(num > 0) str = str + ' ₽'
    if(sign && num > 0) str = `${sign} ${str}`
    return str
}
</script>
<template>
<PageHeader :title="title" :items="items" />
<div class="d-flex align-items-center row mb-3">
    <div class="col-3">
        <Multiselect
            v-model="filter.year"
            :options="options.year"
            :placeholder="'Выберите дату'"
        />
    </div>

    <div class="col-3">
        <Multiselect
            v-model="filter.lks"
            :options="options.lks"
            :placeholder="'Выберите магазин'"
            :label="'name'"
            :track-by="'name'"
            :searchable="true" 
            :allow-empty="true"
            @select="(selectedOption, id) => getSelect(selectedOption, id)"
        />
    </div>

    <div class="col-3">
        <button type="button" class="btn btn-success" @click="getData" :disabled="disabled">Искать</button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th></th>
                                <th>Январь</th>
                                <th>Февраль</th>
                                <th>Март</th>
                                <th>Апрель</th>
                                <th>Май</th>
                                <th>Июнь</th>
                                <th>Июль</th>
                                <th>Август</th>
                                <th>Сентябрь</th>
                                <th>Октябрь</th>
                                <th>Ноябрь</th>
                                <th>Декабрь</th>
                                <th>Итого за год</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="data.sales">
                                <th scope="row" class="text-centered">Выручка</th>
                                <td v-for="item in data.sales" :class="{'text-success': item > 0 }" v-html="getValue(item)" />
                            </tr>

                            <tr v-if="data.consume">
                                <th scope="row">Расходы ВБ</th>
                                <td v-for="item in data.consume" :class="{'text-danger': item > 0 }" v-html="getValue(item, '-')" />
                            </tr>

                            <tr v-if="data.penalty">
                                <td scope="row">Штрафы ВБ</td>
                                <td v-for="item in data.penalty" :class="{'text-danger': item > 0 }" v-html="getValue(item, '-')" />
                            </tr>

                            <tr v-if="data.commission">
                                <td scope="row">Комиссия ВБ</td>
                                <td v-for="item in data.commission" :class="{'text-danger': item > 0 }" v-html="getValue(item, '-')" />
                            </tr>


                            <tr v-if="data.delivery">
                                <td scope="row">Логистика ВБ</td>
                                <td v-for="item in data.delivery" :class="{'text-danger': item > 0 }" v-html="getValue(item, '-')" />
                            </tr>

                            <tr v-if="data.all" class="table-light">
                                <th scope="row">К перечислению</th>
                                <td v-for="item in data.all" :class="{'text-success': item > 0, 'text-danger': item < 0 }"  v-html="getValue(item)" />
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</template>