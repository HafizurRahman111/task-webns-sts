<template>
  <SiteLayout>
    <!-- Hero Section -->
    <HeroSection />
    
    <!-- Features Section -->
    <FeaturesSection />
    
    <!-- Testimonials Section -->
    <TestimonialsSection />
  </SiteLayout>
</template>

<script setup>
import SiteLayout from '@/layouts/AppLayout.vue'
import HeroSection from '@/components/home/HeroSection.vue'
import FeaturesSection from '@/components/home/FeaturesSection.vue'
import TestimonialsSection from '@/components/home/TestimonialsSection.vue'
</script>