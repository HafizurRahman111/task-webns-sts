import './assets/main.css'
// Import Font Awesome core
import { library } from '@fortawesome/fontawesome-svg-core';
// Import specific icons
import { faSignInAlt, faUserPlus } from '@fortawesome/free-solid-svg-icons';
// Import FontAwesome component
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
// Add icons to the library
library.add(faSignInAlt, faUserPlus);

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

const app = createApp(App)
app.component('font-awesome-icon', FontAwesomeIcon);
app.use(router)
app.mount('#app')
