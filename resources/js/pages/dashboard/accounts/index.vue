<script setup>
import {onMounted, ref} from 'vue'
import store from '@/store'
import PageHeader from "@/components/PageHeader.vue"
import AccountModal from '@/components/modals/AccountModal.vue'
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
const modalAccount = ref()
const data = ref([])
const getData = () => {
    axios.get('/api/v1/profile/account/list')
        .then((res) => {
            data.value = res.data
        })
}
const openCreateModal = () => modalAccount.value.showModal()
onMounted(() => {
    getData()
})
</script>
<template>
    <AccountModal 
        ref="modalAccount" 
        :title="'Создать банковский счет'"
    />
    <PageHeader :title="title" :items="items" />
    <div class="row">
        <div class="col-12">
           <div class="card">
            <div class="card-body">
                <div>
                    <button @click="openCreateModal" class="btn btn-success mb-2">
                        <i class="mdi mdi-plus mr-2"></i> Добавить банковский счёт
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
                        <th>Название</th>
                        <th>Баланс</th>
                        <th>Банк</th>
                        <th>БИК</th>
                        <th>К/С</th>
                        <th>Номер</th>
                        <th>Валюта</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in data" :key="index">
                        <td>{{item.title}} <br /> <span class="font-size-12">{{ item.store_name }}</span></td>
                        <td>{{item.balance}}</td>
                        <td>{{item.bank}}</td>
                        <td>{{item.bic}}</td>
                        <td>{{item.ks}}</td>
                        <td>{{item.number}}</td>
                        <td>{{item.currency}}</td>
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