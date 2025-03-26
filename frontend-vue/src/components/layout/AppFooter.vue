<template>
    <footer class="app-footer">
      <div class="footer-container">
        <p class="copyright">
          &copy; {{ new Date().getFullYear() }} Support Ticketing System
        </p>
      </div>
    </footer>
  </template>
  
  <style scoped>
  .app-footer {
    background: linear-gradient(to right, #4b5563, #374151);
    color: white;
    padding: 1.5rem 0;
    margin-top: auto;
  }
  
  .footer-container {
    width: 100%;
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1rem;
    text-align: center;
  }
  
  .copyright {
    font-size: 0.875rem;
    line-height: 1.25rem;
  }
  
  @media (min-width: 640px) {
    .footer-container {
      padding: 0 1.5rem;
    }
  }
  </style>