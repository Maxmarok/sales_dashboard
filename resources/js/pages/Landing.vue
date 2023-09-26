<script setup>
import { onMounted, ref } from "vue"
import LoginModal from '@/components/modals/LoginModal.vue'
import RegisterModal from '@/components/modals/RegisterModal.vue'



const modalLogin = ref()
const modalRegister = ref()
const openLoginModal = () => modalLogin.value.showModal()


</script>
<template>
  <LoginModal ref="modalLogin" />
  <RegisterModal ref="modalRegister" />
<!-- 
  <button @click="openLoginModal('modalRegister')">Войти</button>
  <button @click="openLoginModal('modalLogin')">Зарегистрироваться</button> -->

</template>