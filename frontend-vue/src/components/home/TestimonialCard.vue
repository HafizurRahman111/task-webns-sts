<template>
    <div class="testimonial-card">
      <p class="testimonial-quote">
        {{ quote }}
      </p>
      <p class="testimonial-author">
        {{ author }}
      </p>
    </div>
  </template>
  
  <script setup>
  defineProps({
    quote: String,
    author: String
  })
  </script>
  
  <style>
  .testimonial-card {
    padding: 1.5rem;
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  }
  
  .testimonial-quote {
    color: #374151;
    font-style: italic;
  }
  
  .testimonial-author {
    margin-top: 1rem;
    font-size: 0.875rem;
    color: #6b7280;
  }
  
  /* Responsive padding */
  @media (min-width: 768px) {
    .testimonial-card {
      padding: 2rem;
    }
  }
  </style>