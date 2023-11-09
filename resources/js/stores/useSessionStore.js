import { defineStore } from "pinia";
import api from "../axios";
import Router from "../router/router";

export const useSessionStore = defineStore("session", {
    state: () => ({
        name: null,
        access: [],
    }),
    getters: {
        
    },
    actions: {
        async checkSession(){
            await api.get('check_access').then((result) => {
                // console.log(result);
                this.name = result.data.uName;
                this.access = result.data.uAccess;
                
            }).catch((err) => {
                
            });
        },
        resetStore() {
            // console.log("useAuthStore: resetStore");
            this.$reset();
        },
    },
    persist: true,
});
