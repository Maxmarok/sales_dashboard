<script setup>
import {onMounted, ref, inject} from 'vue'
import Multiselect from "@vueform/multiselect"
import PageHeader from "@/components/PageHeader.vue"
import store from '@/store'
const title = "Отчет о прибылях и убытках"
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

const swal = inject('$swal')

const options = ref({
  year: [
    '2023',
    '2022',
  ],
  lks: [
    {value: 0, label: 'Все магазины'}
  ]   
})

const filter = ref({
  year: '2023',
  lk: 0
})

const disabled = ref(false);

const data = ref([
  {sales: null},
  {penalty: null},
  {delivery: null},
  {consume: null}
]);
onMounted(() => {
  getStores()
  getData()
});

const getSelect = (option) => {
  getData().then(() => {
    swal.fire({
        text: 'Фильтр применен',
        position: 'top-end',
        toast: true,
        showConfirmButton: false,
        icon: 'success',
        timer: 2000,
      })
  })
}

const getData = async () => {

  let obj = {
    year: filter.value.year
  }

  if(filter.value.lk) obj.lk_id = filter.value.lk

  await axios.post(`/api/v1/movements`, obj)
    .then((res) => {
      if(res.data) {
        data.value = res.data.data;
      }

      disabled.value = false;

      return true;
    })
}

const getStores = () => {
  axios.get('/api/v1/profile/lk/list')
    .then(res => {
      res.data.forEach(x => {
        options.value.lks.push({value: x.id, label: x.name})
      })
      console.log(options.value.lks)
    })
}

const getValue = (num, sign = null) => {
  let str =  Math.round(num, 0).toLocaleString() + ' ₽'
  //if(num > 0) str = str + ' ₽'
  if(sign && num > 0) str = `${sign} ${str}`
  return str
}
</script>
<template>
<PageHeader :title="title" :items="items" />
<div class="d-flex align-items-center row mb-3">
  <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-2 mb-sm-0">
    <Multiselect
      v-model="filter.year"
      :options="options.year"
      :placeholder="'Выберите дату'"
      :searchable="true" 
      @select="(selectedOption, id) => getSelect(selectedOption, id)"
    />
  </div>

  <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
    <Multiselect
      v-model="filter.lk"
      :options="options.lks"
      :placeholder="'Выберите магазин'"
      :searchable="true" 
      :allow-empty="true"
      @select="(selectedOption, id) => getSelect(selectedOption, id)"
      :reduce="option => option.id"
    />
  </div>

  <!-- <div class="col-3">
    <button type="button" class="btn btn-success" @click="getData" :disabled="disabled">Искать</button>
  </div> -->
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-sticky mb-0 text-nowrap text-center">
            <thead class="thead-light">
              <tr>
                <th></th>
                <th>Январь</th>
                <th>Февраль</th>
                <th>Март</th>
                <th>Апрель</th>
                <th>Май</th>
                <th>Июнь</th>
                <th>Июль</th>
                <th>Август</th>
                <th>Сентябрь</th>
                <th>Октябрь</th>
                <th>Ноябрь</th>
                <th>Декабрь</th>
                <th>Итого за год</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="data.sales">
                <th scope="row" class="text-centered">Выручка</th>
                <td v-for="item in data.sales" :class="{'text-success': item > 0 }" v-html="getValue(item)" />
              </tr>

              <tr v-if="data.consume">
                <th scope="row">Расходы ВБ</th>
                <td v-for="item in data.consume" :class="{'text-danger': item > 0 }" v-html="getValue(item, '-')" />
              </tr>

              <tr v-if="data.penalty">
                <th scope="row">Штрафы ВБ</th>
                <td v-for="item in data.penalty" :class="{'text-danger': item > 0 }" v-html="getValue(item, '-')" />
              </tr>

              <tr v-if="data.commission">
                <th scope="row">Комиссия ВБ</th>
                <td v-for="item in data.commission" :class="{'text-danger': item > 0 }" v-html="getValue(item, '-')" />
              </tr>


              <tr v-if="data.delivery">
                <th scope="row">Логистика ВБ</th>
                <td v-for="item in data.delivery" :class="{'text-danger': item > 0 }" v-html="getValue(item, '-')" />
              </tr>

              <tr v-if="data.all" class="table-light">
                <th scope="row">К перечислению</th>
                <td v-for="item in data.all" :class="{'text-success': item > 0, 'text-danger': item < 0 }"  v-html="getValue(item)" />
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</template>