import {createRouter, createWebHistory} from 'vue-router';
import AdminLayout from '../layout/AdminLayout/AdminLayout.vue' // ^ Main Template
import Dashboard from '../pages/Dashboard.vue';

// ^ Setting Pages
import CuttoffSettings from '../pages/Settings/CutoffSettings.vue';
import UserSettings from '../pages/Settings/UserSettings.vue';

// ^ Interceptors Page
import Unauthorized from '../pages/Interceptors/Unauthorized.vue';

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
const hasAccess = async () => { // * TO VALIDATE USER HAS ACCESS ON SYSTEM
    await api.get('check_access').then((result) => {
        
    }).catch((err) => {
        
    });
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
                path: 'cutoff_settings',
                name: 'SettingsCutoff',
                beforeEnter: isLoggedIn,
                component: CuttoffSettings
            },
            {
                path: 'user_settings',
                name: 'SettingsUser',
                beforeEnter: isLoggedIn,
                component: UserSettings
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