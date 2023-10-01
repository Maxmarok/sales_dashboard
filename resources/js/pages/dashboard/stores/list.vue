<script setup>
import {onMounted, ref} from 'vue'
import store from '@/store'
import PageHeader from "@/components/PageHeader.vue"
import ApiKeyModal from '@/components/modals/ApiKeyModal.vue'

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

const modalApiKey = ref()

const modalTitle = ref()
const modalType = ref()
const modalItem = ref()

const editApiKeyModal = (title, type, item) => {
    modalTitle.value = title
    modalType.value = type
    modalItem.value = item
    modalApiKey.value.showModal()
}

const getData = () => {
    axios.get('/api/v1/profile/lk/list')
        .then((res) => {
            data.value = res.data
        })
    //data.value = store.state.storeList
}
const getValue = (num, sign = null) => {
    let str =  Math.round(num, 0).toLocaleString()
    return `${str} ${sign}`
}
onMounted(() => {
    getData()
})
</script>
<template>
    <PageHeader :title="title" :items="items">
        <template #right>
            <router-link :to="{name: 'StoreAdd'}" class="btn btn-sm btn-success">
              <i class="mdi mdi-plus mr-1" /> Добавить магазин
            </router-link>
        </template>
    </PageHeader>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="table-responsive">
                    <table
                        class="table datatable dt-responsive text-nowrap mb-0"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;"
                        v-if="data"
                    >
                        <thead class="thead-light">
                        <tr>
                            <th>Название</th>
                            
                            <th>Подключенные счета</th>
                            <th>Налог</th>
                            <th>Ключи API</th>
                            <th>Отчеты</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in data" :key="index">
                            <td>{{item.name}} <br /><span class="text-secondary font-size-12">{{item.date}}</span></td>
                            

                            <td>
                                <div v-for="account in item.accounts">
                                    {{ account.title }} <br /><span class="text-secondary font-size-12">{{ getValue(account.balance, account.currency_sign) }}</span>
                                </div>
                            </td>
                            <td>{{item.tax}} %</td>
                            <td>
                                <div>
                                    Стандартный: <span v-if="item.api_standard_key" class="text-primary cursor-pointer" @click="editApiKeyModal('Изменить стандартный ключ', 'standard', item.api_standard)">{{ item.api_standard_key }} <i class="mdi mdi-pencil" /></span>
                                    <button class="btn btn-sm btn-outline-primary" v-else><i class="mdi mdi-plus" />Добавить</button>
                                </div>

                                <div>
                                    Статистика: <span v-if="item.api_statistic_key" class="text-primary cursor-pointer" @click="editApiKeyModal('Изменить ключ статистики', 'statistic', item.api_statistic)">{{ item.api_statistic_key }} <i class="mdi mdi-pencil" /></span>
                                    <button class="btn btn-sm btn-outline-primary" v-else><i class="mdi mdi-pencil"></i>Добавить</button>
                                </div>

                                <div>
                                    Реклама: <span v-if="item.api_ad_key" class="text-primary cursor-pointer" @click="editApiKeyModal('Изменить ключ рекламы', 'ad', item.api_ad)">{{ item.api_ad_key }} <i class="mdi mdi-pencil" /></span>
                                    <button class="btn btn-sm btn-outline-primary" v-else><i class="mdi mdi-pencil"></i>Добавить</button>
                                </div>
                            </td>
                            
                            <td>
                                <div class="mb-2">
                                    <router-link
                                        :to="{name: 'Reports', params: {id: item.id}}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Редактировать"
                                    >
                                        Движение средств
                                    </router-link>
                                </div>

                                <div>
                                    <router-link
                                        :to="{name: 'Reports', params: {id: item.id}}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Редактировать"
                                    >
                                    Прибыли и убытки
                                    </router-link>
                                </div>
                    
                            </td>
                            <td>   
                                <div class="d-flex">
                                    <router-link 
                                        :to="{name: 'StoreChange', params: {id: item.id}}"
                                        class="btn btn-sm btn-primary mx-auto text-nowrap"
                                        title="Изменить"
                                    >
                                    <i class="mdi mdi-pencil mr-1" /> Изменить  
                                    </router-link>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <ApiKeyModal 
        ref="modalApiKey"
        :title="modalTitle"
        :type="modalType"
        :item="modalItem"
        @action="getData"
    />
</template>