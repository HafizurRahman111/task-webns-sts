<template>
    <section class="features-section">
      <h2 class="section-title">Key Features</h2>
      <div class="features-grid">
        <FeatureCard 
          title="Efficient Ticket Management"
          description="Organize and prioritize support tickets with ease. Assign, track, and resolve issues faster."
          color="blue"
        />
        <FeatureCard 
          title="Real-Time Collaboration"
          description="Collaborate with your team in real-time. Share updates, comments, and solutions seamlessly."
          color="purple"
        />
        <FeatureCard 
          title="Advanced Analytics"
          description="Gain insights with detailed reports and analytics. Monitor performance and improve efficiency."
          color="green"
        />
      </div>
    </section>
  </template>
  
  <script setup>
  import FeatureCard from '@/components/home/FeatureCard.vue'
  </script>
  
  <style>
  /* Base styles */
  .features-section {
    margin-left: auto;
    margin-right: auto;
    padding: 1.5rem 1rem;
  }
  
  .section-title {
    font-size: 1.5rem;
    font-weight: 700;
    text-align: center;
    color: #2563eb;
    margin-bottom: 2rem;
    padding: 0.5rem 0;
  }
  
  .features-grid {
    display: grid;
    gap: 1.5rem;
  }
  
  /* Responsive styles */
  @media (min-width: 768px) {
    .features-section {
      padding-left: 1.5rem;
      padding-right: 1.5rem;
    }
    
    .section-title {
      font-size: 1.875rem;
      margin-bottom: 3rem;
    }
    
    .features-grid {
      gap: 2rem;
      grid-template-columns: repeat(2, 1fr);
    }
  }
  
  @media (min-width: 1024px) {
    .features-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }
  </style>