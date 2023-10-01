<script setup>
import {onMounted, ref} from 'vue'
import store from '@/store'
import PageHeader from "@/components/PageHeader.vue"
import AddArticleModal from '@/components/modals/AddArticleModal.vue'
const title = "Статьи операций"
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
const modalArticle = ref()
const modalType = ref()
const modalItem = ref()
const data = ref([])
const getData = () => {
    axios.get('/api/v1/profile/article/list')
        .then((res) => {
            data.value = res.data
        })
}
const openCreateModal = (title, type, item) => {
    modalTitle.value = title
    modalType.value = type
    modalItem.value = item
    modalArticle.value.showModal()
}
onMounted(() => {
    getData()
})
</script>
<template>
    <AddArticleModal 
        ref="modalArticle" 
        :title="modalTitle"
        :type="modalType"
        :item="modalItem"
        @action="getData"
    />
    <PageHeader :title="title" :items="items" />

    <div class="row">
        <div class="col-12 col-md-6 col-xl-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Статьи расходов</h4>
                    <button @click="openCreateModal('Создать статью расходов', 'consume')" class="btn btn-sm btn-outline-danger col-12 mb-2">
                        <i class="mdi mdi-plus mr-2"></i> Добавить статью расходов
                    </button>
                    <button v-for="item in data.filter(x => x.article_type === 'consume')" @click="openCreateModal('Изменить статью расходов', 'consume', item)" class="btn btn-sm btn-light col-12 mb-2">{{ item.title }} <i class="mdi mdi-pencil" /></button>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Статьи доходов</h4>
                    <button @click="openCreateModal('Создать статью доходов', 'profit')" class="btn btn-sm btn-outline-success col-12 mb-2">
                        <i class="mdi mdi-plus mr-2"></i> Добавить статью доходов
                    </button>
                    <button v-for="item in data.filter(x => x.article_type === 'profit')" @click="openCreateModal('Изменить статью доходов', 'profit', item)" class="btn btn-sm btn-light col-12 mb-2">{{ item.title }} <i class="mdi mdi-pencil" /></button>
                </div>
            </div>
        </div>
    </div>
</template>