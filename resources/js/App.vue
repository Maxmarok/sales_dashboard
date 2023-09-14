<script setup>
import { onMounted, ref } from 'vue'
onMounted(() => {
  document.body.setAttribute("data-sidebar", "dark")
})
</script>

<template>
<div>
  <router-view />
</div>
</template>
