<script setup>
import { ref, defineExpose, defineProps, defineEmits, inject, watch, onMounted } from "vue";
import Modal from "@/components/Modal.vue";
const emit = defineEmits(['action'])
const doAction = (id) => emit('action', id)

let thisModal = ref(null);

const _showModal = () => thisModal.value.show()
const _hideModal = () => thisModal.value.hide()

defineExpose({ showModal: _showModal, hideModal: _hideModal})

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
    default: true
  },
  large: {
    type: Boolean,
    default: false
  },
  item: {
    type: Object,
    default: null,
  }
});

const data = ref({
  title: null,
  description: null,
  article_type: null,
  type: null,
})

const errors = ref({
  title: null,
  description: null,
  article_type: null,
  type: null,
})

const swal = inject('$swal')

const createArticle = () => {
  axios.post('/api/v1/profile/article/add', data.value)
    .then(res => {
      thisModal.value.hide()
      swal.fire({
        text: 'Статья операции успешно добавлена!',
        position: 'top-end',
        toast: true,
        showConfirmButton: false,
        icon: 'success',
        timer: 3000,
      })
      doAction(res.data.account.id)
    })
    .catch(err => {
      if(err.response !== undefined) errors.value = err.response.data.errors
    })
}

const editArticle = () => {
  axios.post('/api/v1/profile/article/update', data.value)
    .then(res => {
      thisModal.value.hide()
      swal.fire({
        text: 'Статья операции успешно изменена!',
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

const setDefault = () => {
  data.value.title = null
  data.value.description = null
  data.value.article_type = props.type
  data.value.type = null
  data.value.id = null
}

watch(() => props.item, function() {
  if(props.item !== null) {
    data.value.title = props.item.title
    data.value.description = props.item.description
    data.value.article_type = props.item.article_type
    data.value.type = props.item.type
    data.value.id = props.item.id
  } else {
    setDefault()
  }
})

watch(() => props.type, function() {
  if(props.type) data.value.article_type = props.type
})

</script>

<template>
  <!-- <button @click="showModal">Show Modal</button> -->
  <Modal :title="title" ref="thisModal" :center="props.center" :large="props.large">
    <template #body>
      <div>
        <div class="form-group mb-4 col-lg-12">
          <label for="title">Название</label>
          <input
              type="text"
              class="form-control"
              :class="{'is-invalid': errors.title }"
              id="title"
              placeholder="Введите название статьи"
              v-model="data.title"
              @input="changeInput('title')"
          />

          <div v-if="errors.title" class="invalid-feedback">
            <span v-for="error in errors.title " v-html="error" />
          </div>
        </div>

        <div class="form-group col-lg-12" v-if="type === 'consume'">

          <div>
            <div class="custom-control custom-radio">
              <input type="radio" id="main" class="custom-control-input" value="main" v-model="data.type">
              <label for="main" class="custom-control-label card-title text-primary">На основную деятельность</label>
            </div>

            <p class="card-title-desc font-size-12 ml-4">
              Все движения денег, связанные с основной ежедневной работой бизнеса: выплата ЗП, аренда офиса, реклама и т.п.
            </p>
          </div>

          <div>
            <div class="custom-control custom-radio">
              <input type="radio" id="buying" class="custom-control-input" value="buying" v-model="data.type">
              <label for="buying" class="custom-control-label card-title text-primary">На закупку товара</label>
            </div>

            <p class="card-title-desc font-size-12 ml-4">
              Приобретение товаров или материалов на склад. Не учитывается как расход в отчете о прибылях и убытках.
            </p>
          </div>

          <div>
            <div class="custom-control custom-radio">
              <input type="radio" id="invest" class="custom-control-input" value="invest" v-model="data.type">
              <label for="invest" class="custom-control-label card-title text-danger">На основные средства и капитальные вложения</label>
            </div>

            <p class="card-title-desc font-size-12 ml-4">
              Покупка и обслуживание основных средств: оборудования, имущества, значимых объектов инфраструктуры: например, ремонт в офисе или покупка станка.
            </p>
          </div>

          <div>
            <div class="custom-control custom-radio">
              <input type="radio" id="credit" class="custom-control-input" value="credit" v-model="data.type">
              <label for="credit" class="custom-control-label card-title text-success">На выплату тела кредита, займа или возвратного депозита</label>
            </div>

            <p class="card-title-desc font-size-12 ml-4">
              Учитывается только тело кредита и не влияет на отчет о прибылях и убытках.
            </p>
          </div>
        </div>

        <div class="form-group col-lg-12" v-if="type === 'profit'">

          <div>
            <div class="custom-control custom-radio">
              <input type="radio" id="main" class="custom-control-input" value="main" v-model="data.type">
              <label for="main" class="custom-control-label card-title text-primary">От основной деятельности</label>
            </div>

            <p class="card-title-desc font-size-12 ml-4">
              Все движения денег, связанные с основной ежедневной работой бизнеса, которая обеспечивает прибыль: поступления от продажи товаров и услуг.
            </p>
          </div>

          <div>
            <div class="custom-control custom-radio">
              <input type="radio" id="buying" class="custom-control-input" value="buying" v-model="data.type">
              <label for="buying" class="custom-control-label card-title text-danger">От продажи основных средств</label>
            </div>

            <p class="card-title-desc font-size-12 ml-4">
              Не будет считаться прибылью и не повлияет на отчет о прибылях и убытках.
            </p>
          </div>

          <div>
            <div class="custom-control custom-radio">
              <input type="radio" id="credit" class="custom-control-input" value="credit" v-model="data.type">
              <label for="credit" class="custom-control-label card-title text-success">Получение кредита, займа или возвратного депозита</label>
            </div>

            <p class="card-title-desc font-size-12 ml-4">
              Вы получаете заемные средства, которые потом могут пойти в инвестиционную или операционную деятельность.
            </p>
          </div>

          <div>
            <div class="custom-control custom-radio">
              <input type="radio" id="profit" class="custom-control-input" value="profit" v-model="data.type">
              <label for="profit" class="custom-control-label card-title text-success">Ввод денег в бизнес</label>
            </div>

            <p class="card-title-desc font-size-12 ml-4">
              Не влияет на отчет о прибылях и убытках.
            </p>
          </div>

          
        </div>

        <div class="form-group mb-4 col-lg-12">
          <label for="description">Комментарий</label>
          <textarea
              class="form-control"
              :class="{'is-invalid': errors.description }"
              id="description"
              placeholder="Введите комментарий"
              v-model="data.description"
          />
          <div v-if="errors.description" class="invalid-feedback">
            <span v-for="error in errors.description " v-html="error" />
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <button class="btn btn-sm btn-primary" @click="editArticle" v-if="item">Сохранить изменения</button>
      <button class="btn btn-sm btn-primary" @click="createArticle" v-else>Создать статью</button>
    </template>
</Modal>
</template>