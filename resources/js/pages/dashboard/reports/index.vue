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
        '2022',
        '2023',
    ],
    lk: store.state.storeList
})

const filter = ref({
    year: '2022',
    lk: 1,
})

const disabled = ref(false);

const data = ref([]);
onMounted(() => {
    setTimeout(() => {
        document.getElementById("preloader").style.display = "block";
        document.getElementById("status").style.display = "block";
    })

    getData().then(() => {
        setTimeout(() => {
                document.getElementById("preloader").style.display = "none";
                document.getElementById("status").style.display = "none";
            }, 1000)
    })
});

const getData = async () => {

    await axios.post(`/api/v1/movements`, {lk_id: 1, year: filter.value.year})
        .then((res) => {
            console.log(res);
            if(res.data) {
                data.value = res.data.data;
            }

            disabled.value = false;
        })
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
            v-model="filter.lk"
            :options="options.lk"
            :placeholder="'Выберите магазин'"
            label="name"
            track-by="id"
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
                    <table class="table table-bordered mb-0">
                        <thead>
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
                                <th scope="row">Выручка</th>
                                <td>{{ data.sales.jan }}</td>
                                <td>{{ data.sales.feb }}</td>
                                <td>{{ data.sales.mar }}</td>
                                <td>{{ data.sales.apr }}</td>
                                <td>{{ data.sales.may }}</td>
                                <td>{{ data.sales.jun }}</td>
                                <td>{{ data.sales.jul }}</td>
                                <td>{{ data.sales.aug }}</td>
                                <td>{{ data.sales.sep }}</td>
                                <td>{{ data.sales.oct }}</td>
                                <td>{{ data.sales.nov }}</td>
                                <td>{{ data.sales.dec }}</td>
                                <td>{{ data.sales.all }}</td>
                            </tr>

                            <tr>
                                <th scope="row">Расходы ВБ</th>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                            </tr>

                            <tr>
                                <th scope="row">Комиссия ВБ</th>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                            </tr>

                            <tr>
                                <th scope="row">Логистика ВБ</th>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                            </tr>

                            <tr>
                                <th scope="row">Реклама ВБ</th>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                            </tr>

                            <tr>
                                <th scope="row">Хранение ВБ</th>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                                <td>0 ₽</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</template>