<script setup>
import {ref, inject} from 'vue'
import router from '@/router'
import store from '@/store'
const user = ref({
  email: null,
  name: null,
  password: null,
})
const errors = ref({
  email: null,
  name: null,
  password: null,
})
const swal = inject('$swal')
const loginUser = () => {
  axios.post('/api/v1/login', user.value)
    .then((res) => {
      store.commit('saveUser', res.data.user)
      store.commit('saveToken', res.data.token)
      router.push({name: 'Dashboard'})
    })
    .catch((err) => {
      if(err.response.data.errors !== undefined) errors.value = err.response.data.errors
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
</script>
<template>
<div class="col-lg-4">
  <div class="authentication-page-content p-4 d-flex align-items-center min-vh-100">
    <div class="w-100">
      <div class="row justify-content-center">
        <div class="col-lg-9">
          <div>
            <div class="text-center">
              <div>
                <a href="/" class="logo">
                  <img src="@/assets/images/logo-dark.png" height="20" alt="logo" />
                </a>
              </div>

              <h4 class="font-size-18 mt-4">Авторизация</h4>
            </div>

            <div class="p-2 mt-5">

              <form class="form-horizontal" @submit.prevent="loginUser">

                <div class="form-group auth-form-group-custom mb-4">
                  <i class="ri-mail-line auti-custom-input-icon"></i>
                  <label for="email">Email</label>
                  <input
                    v-model="user.email"
                    type="email"
                    class="form-control"
                    id="email"
                    placeholder="Введите email"
                    :class="{ 'is-invalid': errors.email }"
                  />
                  <div v-if="errors.email" class="invalid-feedback">
                    <span v-for="error in errors.email " v-html="error" />
                  </div>
                </div>

                <div class="form-group auth-form-group-custom mb-4">
                  <i class="ri-lock-2-line auti-custom-input-icon"></i>
                  <label for="password">Пароль</label>
                  <input
                    v-model="user.password"
                    type="password"
                    class="form-control"
                    id="password"
                    placeholder="Введите пароль"
                    :class="{ 'is-invalid': errors.password }"
                  />
                  <div v-if="errors.password" class="invalid-feedback">
                    <span v-for="error in errors.password " v-html="error" />
                  </div>
                </div>

                <div class="text-center">
                  <button
                    class="btn btn-primary w-md waves-effect waves-light"
                    type="submit"
                  >Авторизоваться</button>
                </div>
              </form>
            </div>

            <div class="mt-5 text-center">
              <p>
                Не зарегистрированы?
                <router-link
                  tag="a"
                  :to="{name: 'Register'}"
                  class="font-weight-medium text-primary"
                >Регистрация</router-link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</template>