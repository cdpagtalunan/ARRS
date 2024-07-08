import Axios from 'axios';
// import Router from './router';
import Router from './router/router';

const axios = Axios.create()
axios.interceptors.response.use(
    (response) => {
        return response
    },
    (error) => {
        if(error.response && error.response.status === 401){
            Router.push({name: 'Unauthorized'})
            // window.location.href = "http://rapidx";
            console.log('401');
        }

        if(error.response && error.response.status === 403){
            // Router.push({name: 'Forbidden'})
            console.log('403');
        }

        if(error.response && error.response.status === 404){
            // Router.push({name: 'NotFound'})
            console.log('404');
            // Router.push({name: 'Login'})
        }
        if(error.response && error.response.status === 405){
            // Router.push({name: 'NotFound'})
            console.log('405');
        }
        if(error.response && error.response.status === 302){
            // Router.push({name: 'NotFound'})
            console.log('302');
        }
        return Promise.reject(error)
    }
)

const api = {... axios}
// api.defaults.baseURL = import.meta.env.VITE_APP_LARAVEL_SERVER
api.defaults.baseURL = 'http://rapidx/ARRS_rev/';
// api.defaults.baseURL = 'http://rapidx/ARRS/';
// api.get('/csrf-cookie')

export default api