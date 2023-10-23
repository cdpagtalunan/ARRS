// require('./bootstrap');
import './bootstrap';
// import 'bootstrap';

import { createApp } from 'vue'
import AppTemplate from './layout/index.vue';
import router from './router/router';
import 'admin-lte/dist/js/adminlte.min.js';

// * PRIME VUE
import PrimeVue from 'primevue/config';
// import "primevue/resources/themes/lara-light-indigo/theme.css";
// import "primevue/resources/themes/bootstrap4-dark-blue/theme.css";
import "primevue/resources/themes/bootstrap4-light-blue/theme.css";
import Button from "primevue/button"
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Listbox from 'primevue/listbox';
import Paginator from 'primevue/paginator';
import ProgressSpinner from 'primevue/progressspinner';


// * Fontawesome
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { far } from '@fortawesome/free-regular-svg-icons';
import { fas } from '@fortawesome/free-solid-svg-icons';
library.add(far, fas);

createApp(AppTemplate)
.component('Button', Button)
.component('DataTable', DataTable)
.component('Column', Column)
.component('InputText', InputText)
.component('Dropdown', Dropdown)
.component('Listbox', Listbox)
.component('Paginator', Paginator)
.component('ProgressSpinner', ProgressSpinner)
.use(PrimeVue)
.component('icons', FontAwesomeIcon)
.use(router)
.mount('#app')