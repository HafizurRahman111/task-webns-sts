<template>
  <AppLayout>
    <FormCard 
      title="Login" 
      footerText="Don't have an account? Sign Up"
      :errorMessage="errorMessage"
      @submit="handleLogin"
    >
      <FormInput
        id="email"
        label="Email"
        type="email"
        v-model="email"
        placeholder="Enter your email"
        :error="errors.email"
        @blur="validateEmail"
      />
      
      <PasswordInput
        id="password"
        label="Password"
        v-model="password"
        placeholder="Enter your password"
        :error="errors.password"
        @blur="validatePassword"
      />
      
      <SubmitButton label="Login" :loading="isSubmitting" />
      
      <template #footer>
        Don't have an account?
        <router-link to="/register" class="link">Sign Up</router-link>
      </template>
    </FormCard>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppLayout from '@/layouts/AppLayout.vue'
import FormCard from '@/components/ui/FormCard.vue'
import FormInput from '@/components/ui/FormInput.vue'
import PasswordInput from '@/components/ui/PasswordInput.vue'
import SubmitButton from '@/components/ui/SubmitButton.vue'

// Form state
const email = ref('')
const password = ref('')
const isSubmitting = ref(false)

// Error handling
const errorMessage = ref('')
const errors = ref({
  email: '',
  password: ''
})

// Dependencies
const authStore = useAuthStore()
const router = useRouter()

// Redirect if already authenticated
onMounted(() => {
  if (authStore.isAuthenticated) {
    router.replace('/dashboard')
  }
})

// Form validation
const validateEmail = () => {
  if (!email.value) {
    errors.value.email = 'Email is required'
    return false
  }
  if (!/^\S+@\S+\.\S+$/.test(email.value)) {
    errors.value.email = 'Please enter a valid email'
    return false
  }
  errors.value.email = ''
  return true
}

const validatePassword = () => {
  if (!password.value) {
    errors.value.password = 'Password is required'
    return false
  }
  if (password.value.length < 8) {
    errors.value.password = 'Password must be at least 8 characters'
    return false
  }
  errors.value.password = ''
  return true
}

const validateForm = () => {
  const isEmailValid = validateEmail()
  const isPasswordValid = validatePassword()
  return isEmailValid && isPasswordValid
}

// Form submission
const handleLogin = async () => {
  if (!validateForm()) return

  isSubmitting.value = true
  errorMessage.value = ''

  try {
    await authStore.login({
      email: email.value,
      password: password.value
    })
    router.push('/dashboard')
  } catch (error) {
    handleLoginError(error)
  } finally {
    isSubmitting.value = false
  }
}

const handleLoginError = (error) => {
  if (error.response?.status === 401) {
    errorMessage.value = 'Invalid email or password'
  } else if (error.response?.data?.errors) {
    // Handle backend validation errors
    const apiErrors = error.response.data.errors
    errors.value = {
      email: apiErrors.email?.[0] || '',
      password: apiErrors.password?.[0] || ''
    }
  } else {
    errorMessage.value = 'An unexpected error occurred. Please try again.'
    console.error('Login error:', error)
  }
}
</script>

<style scoped>
.link {
  color: var(--primary);
  font-weight: 500;
  text-decoration: none;
}

.link:hover {
  text-decoration: underline;
}
</style>