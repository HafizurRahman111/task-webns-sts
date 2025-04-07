<template>
  <div class="form-group">
    <label :for="id" class="form-label">{{ label }}</label>
    <div class="input-wrapper">
      <input :id="id" :type="type" :value="modelValue" :placeholder="placeholder" class="form-input"
        :class="{ 'input-error': error }" @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur')" />
      <slot name="append"></slot>
    </div>
    <p v-if="error" class="error-message">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
  id: String,
  label: String,
  type: {
    type: String,
    default: 'text'
  },
  modelValue: [String, Number],
  placeholder: String,
  error: String
})

defineEmits(['update:modelValue', 'blur'])
</script>

<style scoped>
.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  font-size: 1rem;
  font-weight: 500;
  color: rgb(37, 37, 37);
}

.input-wrapper {
  position: relative;
}

.form-input {
  width: 100%;
  padding: 0.3rem 1rem;
  border: 1px solid rgb(236, 236, 236);
  border-radius: .5rem;
  font-size: 1rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: rgb(61, 223, 29);
  box-shadow: 0 0 0 3px rgb(3, 145, 3);
}

.input-error {
  border-color: rgb(253, 55, 55);
}

.input-error:focus {
  box-shadow: 0 0 0 3px rgb(201, 5, 5);
}

.error-message {
  color: rgb(241, 46, 46);
  font-size: 0.8rem;
  margin-top: 0.2rem;
}
</style>