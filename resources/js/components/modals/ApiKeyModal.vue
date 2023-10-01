<script setup>
import { ref, defineExpose, defineProps, defineEmits, watch, inject } from "vue"
import Modal from "@/components/Modal.vue"
const emits = defineEmits(['action'])
const doAction = () => emits('action')

let thisModal = ref(null)

const _showModal = () => thisModal.value.show()

defineExpose({ showModal: _showModal })

const props = defineProps({
  title: {
    type: String,
    default: "Модальное окно",
  },
  type: {
    type: String,
    default: null,
  },
  center: {
    type: Boolean,
    default: false
  },
  large: {
    type: Boolean,
    default: false
  },
  item: {
    type: Object,
    default: null,
  }
})

const data = ref({
    key: null,
    id: null,
    lk_id: null,
})

const errors = ref({
    key: null,
    id: null,
    lk_id: null,
})

const swal = inject('$swal')

const addApiKey = () => {
  const arr = {
      marketplace: 'WB',
      type: props.type, 
      key: data.value.key,
      lk_id: data.value.lk_id,
  }
  axios.post(`/api/v1/profile/add-api-key`, arr)
      .then((res) => {
        thisModal.value.hide()
        swal.fire({
            text: 'Ключ успешно добавлен',
            position: 'bottom-end',
            showConfirmButton: false,
            icon: 'success',
            backdrop: false,
            timer: 2000,
        })
        doAction()
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

const editApiKey = () => {
  const arr = {
      marketplace: 'WB',
      type: props.type, 
      key: data.value.key,
      lk_id: data.value.lk_id,
      id: data.value.id
  }
  axios.post(`/api/v1/profile/change-api-key`, arr)
      .then((res) => {
        thisModal.value.hide()
        swal.fire({
            text: 'Ключ успешно изменен',
            position: 'bottom-end',
            showConfirmButton: false,
            icon: 'success',
            backdrop: false,
            timer: 2000,
        })
        doAction()
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

const setDefault = () => {
  data.value.key = null
  data.value.lk_id = null
}

watch(() => props.item, function() {
  if(props.item !== null) {
    data.value.key = props.item.key
    data.value.lk_id = props.item.lk_id
    data.value.id = props.item.id

    console.log(data.value)
  } else {
    setDefault()
  }
});

</script>

<template>
  <!-- <button @click="showModal">Show Modal</button> -->
  <Modal :title="props.title" ref="thisModal" :center="props.center" :large="props.large">
    <template #body>
      <div class="row">
        <div class="col-12">
          <div class="form-group mb-4 col-12">
            <label for="key">API-ключ</label>
            <textarea
                type="text"
                class="form-control"
                :class="{'is-invalid': errors.key }"
                id="key"
                placeholder="Введите валидный ключ"
                v-model="data.key"
                rows="4"
            />

            <div v-if="errors.key" class="invalid-feedback">
              <span v-for="error in errors.key " v-html="error" />
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <button class="btn btn-sm btn-primary" @click="editApiKey" v-if="props.item && props.item.key">Сохранить изменения</button>
      <button class="btn btn-sm btn-primary" @click="addApiKey" v-else>{{ title }}</button>
    </template>
</Modal>
</template>