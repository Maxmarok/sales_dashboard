<script setup>
import { defineEmits } from 'vue';
const emit = defineEmits(['toggleMenu'])

const onToggleMenu = () => {
    emit('toggleMenu');
}
</script>

<template>
    <header id="page-topbar">
      <div class="navbar-header">
        <div class="d-flex">
          <!-- LOGO -->
          <div class="navbar-brand-box">
            <a href="index.html" class="logo logo-dark">
              <span class="logo-sm">
                <img src="@/assets/images/logo-sm-dark.png" height="22" />
              </span>
              <span class="logo-lg">
                <img src="@/assets/images/logo-dark.png" height="20" />
              </span>
            </a>
  
            <a href="index.html" class="logo logo-light">
              <span class="logo-sm">
                <img src="@/assets/images/logo-sm-light.png" height="22" />
              </span>
              <span class="logo-lg">
                <img src="@/assets/images/logo-light.png" height="20" />
              </span>
            </a>
          </div>
  
          <button
            @click="onToggleMenu"
            type="button"
            class="btn btn-sm px-3 font-size-24 header-item waves-effect"
            id="vertical-menu-btn"
          >
            <i class="ri-menu-2-line align-middle"></i>
          </button>

        </div>
  
        <div class="d-flex">  
          <div class="dropdown d-inline-block">
            <button
              type="button"
              class="btn header-item noti-icon right-bar-toggle waves-effect toggle-right"
              @click="toggleRightSidebar"
            >
              <i class="ri-settings-2-line toggle-right"></i>
            </button>
          </div>
        </div>
      </div>
    </header>
  </template>