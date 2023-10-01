<script setup>
import { onMounted, ref, defineExpose, defineProps, inject } from "vue"
import Modal from "@/components/Modal.vue"
import Multiselect from "@vueform/multiselect"
const thisModal = ref(null)

const _showModal = () => thisModal.value.show()

defineExpose({ showModal: _showModal })
defineProps({
  title: {
    type: String,
    default: "Модальное окно",
  },
  center: {
    type: Boolean,
    default: false
  },
  large: {
    type: Boolean,
    default: true
  },
});

const data = ref({
  title: null,
  lk_id: null,
  bank: null,
  bic: null,
  ks: null,
  number: null,
  currency: null,
  balance: null,
})

const errors = ref({
  title: null,
  lk_id: null,
  bank: null,
  bic: null,
  ks: null,
  number: null,
  currency: null,
  balance: null,
})

const lkList = ref(null)
const currencyList = ref([
  {value: 'RUB', label: 'RUB ₽ – Российский рубль'},
  {value: 'KZT', label: 'KZT ₸ – Казахстанский тенге'},
  {value: 'BYR', label: 'BYR Br – Белорусский рубль'}
])

const swal = inject('$swal')

const getData = () => {
  axios.get('/api/v1/profile/lk/list')
    .then((res) => {
      lkList.value = res.data.map(x => {
        return {value: x.id, label: x.name}
      })
      
      if(lkList.value.length > 0) data.value.lk_id = lkList.value[0]
    })
}

const createAccount = () => {
  axios.post('/api/v1/profile/account/add', data.value)
    .then(res => {
      thisModal.value.hide()
      swal.fire({
        text: 'Банковский счет успешно добавлен!',
        position: 'bottom-end',
        showConfirmButton: false,
        icon: 'success',
        backdrop: false,
        timer: 3000,
      })
      getData()
    })
    .catch(err => {
      if(err.response !== undefined) errors.value = err.response.data.errors
    })
}

const changeInput = (name) => {
  if(errors.value[name]) errors.value[name] = null;
}

onMounted(() => {
  getData()
})

</script>

<template>
  
  <Modal :title="title" ref="thisModal" size="xl">
    <template #body>
      <div class="row">
        <div class="col-12">
          <div class="col-12 row">
            <div class="form-group mb-4 col-lg-6">
              <label for="title">Название</label>
              <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid': errors.title }"
                  id="title"
                  placeholder="Введите название счета"
                  v-model="data.title"
                  @input="changeInput('title')"
              />

              <div v-if="errors.title" class="invalid-feedback">
                <span v-for="error in errors.title " v-html="error" />
              </div>
            </div>

            <div class="form-group mb-4 col-lg-6">
              <label for="name">Магазин</label>
              <Multiselect
                v-model="data.lk_id"
                :reduce="option => option.id"
                :class="[{is_invalid: errors.lk_id}]"
                :options="lkList"
                :placeholder="'Выберите магазин'"
                :searchable="true"
              />

              <div v-if="errors.lk_id" class="invalid-feedback">
                <span v-for="error in errors.lk_id" v-html="error" />
              </div>
            </div>

          </div>

          <div class="col-12 row">

            <div class="form-group mb-4 col-lg-6">
              <label for="name">Банк</label>
              <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid': errors.bank }"
                  id="name"
                  placeholder="Введите название банка"
                  v-model="data.bank"
              />

              <div v-if="errors.bank" class="invalid-feedback">
                <span v-for="error in errors.bank " v-html="error" />
              </div>
            </div>

            <div class="form-group mb-4 col-lg-6">
              <label for="name">БИК</label>
              <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid': errors.bic }"
                  id="name"
                  placeholder="Введите БИК банка"
                  v-model="data.bic"
              />
              <div v-if="errors.bic" class="invalid-feedback">
                <span v-for="error in errors.bic " v-html="error" />
              </div>
            </div>
          </div>


          <div class="col-12 row">
            <div class="form-group mb-4 col-lg-6">
              <label for="name">К/С</label>
              <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid': errors.ks }"
                  id="name"
                  placeholder="Введите К/С банка"
                  v-model="data.ks"
              />
              <div v-if="errors.ks" class="invalid-feedback">
                <span v-for="error in errors.ks " v-html="error" />
              </div>
            </div>

            <div class="form-group mb-4 col-lg-6">
              <label for="name">Номер счета</label>
              <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid': errors.number }"
                  id="name"
                  placeholder="Введите номер счета"
                  v-model="data.number"
              />
              <div v-if="errors.number" class="invalid-feedback">
                <span v-for="error in errors.number " v-html="error" />
              </div>
            </div>
          </div>

          <div class="col-12 row">
            <div class="form-group mb-4 col-lg-6">
              <label for="name">Баланс</label>
              <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid': errors.balance }"
                  id="name"
                  placeholder="Введите текущий баланс на счете"
                  v-model="data.balance"
              />
              <div v-if="errors.balance" class="invalid-feedback">
                <span v-for="error in errors.balance " v-html="error" />
              </div>
            </div>

            <div class="form-group mb-4 col-lg-6">
              <label for="name">Валюта</label>
              <Multiselect
                v-model="data.currency"
                :class="{'is-invalid': errors.balance }"
                :options="currencyList"
                :placeholder="'Выберите валюту счета'"
                :searchable="true"
              />
              <div v-if="errors.currency" class="invalid-feedback">
                <span v-for="error in errors.currency " v-html="error" />
              </div>
            </div>

          </div>

        </div>
      </div>
    </template>
    <template #footer>
      <button class="btn btn-primary" @click="createAccount">Создать счет</button>
    </template>
  </Modal>
</template>