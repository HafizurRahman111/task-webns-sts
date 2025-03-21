<template>
  <SiteLayout>
    <!-- Hero Section -->
    <section
      class="flex flex-col items-center justify-center text-center py-20 bg-gradient-to-r from-blue-500 to-blue-600 text-white">
      <h1 class="text-5xl font-bold mb-6">Welcome to the Support Ticketing System</h1>
      <p class="text-xl max-w-2xl mx-auto mb-8">
        Streamline your support process with our intuitive ticketing system. Manage, track, and resolve issues
        efficiently.
      </p>
      <router-link to="/register"
        class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg hover:bg-gray-100 transition duration-300">
        Get Started
      </router-link>
    </section>

    <!-- Features Section -->
    <section class="container mx-auto px-6 py-6">
      <h2 class="text-3xl font-bold text-center text-blue-600 mb-12 py-2">Key Features</h2>
      <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        <div class="p-8 bg-white rounded-lg shadow-lg transform hover:scale-105 transition duration-300">
          <h3 class="text-xl font-bold mb-4 text-blue-600">Efficient Ticket Management</h3>
          <p class="text-gray-700">
            Organize and prioritize support tickets with ease. Assign, track, and resolve issues faster.
          </p>
        </div>
        <div class="p-8 bg-white rounded-lg shadow-lg transform hover:scale-105 transition duration-300">
          <h3 class="text-xl font-bold mb-4 text-purple-600">Real-Time Collaboration</h3>
          <p class="text-gray-700">
            Collaborate with your team in real-time. Share updates, comments, and solutions seamlessly.
          </p>
        </div>
        <div class="p-8 bg-white rounded-lg shadow-lg transform hover:scale-105 transition duration-300">
          <h3 class="text-xl font-bold mb-4 text-green-600">Advanced Analytics</h3>
          <p class="text-gray-700">
            Gain insights with detailed reports and analytics. Monitor performance and improve efficiency.
          </p>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="bg-gray-50 py-6">
      <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-blue-600 mb-12">What Our Users Say</h2>
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          <div class="p-8 bg-white rounded-lg shadow-lg">
            <p class="text-gray-700 italic">
              "This platform has revolutionized our support process. Highly recommended!"
            </p>
            <p class="mt-4 text-sm text-gray-500">- John Doe, Support Manager</p>
          </div>
          <div class="p-8 bg-white rounded-lg shadow-lg">
            <p class="text-gray-700 italic">
              "The real-time collaboration feature is a game-changer for our team."
            </p>
            <p class="mt-4 text-sm text-gray-500">- Jane Smith, IT Specialist</p>
          </div>
          <div class="p-8 bg-white rounded-lg shadow-lg">
            <p class="text-gray-700 italic">
              "The analytics dashboard provides valuable insights into our support performance."
            </p>
            <p class="mt-4 text-sm text-gray-500">- Mike Johnson, Operations Lead</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action Section -->
    <section class="bg-blue-600 text-white py-8">
      <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold mb-6 py-1">Ready to Get Started?</h2>
        <p class="text-xl mb-8 py-2">
          Join thousands of satisfied users and take your support process to the next level.
        </p>
        <router-link to="/register"
          class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg hover:bg-gray-100 transition duration-300">
          Sign Up Now
        </router-link>
      </div>
    </section>
  </SiteLayout>
</template>

<script>
import SiteLayout from '@/components/layouts/SiteLayout.vue';

export default {
  name: 'Home',
  components: {
    SiteLayout,
  },
};
</script>

<style scoped>
/* No custom CSS needed - Tailwind handles everything */
</style>