// require('./bootstrap');
import './bootstrap';

import { createApp } from 'vue'
// import test from './App.vue';
import 'admin-lte/dist/js/adminlte.min.js';

// import PrimeVue from 'primevue/config';

// // import "primevue/resources/themes/lara-light-indigo/theme.css";
// // import "primevue/resources/themes/bootstrap4-dark-blue/theme.css";

// import "primevue/resources/themes/bootstrap4-light-blue/theme.css";

// import Button from "primevue/button"
// import DataTable from 'primevue/datatable';
// import Column from 'primevue/column';
// import InputText from 'primevue/inputtext';
// import Dropdown from 'primevue/dropdown';
// import Listbox from 'primevue/listbox';


const app = createApp({})
// createApp(AppTemplate)
// app.component('component-test', test)
// app.component('Button', Button)
// app.component('DataTable', DataTable)
// app.component('Column', Column)
// app.component('InputText', InputText)
// app.component('Dropdown', Dropdown)
// app.component('Listbox', Listbox)
// app.use(PrimeVue);
app.mount('#app')