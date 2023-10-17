import {createRouter, createWebHistory} from 'vue-router';
import AdminLayout from '../layout/AdminLayout/AdminLayout.vue' // ^ Main Template
import Dashboard from '../pages/Dashboard.vue';

// ^ Setting Pages
import CuttoffSettings from '../pages/Settings/CutoffSettings.vue';
const routes = [
    {
        path: "/ARRS/",
        component: AdminLayout,
        // beforeEnter: isLoggedIn,
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: Dashboard
            },
            {
                path: 'cutoff_settings',
                name: 'SettingsCutoff',
                component: CuttoffSettings
            },
           
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});


export default router;