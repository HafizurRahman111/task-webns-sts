<template>
    <section class="hero-section">
      <h1 class="hero-title">Welcome to the Support Ticketing System</h1>
      <p class="hero-description">
        Streamline your support process with our intuitive ticketing system. Manage, track, and resolve issues
        efficiently.
      </p>
      <router-link to="/register" class="hero-cta">
        Get Started
      </router-link>
    </section>
  </template>
  
  <script setup>
  // Component logic (if needed)
  </script>
  
  <style>
  /* Global styles (not scoped) for Tailwind directives */
  .hero-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 1rem 0;
    background: linear-gradient(to right, #3b82f6, #2563eb);
    color: white;
    border-radius: 0.75rem;
  }
  
  .hero-title {
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    padding: 0.25rem 0;
  }
  
  .hero-description {
    font-size: 1.125rem;
    max-width: 42rem;
    margin: 0 auto 2rem;
    padding: 0.5rem 0;
  }
  
  .hero-cta {
    display: inline-block;
    background: white;
    color: #2563eb;
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
  }
  
  .hero-cta:hover {
    background: #f3f4f6;
  }
  
  /* Responsive styles */
  @media (min-width: 768px) {
    .hero-title {
      font-size: 3rem;
    }
    
    .hero-description {
      font-size: 1.25rem;
    }
    
    .hero-cta {
      padding: 0.75rem 2rem;
    }
  }
  </style>