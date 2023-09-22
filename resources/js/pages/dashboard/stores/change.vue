<script setup>
import {onMounted, ref, inject} from 'vue'
import PageHeader from "@/components/PageHeader.vue"
import { useRoute } from 'vue-router'
import router from "@/router"
import store from "@/store"

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
    name: '',
})

const getData = async () => {
    const route = useRoute()
    let id = route.params.id  

    await axios.get(`/api/v1/profile/lk/index/${id}`)
        .then((res) => {
            data.value = res.data.data
        })
}

onMounted(() => {
    getData()
})
</script>
<template>
<div>
    <PageHeader :title="title" :items="items" />
    <div class="row">
        <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group mb-4">
                                <label for="name">Название</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    placeholder="Введите название магазина"
                                    v-model="data.name"
                                />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>
</template>