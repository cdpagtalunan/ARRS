import {createRouter, createWebHistory} from 'vue-router';
import AdminLayout from '../layout/AdminLayout/AdminLayout.vue' // ^ Main Template
import Dashboard from '../pages/Dashboard.vue';
import Reconciliation from '../pages/Reconciliation.vue';
import UserRequest from '../pages/UserRequest.vue';

// ^ Setting Pages
import CuttoffSettings from '../pages/Settings/CutoffSettings.vue';
import CategorySettings from '../pages/Settings/CategorySettings.vue';

// ^ Admin Pages
import UserManagement from '../pages/Admin/UserManagement.vue';
import ApprovalRequest from '../pages/Admin/ApprovalRequest.vue';
import AdminExport from '../pages/Admin/AdminExportRecon.vue';
import FinalRecon from '../pages/Admin/FinalRecon.vue';

// ^ Interceptors Page
import Unauthorized from '../pages/Interceptors/Unauthorized.vue';

// ^ Mailer Page
import Mailer from '../pages/Mailer.vue';


import Decrypt from '../pages/Decrypt.vue';

import api from '../axios';

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

import { useSessionStore } from "../stores/index";
const hasAccess =  () => { // * TO VALIDATE USER HAS ACCESS ON SYSTEM
    // const sessionStore = useSessionStore();
    // sessionStore.checkSession();
}


const routes = [
    {
        // path: "/ARRS/",
        path: "/ARRS_rev/",
        component: AdminLayout,
        // beforeEnter: hasAccess,
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
            {
                path: 'request_list',
                name: 'UserRequest',
                beforeEnter: isLoggedIn,
                component: UserRequest
            },
            // Admin
            {
                path: 'user_management',
                name: 'UserManagement',
                beforeEnter: isLoggedIn,
                component: UserManagement
            },
            {
                path: 'approval_request',
                name: 'ApprovalRequest',
                beforeEnter: isLoggedIn,
                component: ApprovalRequest
            },
            {
                path: 'admin_export',
                name: 'AdminExport',
                beforeEnter: isLoggedIn,
                component: AdminExport
            },
            {
                path: 'final_recon',
                name: 'FinalRecon',
                beforeEnter: isLoggedIn,
                component: FinalRecon
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
        // path: '/ARRS/',
        path: '/ARRS_rev/',
        component: '',
        children:  [
            {
                path: 'unauthorized',
                name: 'Unauthorized',
                component: Unauthorized
            },
            {
                path: 'decrypt',
                name: 'Decrypt',
                component: Decrypt
            },
        ]
    },
    {
        path: '/ARRS/mailer/',
        component: '',
        children:  [
            {
                path: 'mail',
                name: 'Mailer',
                component: Mailer
            }
        ]
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});


export default router;
