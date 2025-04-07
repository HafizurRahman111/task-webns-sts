// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Lazy-loaded route components
const HomeView = () => import('@/views/public/HomeView.vue')
const LoginView = () => import('@/views/auth/LoginView.vue')
const RegisterView = () => import('@/views/auth/RegisterView.vue')
const DashboardLayout = () => import('@/layouts/DashboardLayout.vue')
const NotFoundView = () => import('@/views/errors/NotFoundView.vue')

// Dashboard views
const DashboardView = () => import('@/views/dashboard/DashboardView.vue')
const UserListView = () => import('@/views/users/UserListView.vue')
const TicketListView = () => import('@/views/tickets/TicketListView.vue')
const TicketCreateView = () => import('@/views/tickets/TicketCreateView.vue')
const TicketDetailView = () => import('@/views/tickets/TicketDetailView.vue')
const TicketEditView = () => import('@/views/tickets/TicketEditView.vue')
const ChatListView = () => import('@/views/chats/ChatListView.vue')

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomeView,
    meta: {
      title: 'Home',
      transition: 'fade'
    }
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    meta: {
      title: 'Login',
      requiresGuest: true
    }
  },
  {
    path: '/register',
    name: 'Register',
    component: RegisterView,
    meta: {
      title: 'Register',
      requiresGuest: true
    }
  },
  {
    path: '/dashboard',
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: DashboardView,
        meta: {
          title: 'Dashboard',
          transition: 'fade',
          usePathKey: true
        }
      },
      {
        path: 'chats',
        name: 'Chat',
        component: ChatListView,
        meta: {
          title: 'Live Chats',
          transition: 'fade',
          usePathKey: true
        }
      },
      {
        path: 'users',
        name: 'Users',
        component: UserListView,
        meta: {
          title: 'Users',
          transition: 'fade',
          usePathKey: true
        }
      },
      {
        path: 'tickets',
        name: 'Tickets',
        component: TicketListView,
        meta: {
          title: 'Tickets',
          transition: 'fade',
          usePathKey: true
        }
      },
      {
        path: '/tickets/create',
        name: 'TicketCreate',
        component: TicketCreateView,
        meta: {
          title: 'Ticket Create',
          transition: 'fade',
          usePathKey: true
        }
      },
      {
        path: '/tickets/:id',
        name: 'TicketDetails',
        component: TicketDetailView,
        props: true,
        meta: { title: 'Ticket Details' }
      },
      {
        path: '/tickets/:id/edit',
        name: 'TicketEdit',
        component: TicketEditView,
        props: true,
        meta: { title: 'Ticket Edit' }
      },
    ]
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFoundView,
    meta: { title: 'Page Not Found' }
  },
  {
    path: '/:catchAll(.*)',
    name: 'NotFound',
    component: NotFoundView, // Your 404 component
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { top: 0 }
  }
})

// Navigation guards
router.beforeEach(async (to) => {
  const authStore = useAuthStore()

  document.title = to.meta.title
    ? `${to.meta.title} | Support Ticketing System `
    : 'STS'

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return { name: 'Login', query: { redirect: to.fullPath } }
  }

  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    return { name: 'Dashboard' }
  }
})

export default router