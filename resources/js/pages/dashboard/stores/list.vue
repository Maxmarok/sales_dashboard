<script setup>
import {onMounted, ref} from 'vue'
import store from '@/store'
import PageHeader from "@/components/PageHeader.vue"
const title = "Магазины"
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
const getData = () => {
    axios.get('/api/v1/profile/lk/list')
        .then((res) => {
            data.value = res.data
        })
    //data.value = store.state.storeList
}
onMounted(() => {
    getData()
})
</script>
<template>
    <PageHeader :title="title" :items="items" />
    <div class="row">
         <div class="col-12">
    
           <div class="card">
            <div class="card-body">
                <div>
                    <router-link :to="{name: 'StoreAdd'}" class="btn btn-success mb-2">
                        <i class="mdi mdi-plus mr-2"></i> Добавить магазин
                    </router-link>
                </div>
                <div class="table-responsive mt-3">
                <table
                    class="table table-centered datatable dt-responsive nowrap"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;"
                    v-if="data"
                >
                    <thead class="thead-light">
                    <tr>
                        <th>Название</th>
                        <th>Стандартный ключ API</th>
                        <th>Ключ статистики API</th>
                        <th>Ключ рекламы API</th>
                        <th>Налог с выручки, %</th>
                        <th>Дата добавления</th>
                        <th>Отчеты</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in data" :key="index">
                        <td>{{item.name}}</td>
                        <td>
                            <span v-if="item.api_standard_key" v-html="item.api_standard_key" />
                            <button class="btn btn-outline-primary" v-else><i class="mdi mdi-pencil font-size-18"></i>Добавить</button>
                        </td>
                        <td>
                            <span v-if="item.api_statistic_key" v-html="item.api_statistic_key" />
                        </td>
                        <td>
                            <span v-if="item.api_ad_key" v-html="item.api_ad_key" />
                        </td>
                        <td>{{item.tax}}</td>
                        <td>{{item.date}}</td>
                        <td>
                            <router-link
                                :to="{name: 'Reports', params: {id: item.id}}"
                                class="btn btn-outline-primary"
                                title="Редактировать"
                            >
                            <i class="ri-coins-fill mr-2"></i> Движение средств
                            </router-link>
                  
                        </td>
                        <td>
                            <router-link
                                :to="{name: 'StoreChange', params: {id: item.id}}"
                                class="text-primary"
                                title="Редактировать"
                            >
                                <i class="mdi mdi-pencil font-size-18"></i>
                            </router-link>
                            
                        </td>
                    </tr>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
        </div>
    </div>
</template>