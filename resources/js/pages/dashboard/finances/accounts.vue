<script setup>
import {onMounted, ref} from 'vue'
import store from '@/store'
import PageHeader from "@/components/PageHeader.vue"
import AddAccountModal from '@/components/modals/AddAccountModal.vue'
const title = "Банковские счета"
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
const modalTitle = ref()
const modalAccount = ref()
const modalItem = ref()
const data = ref([])
const getData = () => {
    axios.get('/api/v1/profile/account/list')
        .then((res) => {
            data.value = res.data
        })
}

const openCreateModal = (title, item) => {
    modalTitle.value = title
    modalItem.value = item
    modalAccount.value.showModal()
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
    <AddAccountModal 
        ref="modalAccount" 
        :title="modalTitle"
        :item="modalItem"
        @action="getData"
    />
    <PageHeader :title="title" :items="items">
        <template #right>
            <div>
                <button @click="openCreateModal('Создать новый счет')" class="btn btn-sm btn-success mb-2">
                    <i class="mdi mdi-plus mr-2"></i> Создать банковский счёт
                </button>
            </div>
        </template>
    </PageHeader>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="table-responsive">
                    <table
                        class="table table-centered datatable dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;"
                        v-if="data"
                    >
                        <thead class="thead-light">
                            <tr>
                                <th>Название</th>
                                <th>Баланс</th>
                                <th>Банк</th>
                                <th>Реквизиты</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in data" :key="index">
                                <td>{{item.title}} <br /> <span class="font-size-12 text-secondary">{{ item.store_name }}</span></td>
                                <td>{{getValue(item.balance, item.currency_sign)}}</td>
                                <td>
                                    <span v-if="item.bank" v-html="item.bank" />
                                    <span v-else class="text-secondary font-size-12">Не указан</span>
                                </td>
                                <td v-if="item.bic || item.ks || item.number">
                                    <div>
                                        <span class="font-size-12" v-if="item.bic">
                                            Бик: {{item.bic}}
                                        </span>

                                        <span v-else class="text-secondary font-size-12">Бик не указан</span>
                                    </div>

                                    <div>
                                        <span class="font-size-12" v-if="item.ks">
                                            К/С: {{item.ks}}
                                        </span>

                                        <span v-else class="text-secondary font-size-12">К/С не указан</span>
                                    </div>

                                    <div>
                                        <span class="font-size-12" v-if="item.number">
                                            Номер: {{item.number}}
                                        </span>

                                        <span v-else class="text-secondary font-size-12">Номер не указан</span>
                                    </div>
                                    
                                </td>
                                <td v-else>
                                    <span class="text-secondary font-size-12">Не указаны</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary d-flex mx-auto" @click="openCreateModal('Изменить счет', item)">
                                        Изменить  <i class="mdi mdi-pencil font-size-14 ml-2"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>