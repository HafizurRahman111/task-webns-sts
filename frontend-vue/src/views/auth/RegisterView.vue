<template>
    <AppLayout>
      <FormCard 
        title="Create Your Account" 
        footerText="Already have an account? Sign In"
        :errorMessage="errorMessage"
        @submit="handleRegister"
      >
        <FormInput
          id="name"
          label="Full Name"
          v-model="name"
          placeholder="John Doe"
          :error="errors.name"
          @blur="validateName"
        />
        
        <FormInput
          id="email"
          label="Email"
          type="email"
          v-model="email"
          placeholder="john@example.com"
          :error="errors.email"
          @blur="validateEmail"
        />
        
        <FormInput
          id="phone"
          label="Phone Number"
          type="tel"
          v-model="phone"
          placeholder="+1234567890"
          :error="errors.phone"
          @blur="validatePhone"
        />
        
        <PasswordInput
          id="password"
          label="Password"
          v-model="password"
          placeholder="At least 8 characters"
          :error="errors.password"
          @blur="validatePassword"
        />
        
        <PasswordInput
          id="confirmPassword"
          label="Confirm Password"
          v-model="confirmPassword"
          placeholder="Confirm your password"
          :error="errors.confirmPassword"
          @blur="validateConfirmPassword"
        />
        
        <SubmitButton label="Create Account" :loading="isSubmitting" />
        
        <template #footer>
          Already have an account?
          <router-link to="/login" class="link">Sign In</router-link>
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
  const name = ref('')
  const email = ref('')
  const phone = ref('')
  const password = ref('')
  const confirmPassword = ref('')
  const isSubmitting = ref(false)
  
  // Error handling
  const errorMessage = ref('')
  const errors = ref({
    name: '',
    email: '',
    phone: '',
    password: '',
    confirmPassword: ''
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
  
  // Validation functions
  const validateName = () => {
    if (!name.value) {
      errors.value.name = 'Full name is required'
      return false
    }
    if (name.value.length < 2) {
      errors.value.name = 'Name must be at least 2 characters'
      return false
    }
    errors.value.name = ''
    return true
  }
  
  const validateEmail = () => {
    if (!email.value) {
      errors.value.email = 'Email is required'
      return false
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
      errors.value.email = 'Please enter a valid email'
      return false
    }
    errors.value.email = ''
    return true
  }
  
  const validatePhone = () => {
    if (!phone.value) {
      errors.value.phone = 'Phone number is required'
      return false
    }
    if (!/^[\d\s\+-]{6,20}$/.test(phone.value)) {
      errors.value.phone = 'Please enter a valid phone number'
      return false
    }
    errors.value.phone = ''
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
  
  const validateConfirmPassword = () => {
    if (!confirmPassword.value) {
      errors.value.confirmPassword = 'Please confirm your password'
      return false
    }
    if (confirmPassword.value !== password.value) {
      errors.value.confirmPassword = 'Passwords do not match'
      return false
    }
    errors.value.confirmPassword = ''
    return true
  }
  
  const validateForm = () => {
    const validations = [
      validateName(),
      validateEmail(),
      validatePhone(),
      validatePassword(),
      validateConfirmPassword()
    ]
    return validations.every(Boolean)
  }
  
  // Form submission
  const handleRegister = async () => {
    if (!validateForm()) return
  
    isSubmitting.value = true
    errorMessage.value = ''
  
    try {
      await authStore.register({
        name: name.value,
        email: email.value,
        phone: phone.value,
        password: password.value,
        password_confirmation: confirmPassword.value
      })
      router.push({ name: 'login' })
    } catch (error) {
      handleRegistrationError(error)
    } finally {
      isSubmitting.value = false
    }
  }
  
  const handleRegistrationError = (error) => {
    if (error.response?.data?.errors) {
      const apiErrors = error.response.data.errors
      errors.value = {
        name: apiErrors.name?.[0] || '',
        email: apiErrors.email?.[0] || '',
        phone: apiErrors.phone?.[0] || '',
        password: apiErrors.password?.[0] || '',
        confirmPassword: apiErrors.password_confirmation?.[0] || ''
      }
      
      // Set generic error message if available
      if (error.response.data.message) {
        errorMessage.value = error.response.data.message
      }
    } else {
      errorMessage.value = 'An unexpected error occurred. Please try again.'
      console.error('Registration error:', error)
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