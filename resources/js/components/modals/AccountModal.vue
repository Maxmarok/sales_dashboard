<script setup>
import { ref, defineExpose, defineProps } from "vue"
import Modal from "@/components/Modal.vue"

let thisModal = ref(null)

const _showModal = () => thisModal.value.show()

defineExpose({ showModal: _showModal })

const props = defineProps({
  title: String
})

</script>

<template>
  <Modal :title="props.title" ref="thisModal">
    <template #body>
      <div>
        
      </div>
    </template>
    <template #footer>
      <button class="btn btn-primary">Создать счет</button>
    </template>
  </Modal>
</template>