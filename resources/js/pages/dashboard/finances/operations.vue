<script setup>
import {onMounted, ref} from 'vue'
import store from '@/store'
import PageHeader from "@/components/PageHeader.vue"
import AddOperationModal from '@/components/modals/AddOperationModal.vue'
const title = "Операции расчета"
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
const modalType = ref()
const modalItem = ref()
const data = ref([])
const getData = () => {
    axios.get('/api/v1/profile/operation/list')
        .then((res) => {
            data.value = res.data
        })
}
const openCreateModal = (title, type, item) => {
  modalTitle.value = title
  modalType.value = type
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
    <AddOperationModal 
        ref="modalAccount" 
        :title="modalTitle"
        :type="modalType"
        :item="modalItem"
        @action="getData"
    />
    <PageHeader :title="title" :items="items">
        <template #right>
            <div>
                <button @click="openCreateModal('Добавить приход', 'profit')" class="btn btn-sm btn-success">
                    <i class="mdi mdi-plus mr-2"></i> Добавить приход
                </button>

                <button @click="openCreateModal('Добавить расход', 'consume')" class="btn btn-sm btn-danger ml-2">
                    <i class="mdi mdi-minus mr-2"></i> Добавить расход
                </button>
            </div>
        </template>
    </PageHeader>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="table-responsive">
                    <table
                        class="table table-centered datatable dt-responsive nowrap mb-0"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;"
                        v-if="data"
                    >
                        <thead class="thead-light">
                        <tr>
                            <th>Операция</th>
                            
                            <th>Дата</th>
                            <th>Счет</th>
                            <th>Статья</th>
                            <th>Артикул</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in data" :key="index">
                            <td>
                                <span class="text-success" v-html="`+ ${getValue(item.value, item.currency_sign)}`" v-if="item.type === 'profit'" />
                                <span class="text-danger" v-html="`- ${getValue(item.value, item.currency_sign)}`" v-if="item.type === 'consume'" />
                                
                            </td>
                            <td>{{item.date}}</td>
                            <td>{{item.account_name}}</td>
                            <td>{{item.article_name}}<br /> <span class="font-size-12 text-secondary">{{ item.description }}</span></td>
                            
                            <td>
                                <span v-if="item.art" v-html="item.art" />
                                <span v-else class="text-secondary font-size-12">Без артикула</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary d-flex mx-auto" @click="openCreateModal('Изменить операцию', item.type, item)">
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