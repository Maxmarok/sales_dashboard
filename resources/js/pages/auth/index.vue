<script setup>
import {onMounted, onUnmounted} from 'vue'
onMounted(() => document.body.classList.add("auth-body-bg"))
onUnmounted(() => document.body.classList.remove("auth-body-bg"))
</script>
<template>
<div>
  <div class="home-btn d-none d-sm-block">
    <a href="/">
      <i class="mdi mdi-home-variant h2 text-white"></i>
    </a>
  </div>
  <div class="container-fluid p-0">
    <div class="row no-gutters">
        <router-view />
      <div class="col-lg-8">
          <div class="authentication-bg">
            <div class="bg-overlay"></div>
          </div>
      </div>
    </div>
  </div>
</div>
</template>