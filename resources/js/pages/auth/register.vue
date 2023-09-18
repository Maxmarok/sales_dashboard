<script setup>
import {ref} from 'vue'
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
const registerUser = () => {
  axios.post('/api/v1/register', user.value)
    .then((res) => {
      store.commit('saveUser', res.data.user)
      store.commit('saveToken', res.data.token)
      router.push({name: 'AddStore'})
    })
    .catch((err) => {
      if(err.response.data.errors !== undefined) errors.value = err.response.data.errors
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

              <h4 class="font-size-18 mt-4">Регистрация</h4>
            </div>

            <div class="p-2 mt-5">

              <form class="form-horizontal" @submit.prevent="registerUser">
                <div class="form-group auth-form-group-custom mb-4">
                  <i class="ri-user-2-line auti-custom-input-icon"></i>
                  <label for="username">ФИО</label>
                  <input
                    v-model="user.name"
                    type="text"
                    class="form-control"
                    id="username"
                    :class="{ 'is-invalid': errors.name }"
                    placeholder="Введите ФИО"
                  />
                  <div v-if="errors.name" class="invalid-feedback">
                    <span v-for="error in errors.name " v-html="error" />
                  </div>
                </div>

                <div class="form-group auth-form-group-custom mb-4">
                  <i class="ri-mail-line auti-custom-input-icon"></i>
                  <label for="useremail">Email</label>
                  <input
                    v-model="user.email"
                    type="email"
                    class="form-control"
                    id="useremail"
                    placeholder="Введите email"
                    :class="{ 'is-invalid': errors.email }"
                  />
                  <div v-if="errors.email" class="invalid-feedback">
                    <span v-for="error in errors.email " v-html="error" />
                  </div>
                </div>

                <div class="form-group auth-form-group-custom mb-4">
                  <i class="ri-phone-line auti-custom-input-icon"></i>
                  <label for="phone">Номер телефона</label>
                  <input
                    v-model="user.phone"
                    type="phone"
                    class="form-control"
                    id="phone"
                    placeholder="Введите номер телефона"
                    :class="{ 'is-invalid': errors.phone }"
                    v-mask="'+7 (###) ###-##-##'"
                  />

                  <div v-if="errors.phone" class="invalid-feedback">
                    <span v-for="error in errors.phone " v-html="error" />
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
                  <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Зарегистрироваться</button>
                </div>

                <div class="mt-4 text-center">
                  <p class="mb-0">Регистрируясь, вы соглашаетесь с <br /> <a href="#" class="text-primary" >Политикой конфиденциальности</a>
                  </p>
                </div>
              </form>
            </div>

            <div class="mt-5 text-center">
              <p>
                Уже зарегистрированы?
                <router-link
                  tag="a"
                  :to="{name: 'Login'}"
                  class="font-weight-medium text-primary"
                >Войти</router-link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</template>