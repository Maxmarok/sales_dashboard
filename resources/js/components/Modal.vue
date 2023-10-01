<script setup>
import { ref, defineProps, defineExpose, onMounted } from "vue"
import { Modal } from "bootstrap"

defineProps({
  title: {
    type: String,
    default: "Title 123",
  },
  center: {
    type: Boolean,
    default: false
  },
  large: {
    type: Boolean,
    default: true
  },
});

const modalEle = ref();
let thisModalObj = null;

onMounted(() => {
    thisModalObj = new Modal(modalEle.value);
})

const _show = () => thisModalObj.show();
const _hide = () => thisModalObj.hide();
defineExpose({ show: _show, hide: _hide });
</script>

<template>
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby=""
    aria-hidden="true" ref="modalEle">
    <div class="modal-dialog" 
      :class="{'modal-dialog-centered': center, 'modal-xl': large}">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{ title }}</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" @click="_hide">&times;</button>
        </div>
        <div class="modal-body">
          <slot name="body" />
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" @click="_hide">
            Закрыть
          </button>
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </div>
</template>