<script setup>
import { onMounted, ref, defineExpose, defineProps, inject, defineEmits, watch } from "vue"
import moment from 'moment'
import Modal from "@/components/Modal.vue"
import Multiselect from "@vueform/multiselect"
const emit = defineEmits('getData')
const onGetData = () => emit('getData')

const thisModal = ref(null)

const _showModal = () => thisModal.value.show()

defineExpose({ showModal: _showModal })

const props = defineProps({
  title: String,
  type: String,
})

const data = ref({
  type: null,
  value: null,
  art: null,
  date: moment(moment.now()).format('DD.MM.YYYY'),
  description: null,
  account_id: null,
  article_id: null,
})

const errors = ref({
  value: null,
  art: null,
  date: null,
  description: null,
  account_id: null,
  article_id: null,
})

const accountList = ref(null)
const articleList = ref(null)

const swal = inject('$swal')

const getData = () => {
  axios.get('/api/v1/profile/account/list')
    .then((res) => {
      accountList.value = res.data.map(x => {
        return {value: x.id, label: x.title}
      })
    })
}

const createAccount = () => {
  axios.post('/api/v1/profile/operation/add', data.value)
    .then(res => {
      thisModal.value.hide()
      swal.fire({
        text: 'Операция расчета успешно добавлена!',
        position: 'bottom-end',
        showConfirmButton: false,
        icon: 'success',
        backdrop: false,
        timer: 3000,
      })
      onGetData()
    })
    .catch(err => {
      if(err.response !== undefined) errors.value = err.response.data.errors
    })
}

const changeInput = (name) => {
  if(errors.value[name]) errors.value[name] = null;
}

onMounted(() => {
  console.log(data.value.date)
  getData()
})

watch(() => props.type, function() {
  data.value.type = props.type
});

</script>

<template>
  <Modal :title="props.title" ref="thisModal" size="xl">
    <template #body>
      <form>
        <div class="row">
          <div class="col-12">
            <div class="col-12 row">
              <div class="form-group mb-4 col-lg-6">
                <label for="value">Сумма</label>
                <input
                    type="text"
                    class="form-control"
                    :class="{'is-invalid': errors.value }"
                    id="value"
                    placeholder="Введите сумму"
                    v-model="data.value"
                    @input="changeInput('value')"
                />

                <div v-if="errors.value" class="invalid-feedback">
                  <span v-for="error in errors.value " v-html="error" />
                </div>
              </div>

              <div class="form-group mb-4 col-lg-6">
                <label for="name">Счет</label>
                <Multiselect
                  v-model="data.account_id"
                  :reduce="option => option.id"
                  :class="[{'is-invalid': errors.account_id}]"
                  :options="accountList"
                  :placeholder="'Выберите счет'"
                  :searchable="true"
                />

                <div v-if="errors.account_id" class="invalid-feedback">
                  <span v-for="error in errors.account_id" v-html="error" />
                </div>
              </div>

            </div>

            <div class="col-12 row">
              <div class="form-group mb-4 col-lg-6">
                <label for="art">Артикул</label>
                <input
                    type="text"
                    class="form-control"
                    :class="{'is-invalid': errors.art }"
                    id="art"
                    placeholder="Введите артикул"
                    v-model="data.art"
                />

                <div v-if="errors.art" class="invalid-feedback">
                  <span v-for="error in errors.art " v-html="error" />
                </div>
              </div>

              <div class="form-group mb-4 col-lg-6">
                <label for="date">Дата</label>
                <date-picker 
                  v-model="data.date"
                  :editable="true"
                  locale="ru"
                  format="DD.MM.YYYY"
                  input-format="DD.MM.YYYY"
                  display-format="DD.MM.YYYY"
                  input-class="form-control"
                />

                <div v-if="errors.date" class="invalid-feedback">
                  <span v-for="error in errors.date " v-html="error" />
                </div>
              </div>
            </div>

            <div class="col-12 row">
              <div class="form-group mb-4 col-lg-6">
                <label for="description">Комментарий</label>
                <textarea
                    class="form-control"
                    :class="{'is-invalid': errors.description }"
                    id="description"
                    placeholder="Введите описание"
                    v-model="data.description"
                />
                <div v-if="errors.description" class="invalid-feedback">
                  <span v-for="error in errors.description " v-html="error" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </template>
    <template #footer>
      <button class="btn btn-primary" @click="createAccount">Создать счет</button>
    </template>
  </Modal>
</template>