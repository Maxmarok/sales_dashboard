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
const data = ref([])
const getData = () => {
    axios.get('/api/v1/profile/operation/list')
        .then((res) => {
            data.value = res.data
        })
}
const openCreateModal = (title, type) => {
  modalTitle.value = title
  modalType.value = type
  modalAccount.value.showModal()
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
        @action="getData"
    />
    <PageHeader :title="title" :items="items" />
    <div class="row">
        <div class="col-12">
           <div class="card">
            <div class="card-body">
                <div class="col-12 row mb-2">
                    <button @click="openCreateModal('Добавить операцию прихода', 'profit')" class="btn btn-primary">
                        <i class="mdi mdi-plus mr-2"></i> Приход
                    </button>

                    <button @click="openCreateModal('Добавить операцию расхода', 'consume')" class="btn btn-primary ml-2">
                        <i class="mdi mdi-minus mr-2"></i> Расход
                    </button>
                </div>

                <div class="table-responsive mt-3">
                    <table
                        class="table datatable dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;"
                        v-if="data"
                    >
                        <thead class="thead-light">
                        <tr>
                            <th>Счет</th>
                            <th>Дата</th>
                            <th>Операция</th>
                            <th>Артикул</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in data" :key="index" :class="{'table-success': item.type === 'profit', 'table-danger': item.type === 'consume'}">
                            <td>{{item.account_name}} <br /> <span class="font-size-12">{{ item.description }}</span></td>
                            <td>{{item.date}}</td>
                            <td>
                                <span v-html="`+ ${item.value}`" v-if="item.type === 'profit'" />
                                <span v-html="`- ${item.value}`" v-if="item.type === 'consume'" />
                            </td>
                            
                            <td>{{item.art}}</td>
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