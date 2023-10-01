<script setup>
import { ref, defineExpose, defineProps } from "vue";
import Modal from "@/components/Modal.vue";

let thisModal = ref(null);

const _showModal = () => thisModal.value.show();

defineExpose({ showModal: _showModal });

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

</script>

<template>
  <!-- <button @click="showModal">Show Modal</button> -->
  <Modal title="Model title goes here" ref="thisModal" :center="true" :large="false">
    <template #body>This should be in the body</template>
    <template #footer>
      <button class="btn btn-primary">Extra footer button</button>
    </template>
</Modal>
</template>