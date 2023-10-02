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
                        class="table datatable dt-responsive text-nowrap mb-0 text-center table-centered"
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
                                <div v-for="account in item.accounts" v-if="item.accounts.length > 0">
                                    {{ account.title }} <span class="text-secondary font-size-12">– {{ getValue(account.balance, account.currency_sign) }}</span>
                                </div>

                                <span v-else class="text-secondary font-size-12">Нет подключенных счетов</span>
                            </td>
                            <td>{{item.tax}} %</td>
                            <td>
                                <div>
                                    <span v-if="item.api_standard_key">
                                        Стандартный: <span class="text-primary cursor-pointer" @click="editApiKeyModal('Изменить стандартный ключ', 'standard', item.api_standard)">{{ item.api_standard_key }} <i class="mdi mdi-pencil" /></span>
                                    </span>
                                    <span class="text-primary cursor-pointer" v-else @click="editApiKeyModal('Добавить стандартный ключ', 'standard', {lk_id: item.id })">Добавить стандартный ключ <i class="mdi mdi-plus"></i></span>
                                </div>

                                <div>
                                    <span v-if="item.api_statistic_key">
                                        Статистика: <span class="text-primary cursor-pointer" @click="editApiKeyModal('Изменить ключ статистики', 'statistic', item.api_statistic)">{{ item.api_statistic_key }} <i class="mdi mdi-pencil" /></span>
                                    </span>
                                    <span class="text-primary cursor-pointer" v-else @click="editApiKeyModal('Добавить ключ статистики', 'statistic', {lk_id: item.id })">Добавить ключ статистики <i class="mdi mdi-plus"></i></span>
                                </div>

                                <div>
                                    <span v-if="item.api_ad_key">
                                        Реклама: <span class="text-primary cursor-pointer" @click="editApiKeyModal('Изменить ключ рекламы', 'ad', item.api_ad)">{{ item.api_ad_key }} <i class="mdi mdi-pencil" /></span>
                                    </span>
                                    <span class="text-primary cursor-pointer" v-else @click="editApiKeyModal('Добавить ключ рекламы', 'ad', {lk_id: item.id })">Добавить ключ рекламы <i class="mdi mdi-plus"></i></span>
                                </div>
                            </td>
                            
                            <td>
                                <div class="mb-2">
                                    <router-link
                                        :to="{name: 'Cashflow', params: {id: item.id}}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Редактировать"
                                    >
                                        Движение средств
                                    </router-link>
                                </div>

                                <div>
                                    <router-link
                                        :to="{name: 'Pnl', params: {id: item.id}}"
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