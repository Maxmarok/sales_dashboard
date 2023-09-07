<script setup>
import { onMounted, ref } from 'vue'
import TopBar from '@/components/TopBar.vue'
import SideBar from '@/components/SideBar.vue'
const isMenuCondensed = ref(false);

onMounted(() => document.body.setAttribute("data-sidebar", "dark"))

const toggleMenu = () => {
  document.body.classList.toggle("sidebar-enable");

  if (window.screen.width >= 992) {
    document.body.classList.toggle("vertical-collpsed");
  } else {
    document.body.classList.remove("vertical-collpsed");
  }

  isMenuCondensed.value = !isMenuCondensed;
}
</script>

<template>
  <TopBar 
    @toggleMenu="toggleMenu"
  />
  <SideBar 
    :is-condensed="isMenuCondensed"
  />
  <router-view />
</template>
