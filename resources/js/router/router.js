import {createRouter, createWebHistory} from 'vue-router';
import AdminLayout from '../layout/AdminLayout/AdminLayout.vue' // ^ Main Template
import Dashboard from '../pages/Dashboard.vue';
import Reconciliation from '../pages/Reconciliation.vue';

// ^ Setting Pages
import CuttoffSettings from '../pages/Settings/CutoffSettings.vue';
import CategorySettings from '../pages/Settings/CategorySettings.vue';

// ^ Admin Pages
import UserManagement from '../pages/AdminManagement/UserManagement.vue';

// ^ Interceptors Page
import Unauthorized from '../pages/Interceptors/Unauthorized.vue';

import api from '../axios';

// function isLoggedIn(to, from, next) { // * TO VALIDATE IF SESSION STILL EXIST
//     api.get('check_user').then((result) => {
//         if(result.data == 1){
//             // return true;
//             next();
//         }
//         else{
//             // return window.location.href = '/RapidX';
//             next({
//                 path: '/RapidX',
//                 replace: true
//             });
//         }
//     });
// }
const isLoggedIn = async () => { // * TO VALIDATE IF SESSION STILL EXIST
    await api.get('check_user').then((result) => {
        if(result.data == 1){
            return true;
        }
        else{
            return window.location.href = '/RapidX';
        }
    });
}

// const hasAccess = async () => { // * TO VALIDATE USER HAS ACCESS ON SYSTEM
//     await api.get('check_access').then((result) => {
        
//     }).catch((err) => {
        
//     });
// }

import { useSessionStore } from "../stores/index";
const hasAccess =  () => { // * TO VALIDATE USER HAS ACCESS ON SYSTEM
    const sessionStore = useSessionStore();
    sessionStore.checkSession();
}

const removeAll = () => {
    const sessionStore = useSessionStore();     
    sessionStore.removeSession();

}

const routes = [
    {
        path: "/ARRS/",
        component: AdminLayout,
        beforeEnter: hasAccess,
        children: [
            {
                path: '',
                name: 'Dashboard',
                beforeEnter: isLoggedIn,
                component: Dashboard
            },
            {
                path: 'recon',
                name: 'Reconciliation',
                beforeEnter: isLoggedIn,
                component: Reconciliation
            },
            // Admin Management
            {
                path: 'user_management',
                name: 'UserManagement',
                beforeEnter: isLoggedIn,
                component: UserManagement
            },
            // SETTINGS
            {
                path: 'cutoff_settings',
                name: 'SettingsCutoff',
                beforeEnter: isLoggedIn,
                component: CuttoffSettings
            },
            {
                path: 'category_settings',
                name: 'SettingsCategory',
                beforeEnter: isLoggedIn,
                component: CategorySettings
            },
           
        ]
    },
    {
        path: '/ARRS/',
        component: '',
        children:  [
            {
                path: 'unauthorized',
                name: 'Unauthorized',
                component: Unauthorized
            },
        ]
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});


export default router;