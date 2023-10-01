<script setup>
import { onMounted, ref, defineExpose, defineProps, inject, defineEmits, watch } from "vue"
import moment from 'moment'
import Modal from "@/components/Modal.vue"
import Multiselect from "@vueform/multiselect"
import AddArticleModal from '@/components/modals/AddArticleModal.vue'
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
    default: true
  },
  item: {
    type: Object,
    default: null,
  }
});

const data = ref({
  type: null,
  value: null,
  art: null,
  date: moment(moment.now()).format('DD.MM.YYYY'),
  description: null,
  account_id: null,
  article_id: null,
  id: null,
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
const articleOptions = ref(null)
const modalTitle = ref()
const modalArticle = ref()
const modalType = ref()

const swal = inject('$swal')

const getData = () => {
  getAccountList()
  getArticleList()
}

const getAccountList = () => {
  axios.get('/api/v1/profile/account/list')
    .then((res) => {
      accountList.value = res.data.map(x => {
        return {value: x.id, label: x.title}
      })
    })
}

const getArticleList = async () => {
  await axios.get('/api/v1/profile/article/list')
    .then((res) => {
      articleList.value = res.data.map(x => {
        return {value: x.id, label: x.title, type: x.article_type}
      })
    })
}

const createOperation = () => {
  axios.post('/api/v1/profile/operation/add', data.value)
    .then(res => {
      thisModal.value.hide()
      swal.fire({
        text: 'Операция расчета успешно добавлена!',
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

const editOperation = () => {
  console.log(data.value)
  axios.post('/api/v1/profile/operation/update', data.value)
    .then(res => {
      thisModal.value.hide()
      swal.fire({
        text: 'Операция успешно изменена!',
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

const changeInput = (name) => {
  if(errors.value[name]) errors.value[name] = null;
}

const openCreateModal = (title, type) => {
    modalTitle.value = title
    modalType.value = type
    modalArticle.value.showModal()
}

const getArticleOptions = () => {
  articleOptions.value = articleList.value.filter(x => x.type === data.value.type)
}

const addArticle = (id) => {
  modalArticle.value.hideModal()
  getArticleList().then(() => {
    data.value.article_id = id
    getArticleOptions()
  })
}

onMounted(() => {
  getData()
})

watch(() => props.type, function() {
  data.value.type = props.type
  getArticleOptions()
});

const setDefault = () => {
  data.value.type = null
  data.value.value = null
  data.value.art = null
  data.value.date = moment(moment.now()).format('DD.MM.YYYY'),
  data.value.description = null
  data.value.account_id = null
  data.value.article_id = null
}

watch(() => props.item, function() {
  if(props.item !== null) {
    data.value.type = props.item.type
    data.value.value = props.item.value
    data.value.art = props.item.art
    data.value.date = props.item.date
    data.value.description = props.item.description
    data.value.account_id = props.item.account_id
    data.value.article_id = props.item.article_id
    data.value.id = props.item.id
  } else {
    setDefault()
  }
});

</script>

<template>
  <Modal :title="title" ref="thisModal" :large="false">
    <template #body>
     
      <div class="row">
        <div class="col-12">
          <div class="form-group mb-4 col-12">
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

          <div class="form-group mb-4 col-12">
            <label for="account_id">Счет</label>
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

          <div class="form-group mb-4 col-12">
            <label for="name">Статья</label>
            <Multiselect
              v-model="data.article_id"
              :reduce="option => option.id"
              :class="[{'is-invalid': errors.article_id}]"
              :options="articleOptions"
              :placeholder="'Выберите статью'"
              :searchable="true"
            />

            <button @click="openCreateModal('Создать новую статью ', 'profit')" class="btn btn-sm btn-outline-primary col-12 mt-2">
              <i class="mdi mdi-plus mr-2"></i> Добавить новую статью
            </button>

            <div v-if="errors.article_id" class="invalid-feedback">
              <span v-for="error in errors.article_id" v-html="error" />
            </div>
          </div>

          <div class="form-group mb-4 col-12">
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
        
          <div class="form-group mb-4 col-12">
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

          <div class="form-group mb-4 col-12">
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
      
    </template>
    <template #footer>
      <button class="btn btn-sm btn-primary" @click="editOperation" v-if="item">Сохранить изменения</button>
      <button class="btn btn-sm btn-primary" @click="createOperation" v-else>{{ title }}</button>
    </template>
  </Modal>

  <AddArticleModal 
    ref="modalArticle" 
    :title="modalTitle"
    :type="modalType"
    @action="(id) => addArticle(id)"
  />
</template>