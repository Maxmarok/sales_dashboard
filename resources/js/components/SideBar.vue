<script setup>
import simplebar from "simplebar-vue"
import { onMounted, watch, ref } from "vue"
import { MetisMenu } from 'metismenujs'
import store from '@/store'
const hasItems = (item) => item.subItems !== undefined ? item.subItems.length > 0 : false

const menu = () => {
    // eslint-disable-next-line no-unused-vars
    var menuRef = new MetisMenu("#side-menu");
    var links = document.getElementsByClassName("side-nav-link-ref");
    var matchingMenuItem = null;
    for (var i = 0; i < links.length; i++) {
        //console.log(window.location.pathname, links[i].pathmane);
      if (window.location.pathname === links[i].pathname) {
        matchingMenuItem = links[i];
        break;
      }
    }

    if (matchingMenuItem && matchingMenuItem !== null) {
      matchingMenuItem.classList.add("active");
      var parent = matchingMenuItem.parentElement;
      
      if (parent && parent !== null) {
        parent.classList.add("mm-active");
        const parent2 = parent.parentElement.closest("ul");
        if (parent2 && parent2.id !== "side-menu") {
          parent2.classList.add("mm-show");
        }
      }
    }
}

// const storesList = ref(store.state.storeList)
// const storesItems = ref(storesList.value ? storesList.value.map(x => {
//     return {label: x.name, link: '/dashboard/reports/'+x.id}
// }) : [])

const menuItems = ref([
    {
        label: 'Рабочий стол',
        icon: 'ri-dashboard-line',
        link: '/dashboard/desktop',
    },

    {
      label: 'Операции',
      icon: 'ri-arrow-left-right-fill',
      link: '/dashboard/finances',
      subItems: [
        {
          label: 'Операции расчета',
          link: '/dashboard/finances/operations'
        },
        {
          label: 'Банковские счета',
          link: '/dashboard/finances/accounts'
        },
        {
          label: 'Статьи операций',
          link: '/dashboard/finances/articles'
        },
      ]
    },

    {
      label: 'Отчеты',
      icon: 'mdi mdi-view-list',
      link: '/dashboard/reports',
      subItems: [
        {
          label: 'Движение средств',
          link: '/dashboard/reports/cashflow'
        },

        {
          label: 'Прибыли и убытки',
          link: '/dashboard/reports/pnl'
        },

        {
          label: 'Анализ расходов',
          link: '/dashboard/reports/expenses'
        },
      ]
    },

    {
      label: 'Платежный календарь',
      icon: 'mdi mdi-calendar-month-outline',
      link: '/dashboard/calendar',
    },

    // {
    //   label: 'Банковские счета',
    //   icon: 'ri-file-text-line',
    //   link: '/dashboard/accounts',
    // },

    {
      label: 'Магазины',
      icon: 'ri-menu-fill',
      link: '/dashboard/stores',
    },

    {
      label: 'Выйти',
      icon: 'ri-logout-box-line',
      link: '/auth/logout'
    }
])

// watch(() => store.getters.storeList, function() {
//   storesItems.value = store.getters.storeList.map(x => {
//     return {label: x.name, link: '/dashboard/stores/'+x.id}
//   })
// });

onMounted(() => menu())
</script>
<template>
    <!-- ========== Left Sidebar Start ========== -->
    <div class="vertical-menu">
      <simplebar class="h-100" ref="currentMenu" id="my-element">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
          <!-- Left Menu Start -->
          <ul class="metismenu list-unstyled" id="side-menu">
            <template v-for="item in menuItems">
              <li class="menu-title" v-if="item.isTitle" :key="item.id">
                {{ item.label }}
              </li>
  
              <!--end Layouts menu -->
              <li v-if="!item.isTitle && !item.isLayout" :key="item.id">
                <a
                  v-if="hasItems(item)"
                  href="javascript:void(0);"
                  class="is-parent"
                  :class="{
                    'has-arrow': !item.badge,
                    'has-dropdown': item.badge,
                  }"
                >
                  <i :class="`bx ${item.icon}`" v-if="item.icon"></i>
                  <span>{{ item.label }}</span>
                  <span
                    :class="`badge badge-pill badge-${item.badge.variant} float-right`"
                    v-if="item.badge"
                    >{{ item.badge.text }}</span
                  >
                </a>
  
                <router-link
                  :to="item.link"
                  v-if="!hasItems(item)"
                  class="side-nav-link-ref"
                  :active-class="'active'"
                >
                  <i :class="`bx ${item.icon}`" v-if="item.icon"></i>
                  <span>{{ item.label }}</span>
                  <span
                    :class="`badge badge-pill badge-${item.badge.variant} float-right`"
                    v-if="item.badge"
                    >{{ item.badge.text }}</span
                  >
                </router-link>
  
                <ul v-if="hasItems(item)" class="sub-menu" aria-expanded="false">
                  <li v-for="(subitem, index) of item.subItems" :key="index">
                    <router-link
                      :to="subitem.link"
                      v-if="!hasItems(subitem)"
                      class="side-nav-link-ref"
                      >{{ subitem.label }}</router-link
                    >
                    <a
                      v-if="hasItems(subitem)"
                      class="side-nav-link-a-ref has-arrow"
                      href="javascript:void(0);"
                      >{{ subitem.label }}</a
                    >
                    <ul
                      v-if="hasItems(subitem)"
                      class="sub-menu mm-collapse"
                      aria-expanded="false"
                    >
                      <li
                        v-for="(subSubitem, index) of subitem.subItems"
                        :key="index"
                      >
                        <router-link
                          :to="subSubitem.link"
                          class="side-nav-link-ref"
                          >{{ subSubitem.label }}</router-link
                        >
                      </li>
                    </ul>
                  </li>
                </ul>
              </li>
            </template>
          </ul>
        </div>
        <!-- Sidebar -->
      </simplebar>
    </div>
    <!-- Left Sidebar End -->
  </template>