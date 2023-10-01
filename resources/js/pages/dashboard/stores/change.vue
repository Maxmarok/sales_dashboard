<script setup>
import {onMounted, ref, inject} from 'vue'
import PageHeader from "@/components/PageHeader.vue"
import { useRoute } from 'vue-router'
import router from "@/router"
import store from "@/store"

import ApiKeyModal from '@/components/modals/ApiKeyModal.vue'

const title = "Изменение магазина"
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

const data = ref({
    name: null,
    tax: null,
})

const id = ref()

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

const swal = inject('$swal')

const deleteApiKey = (id) => {
  swal.fire({
    text: "Вы действительно хотите удалить API-ключ",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#34c38f",
    cancelButtonColor: "#f46a6a",
    confirmButtonText: "Удалить API-ключ",
    cancelButtonText: 'Отменить',
  }).then(result => {
    if (result.value) {
        axios.post('/api/v1/profile/delete-api-key', {key_id: id})
            .then(res => {
                getData()
                swal.fire({
                    text: "Ваш ключ был успешно удален!", 
                    icon: "success",
                    confirmButtonText: 'Закрыть',
                });
            })
      //swal.fire("Deleted!", "Your file has been deleted.", "success");
    }
  });
}

const getData = () => {
   
    axios.get(`/api/v1/profile/lk/index/${id.value}`)
        .then((res) => {
            data.value = res.data.data
        })
}

const updateLk = () => {
    let obj = {
        name: data.value.name,
        tax: data.value.tax,
        id: data.value.id
    }
    axios.post(`/api/v1/profile/lk/update/`, obj)
        .then((res) => {
            if(res.data) {
                
                swal.fire({
                    text: 'Магазин успешно изменен',
                    position: 'bottom-end',
                    showConfirmButton: false,
                    icon: 'success',
                    backdrop: false,
                    timer: 3000,
                })
                
                router.push({name: 'StoreList'})
            }
        })
}

onMounted(() => {
    const route = useRoute()
    id.value = route.params.id

    getData()
})
</script>
<template>
<div>
    <PageHeader :title="title" :items="items">
        <template #right>
            <router-link
                :to="{name: 'StoreList'}"
                class="btn btn-sm btn-primary"
                title="Вернуться к списку магазинов"
            >
                <i class="mdi mdi-keyboard-backspace mr-2" /> Вернуться к списку магазинов
            </router-link>
        </template>
    </PageHeader>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group mb-4">
                                <label for="name">Название магазина</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    placeholder="Введите название магазина"
                                    v-model="data.name"
                                />
                            </div>

                        </div>

                        <div class="col-lg-4">

                            <div class="form-group mb-4">
                                <label for="tax">Налог с выручки, %</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="tax"
                                    placeholder="Введите налог с выручки в %"
                                    v-model="data.tax"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group mb-4">
                                <label for="api_standard_key">Api-ключ (стандартный)</label>
                                <div class="d-flex align-items-center" v-if="data.api_standard">
                                    <input
                                        type="text"
                                        class="form-control col-lg-9"
                                        id="api_standard_key"
                                        placeholder="Введите валидный ключ"
                                        v-model="data.api_standard_key"
                                        disabled
                                    />

                                    <div class="col-lg-1 ml-2 px-0">
                                        <button class="btn btn-sm btn-primary" @click="editApiKeyModal('Изменить стандартный ключ', 'standard', data.api_standard)">
                                            <i class="mdi mdi-lead-pencil" />
                                        </button>
                                    </div>

                                    <div class="col-lg-1 ml-2 px-0">
                                        <button class="btn btn-sm btn-danger" @click="deleteApiKey(data.api_standard.id)">
                                            <i class="mdi mdi-trash-can-outline" />
                                        </button>
                                    </div>
                                </div>

                                <div v-else>
                                    <button class="btn btn-sm btn-outline-primary col-12" @click="editApiKeyModal('Добавить стандартный ключ', 'standard', {lk_id: data.id })">
                                        Добавить ключ
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group mb-4">
                                <label for="api_statistic_key">Api-ключ (статистика)</label>
                                <div class="d-flex align-items-center" v-if="data.api_statistic">
                                    <input
                                        type="text"
                                        class="form-control col-lg-9"
                                        id="api_statistic_key"
                                        placeholder="Введите валидный ключ"
                                        v-model="data.api_statistic_key"
                                        disabled
                                    />

                                    <div class="col-lg-1 ml-2 px-0">
                                        <button class="btn btn-sm btn-primary" @click="editApiKeyModal('Изменить ключ статистики', 'statistic', data.api_statistic)">
                                            <i class="mdi mdi-lead-pencil" />
                                        </button>
                                    </div>

                                    <div class="col-lg-1 ml-2 px-0">
                                        <button class="btn btn-sm btn-danger" @click="deleteApiKey(data.api_statistic.id)">
                                            <i class="mdi mdi-trash-can-outline" />
                                        </button>
                                    </div>
                                </div>

                                <div v-else>
                                    <button class="btn btn-sm btn-outline-primary col-12" @click="editApiKeyModal('Добавить ключ статистики', 'statistic', {lk_id: data.id })">
                                        Добавить ключ
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group mb-4">
                                <label for="api_ad_key">Api-ключ (реклама)</label>
                                <div class="d-flex align-items-center" v-if="data.api_ad">
                                    <input
                                        type="text"
                                        class="form-control col-lg-9"
                                        id="api_ad_key"
                                        placeholder="Введите валидный ключ"
                                        v-model="data.api_ad_key"
                                        disabled
                                    />

                                    <div class="col-lg-1 ml-2 px-0">
                                        <button class="btn btn-sm btn-primary" @click="editApiKeyModal('Изменить ключ рекламы', 'ad', data.api_ad)">
                                            <i class="mdi mdi-lead-pencil" />
                                        </button>
                                    </div>

                                    <div class="col-lg-1 ml-2 px-0">
                                        <button class="btn btn-sm btn-danger" @click="deleteApiKey(data.api_ad.id)">
                                            <i class="mdi mdi-trash-can-outline" />
                                        </button>
                                    </div>
                                </div>

                                <div v-else>
                                    <button class="btn btn-sm btn-outline-primary col-12" @click="editApiKeyModal('Добавить ключ рекламы', 'ad', {lk_id: data.id })">
                                        Добавить ключ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                

                    <button class="btn btn-sm btn-success" @click="updateLk">
                        Сохранить изменения
                    </button>
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
</div>
</template>