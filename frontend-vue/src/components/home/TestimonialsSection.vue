<template>
    <section class="testimonials-section">
      <h2 class="section-title">What Our Users Say</h2>
      <div class="testimonials-grid">
        <TestimonialCard
          quote="This platform has revolutionized our support process. Highly recommended!"
          author="John Doe, Support Manager"
        />
        <TestimonialCard
          quote="The real-time collaboration feature is a game-changer for our team."
          author="Jane Smith, IT Specialist"
        />
        <TestimonialCard
          quote="The analytics dashboard provides valuable insights into our support performance."
          author="Mike Johnson, Operations Lead"
        />
      </div>
    </section>
  </template>
  
  <script setup>
  import TestimonialCard from '@/components/home/TestimonialCard.vue'
  </script>
  
  <style>
  /* Base styles */
  .testimonials-section {
    background-color: #f9fafb;
    padding: 1.5rem 0;
  }
  
  .section-title {
    font-size: 1.5rem;
    font-weight: 700;
    text-align: center;
    color: #2563eb;
    margin-bottom: 2rem;
    padding: 0.5rem 0;
  }
  
  .testimonials-grid {
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    padding-left: 1rem;
    padding-right: 1rem;
    display: grid;
    gap: 1.5rem;
  }
  
  /* Responsive styles */
  @media (min-width: 768px) {
    .section-title {
      font-size: 1.875rem;
      margin-bottom: 3rem;
    }
    
    .testimonials-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  
  @media (min-width: 1024px) {
    .testimonials-grid {
      grid-template-columns: repeat(3, 1fr);
      max-width: 1280px;
    }
  }
  
  @media (min-width: 640px) {
    .testimonials-grid {
      padding-left: 1.5rem;
      padding-right: 1.5rem;
    }
  }
  </style>