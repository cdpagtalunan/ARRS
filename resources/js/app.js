require('./bootstrap');
// import './bootstrap';
// import 'bootstrap';

import { createApp } from 'vue'

import AppTemplate from './layout/index.vue';
import router from './router/router';
import 'admin-lte/dist/js/adminlte.min.js';
import { pinia } from './stores/index';


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

// import '../../public/fonts/vendor/fontawesome/css/all.min.css';
// * Fontawesome
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { far } from '@fortawesome/free-regular-svg-icons';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { fab } from '@fortawesome/free-brands-svg-icons';

library.add(far, fas, fab);

// * Components
import Card from './components/Card.vue';
import Breadcrumb from './components/Breadcrumb.vue';
import PrimeVueDatatable from './components/PrimeVueDatatable.vue';
import Modal from './components/Modal.vue';
import ModalPrompt from './components/ModalPrompt.vue';

// * SweetAlert
import Swal from 'sweetalert2';
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})

// * Toastr
import toastr from 'toastr';
toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "3000",
    "timeOut": "3000",
    "extendedTimeOut": "3000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut",
}

import VueMultiselect from 'vue-multiselect'

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
.component('Card', Card)
.component('Breadcrumb', Breadcrumb)
.component('PrimeVueDatatable', PrimeVueDatatable)
.component('Modal', Modal)
.component('ModalPrompt', ModalPrompt)
.component('VueMultiselect', VueMultiselect)
.provide('Swal',Swal)
.provide('Toast',Toast)
.provide('toastr',toastr)
.use(pinia)
.use(router)
.mount('#app')