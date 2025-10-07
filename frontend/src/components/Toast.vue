<template>
  <div v-if="visible" :class="['toast', `toast-${type}`]">
    {{ message }}
  </div>
</template>

<script>
export default {
  name: 'Toast',
  props: {
    message: String,
    type: {
      type: String,
      default: 'info'
    },
    duration: {
      type: Number,
      default: 3000
    }
  },
  data() {
    return {
      visible: false
    }
  },
  mounted() {
    this.show()
  },
  methods: {
    show() {
      this.visible = true
      setTimeout(() => {
        this.visible = false
        this.$emit('close')
      }, this.duration)
    }
  }
}
</script>

<style scoped>
.toast {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  color: white;
  font-weight: bold;
  z-index: 1000;
  animation: slideIn 0.3s ease;
}

.toast-success { background: #4CAF50; }
.toast-error { background: #f44336; }
.toast-info { background: #2196F3; }
.toast-warning { background: #ff9800; }

@keyframes slideIn {
  from { transform: translateX(100%); }
  to { transform: translateX(0); }
}
</style>