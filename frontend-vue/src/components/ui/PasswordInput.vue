<template>
    <FormInput
      :id="id"
      :label="label"
      :type="showPassword ? 'text' : 'password'"
      :modelValue="modelValue"
      :placeholder="placeholder"
      :error="error"
      @update:modelValue="$emit('update:modelValue', $event)"
      @blur="$emit('blur')"
    >
      <template #append>
        <button
          type="button"
          @click="togglePasswordVisibility"
          class="password-toggle"
          :aria-label="showPassword ? 'Hide password' : 'Show password'"
        >
          <EyeIcon :visible="showPassword" />
        </button>
      </template>
    </FormInput>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import FormInput from './FormInput.vue'
  import EyeIcon from '../icons/EyeIcon.vue'
  
  defineProps({
    id: String,
    label: String,
    modelValue: String,
    placeholder: String,
    error: String
  })
  
  defineEmits(['update:modelValue', 'blur'])
  
  const showPassword = ref(false)
  
  const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value
  }
  </script>
  
  <style scoped>
  .password-toggle {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.25rem;
    color: rgb(43, 40, 40);
  }
  </style>