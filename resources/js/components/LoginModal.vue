<script setup>
import { ref, defineExpose } from "vue";
import Modal from "@/components/Modal.vue";

let thisModal = ref(null);

const _showModal = () => thisModal.value.show();

defineExpose({ showModal: _showModal });

</script>

<template>
  <!-- <button @click="showModal">Show Modal</button> -->
  <Modal title="Model title goes here" ref="thisModal">
    <template #body>This should be in the body</template>
    <template #footer>
      <button class="btn btn-primary">Extra footer button</button>
    </template>
</Modal>
</template>