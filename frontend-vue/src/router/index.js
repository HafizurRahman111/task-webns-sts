import { createRouter, createWebHistory } from 'vue-router';
import SiteLayout from '@/components/layouts/SiteLayout.vue';
import Home from '@/views/Home.vue'; // Ensure this path is correct
import Login from '@/views/Login.vue'; // Ensure this path is correct
import Register from '@/views/Register.vue';
// import DashboardLayout from '@/components/layouts/DashboardLayout.vue'; // Uncomment if you have this component
// import Dashboard from '@/views/Dashboard.vue'; // Uncomment if you have this component

const routes = [
  {
    path: '/',
    children: [
      {
        path: '', // Default route for '/'
        name: 'Home',
        component: Home, // Render Home component inside SiteLayout
      },
      {
        path: 'login', // Route for '/login'
        name: 'Login',
        component: Login, // Render Login component inside SiteLayout
      },
      {
        path: 'register', // Route for '/register'
        name: 'Register',
        component: Register, // Render Register component inside SiteLayout
      },
    ],
  },
  // Uncomment this block if you have a dashboard section
  // {
  //   path: '/dashboard',
  //   component: DashboardLayout, // Use DashboardLayout as the layout for nested routes
  //   children: [
  //     {
  //       path: '', // Default route for '/dashboard'
  //       name: 'Dashboard',
  //       component: Dashboard, 
  // meta: { requiresAuth: true }// Render Dashboard component inside DashboardLayout
  //     },
  //   ],
  // },
];

const router = createRouter({
  history: createWebHistory(), // Use history mode for clean URLs
  routes,
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  if (to.meta.requiresAuth && !token) {
    next('/login');
  } else {
    next();
  }
});

export default router;