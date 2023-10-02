<script setup>
import { FormWizard, TabContent } from "vue3-form-wizard"
import {onMounted, ref, inject} from 'vue'
import PageHeader from "@/components/PageHeader.vue"
import { useRoute } from 'vue-router'
import router from "@/router"
import store from "@/store"
const title = "Добавление магазина"
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
const form = ref({
    title: null,
    standard: null,
    statistic: null,
    ad: null,
})
const errors = ref({
    title: null,
    standard: null,
    statistic: null,
    ad: null,
})
const id = ref()
const wizard = ref()
const swal = inject('$swal')
onMounted(() => {
    const route = useRoute()
    id.value = route.params.id  

    if(id.value) {
        //console.log(id)

        setTimeout(() => {
            document.getElementById("preloader").style.display = "block"
            document.getElementById("status").style.display = "block"
        })

        axios.get(`/api/v1/profile/lk/index/${id.value}`)
            .then((res) => {
                console.log(res)
                if(res.data) {
                    wizard.value.nextTab()
                }
                setTimeout(() => {
                    document.getElementById("preloader").style.display = "none"
                    document.getElementById("status").style.display = "none"
                }, 1000)
            })
        
    }
})
const submitStandard = () => formSubmit('standard')
const submitStatistic = () => formSubmit('statistic')
const submitAd = () => formSubmit('ad')
const formSubmit = async (type) => {
    const arr = {
        marketplace: 'WB',
        type: type, 
        key: form.value[type],
        lk_id: id.value,
    }
    return await axios.post(`/api/v1/profile/add-api-key`, arr)
        .then((res) => {
            //console.log(res);
            swal.fire({
                text: 'Ключ успешно добавлен',
                position: 'bottom-end',
                showConfirmButton: false,
                icon: 'success',
                backdrop: false,
                timer: 2000,
            })
            if(res.data) {
                // if(type === 'ad') {
                //     //store.commit('saveStoreList', res.data.data)
                //     router.push({name: 'ReportsItem', params: {id: id}})
                // } else {
                //     return true;
                // }

                return true;
            } else {
                return false;
            }
        })
        .catch((err) => {
            if(err.response.data.message !== undefined) {
                swal.fire({
                    text: err.response.data.message,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    icon: 'error',
                    backdrop: false,
                    timer: 2000,
                })
            }
        })
}

const createStore = async () => {
    return await axios.post(`/api/v1/profile/lk/add/`, {name: form.value.title})
        .then((res) => {
            //console.log(res);
    
            if(res.data) {
                id.value = res.data.data.id
                swal.fire({
                    text: 'Магазин создан, осталось добавить ключи',
                    position: 'bottom-end',
                    showConfirmButton: false,
                    icon: 'success',
                    backdrop: false,
                    timer: 2000,
                })
                return true;
            } else {
                return false;
            }
        })
        .catch((err) => {
            if(err.response.data.message !== undefined) {
                swal.fire({
                    text: err.response.data.message,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    icon: 'error',
                    backdrop: false,
                    timer: 2000,
                })
            }
        })
}

const addTax = async () => {
    return await axios.post(`/api/v1/profile/lk/update`, {name: form.value.title, id: id.value, tax: form.value.tax})
        .then((res) => {
            //console.log(res);
    
            if(res.data) {
                swal.fire({
                    text: 'Новый магазин успешно добавлен!',
                    position: 'bottom-end',
                    showConfirmButton: false,
                    icon: 'success',
                    backdrop: false,
                    timer: 3000,
                })
                finalCreateStore()
            } else {
                return false;
            }
        })
        .catch((err) => {
            if(err.response.data.message !== undefined) {
                swal.fire({
                    text: err.response.data.message,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    icon: 'error',
                    backdrop: false,
                    timer: 2000,
                })
            }
        })
}

const finalCreateStore = async () => {
    router.push({name: 'StoreList'})
}
</script>
<template>
 <PageHeader :title="title" :items="items" />
 <div class="row">
      <div class="col-12 col-lg-6">
 
        <div class="card">
            <div class="card-body">
                <!-- <h4 class="card-title">Sizing</h4>
                <p class="card-title-desc">123</p> -->
                <form-wizard 
                    color="#556ee6"
                    @on-complete="formSubmit"
                    next-button-text="Далее"
                    back-button-text="Назад"
                    finish-button-text="Добавить магазин"
                    ref="wizard"
                >
                    <tab-content 
                        icon="mdi mdi-account-circle"
                        :before-change="createStore"
                        title="Название магазина"
                        >
                        <div class="mb-4">
                            <label>Название магазина</label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="form-control"
                                id="email"
                                placeholder="Введите название магазина"
                                :class="{ 'is-invalid': errors.title }"
                            />
                        </div>
                    </tab-content>
                    <tab-content 
                        icon="mdi mdi-key"
                        :before-change="submitStandard"
                        title="API-Ключ (стандартный)"
                    >
                        <div class="mb-4">
                            <label>API-Ключ (стандартный)</label>
                            <textarea
                                v-model="form.standard"
                                class="form-control"
                                name="textarea"
                                :class="{ 'is-invalid': errors.standard }"
                                rows="3"
                                placeholder="Введите API-ключ"
                            ></textarea>
                        </div>
                    </tab-content>
                    <tab-content
                        icon="mdi mdi-key"
                        :before-change="submitStatistic"
                        title="API-Ключ (статистика)"
                    >
                        <div class="mb-4">
                            <label>API-Ключ (статистика)</label>
                            <textarea
                                v-model="form.statistic"
                                class="form-control"
                                name="textarea"
                                :class="{ 'is-invalid': errors.statistic }"
                                rows="3"
                                placeholder="Введите API-ключ"
                            ></textarea>
                        </div>
                    </tab-content>

                    <tab-content 
                        icon="mdi mdi-key"
                        :before-change="submitAd"
                        title="API-Ключ (реклама)"
                    >
                        <div class="mb-4">
                            <label>API-Ключ (реклама)</label>
                            <textarea
                                v-model="form.ad"
                                class="form-control"
                                name="textarea"
                                :class="{ 'is-invalid': errors.ad }"
                                rows="3"
                                placeholder="Введите API-ключ"
                            ></textarea>
                        </div>
                    </tab-content>

                    <tab-content 
                        icon="mdi mdi-percent-outline"
                        :before-change="addTax"
                        title="Налог с выручки"
                    >
                        <div class="mb-4">
                            <label for="tax">Налог с выручки, в %</label>
                            <input
                                type="text"
                                v-model="form.tax"
                                class="form-control"
                                name="tax"
                                :class="{ 'is-invalid': errors.ad }"
                                placeholder="Введите налог с выручки в %"
                            />
                        </div>
                    </tab-content>
                </form-wizard>
            </div> 
        </div>
    </div>
</div>


</template>