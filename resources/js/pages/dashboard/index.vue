<script setup>
import { onMounted, ref } from 'vue'
import TopBar from '@/components/TopBar.vue'
import SideBar from '@/components/SideBar.vue'
import store from '@/store'
const isMenuCondensed = ref(false);
const loader = ref(true);
onMounted(() => {
  if (loader === true) {
    document.getElementById("preloader").style.display = "block";
    document.getElementById("status").style.display = "block";

    setTimeout(() => {
      document.getElementById("preloader").style.display = "none";
      document.getElementById("status").style.display = "none";
    }, 1000);
  } else {
    document.getElementById("preloader").style.display = "none";
    document.getElementById("status").style.display = "none";
  }

    
})

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
<div>
    <div id="preloader">
      <div id="status">
        <div class="spinner">
          <i class="ri-loader-line spin-icon"></i>
        </div>
      </div>
    </div>

    <div id="layout-wrapper">
        <TopBar 
            @toggleMenu="toggleMenu"
        />
        <SideBar 
            :is-condensed="isMenuCondensed"
        />
       
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <router-view />
                </div>
            </div>
        </div>
    </div>
</div> 
</template>