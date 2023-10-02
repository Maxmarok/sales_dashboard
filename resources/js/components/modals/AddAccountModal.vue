<script setup>
import { onMounted, ref, defineExpose, defineProps, defineEmits, inject, watch } from "vue"
import Modal from "@/components/Modal.vue"
import Multiselect from "@vueform/multiselect"
const emits = defineEmits(['action'])
const doAction = () => emits('action')

const thisModal = ref(null)

const _showModal = () => thisModal.value.show()

defineExpose({ showModal: _showModal })
const props = defineProps({
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
  item: {
    type: Object,
    default: null,
  }
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
  id: null,
})

const showBalance = ref()

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
        position: 'top-end',
        toast: true,
        showConfirmButton: false,
        icon: 'success',
        timer: 3000,
      })
      doAction()
    })
    .catch(err => {
      if(err.response !== undefined) errors.value = err.response.data.errors
    })
}

const editAccount = () => {
  axios.post('/api/v1/profile/account/update', data.value)
    .then(res => {
      thisModal.value.hide()
      swal.fire({
        text: 'Банковский счет успешно изменен!',
        position: 'top-end',
        toast: true,
        showConfirmButton: false,
        icon: 'success',
        timer: 3000,
      })
      doAction()
    })
    .catch(err => {
      if(err.response !== undefined) {
        if(err.response.data.message !== undefined) {
          swal.fire({
            text: err.response.data.message,
            position: 'bottom-end',
            backdrop: false,
            showConfirmButton: false,
            icon: 'error',
            timer: 5000,
          })
        }

        if(err.response.data.errors !== undefined) errors.value = err.response.data.errors
      }
    })
}

const changeInput = (name, input) => {
  if(errors.value[name]) errors.value[name] = null

  if(name === 'balance') {
    data.value[name] = input.target.value !== '' ? getValue(input.target.value) : 0
  }
}

const getValue = (num) => {
  return num ? parseInt(num.toLocaleString().replace(/\s/g, '')).toLocaleString() : 0
}

onMounted(() => {
  getData()
})

const setDefault = () => {
  data.value.title = null
  data.value.lk_id = null
  data.value.bank = null
  data.value.bic = null
  data.value.ks = null
  data.value.number = null
  data.value.currency = null
  data.value.balance = null
  data.value.id = null
}

watch(() => props.item, function() {
  if(props.item !== null) {
    data.value.title = props.item.title
    data.value.lk_id = props.item.lk_id
    data.value.bank = props.item.bank
    data.value.bic = props.item.bic
    data.value.ks = props.item.ks
    data.value.number = props.item.number
    data.value.currency = props.item.currency
    data.value.balance = getValue(props.item.balance)
    data.value.id = props.item.id
  } else {
    setDefault()
  }
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
              <label for="lk_id">Магазин</label>
              <Multiselect
                v-model="data.lk_id"
                :reduce="option => option.id"
                :class="[{is_invalid: errors.lk_id}]"
                :options="lkList"
                :placeholder="'Выберите магазин'"
                :searchable="true"
                id="lk_id"
              />

              <div v-if="errors.lk_id" class="invalid-feedback">
                <span v-for="error in errors.lk_id" v-html="error" />
              </div>
            </div>
          </div>

          <div class="col-12 row">
            <div class="form-group mb-4 col-lg-6">
              <label for="bank">Банк</label>
              <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid': errors.bank }"
                  id="bank"
                  placeholder="Введите название банка"
                  v-model="data.bank"
              />

              <div v-if="errors.bank" class="invalid-feedback">
                <span v-for="error in errors.bank " v-html="error" />
              </div>
            </div>

            <div class="form-group mb-4 col-lg-6">
              <label for="bic">БИК</label>
              <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid': errors.bic }"
                  id="bic"
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
              <label for="ks">К/С</label>
              <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid': errors.ks }"
                  id="ks"
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
              <label for="balance">Баланс</label>
              <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid': errors.balance }"
                  id="balance"
                  placeholder="Введите текущий баланс на счете"
                  v-model="data.balance"
                  @input="(val) => changeInput('balance', val)"
              />
              <div v-if="errors.balance" class="invalid-feedback">
                <span v-for="error in errors.balance " v-html="error" />
              </div>
            </div>

            <div class="form-group mb-4 col-lg-6">
              <label for="currency">Валюта</label>
              <Multiselect
                v-model="data.currency"
                :class="{'is-invalid': errors.balance }"
                :options="currencyList"
                :placeholder="'Выберите валюту счета'"
                :searchable="true"
                id="currency"
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
      <button class="btn btn-sm btn-primary" @click="editAccount" v-if="item">Сохранить изменения</button>
      <button class="btn btn-sm btn-primary" @click="createAccount" v-else>Создать счет</button>
    </template>
  </Modal>
</template>