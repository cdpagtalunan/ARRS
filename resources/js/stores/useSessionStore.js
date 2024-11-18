import { defineStore } from "pinia";
import api from "../axios";
import Router from "../router/router";

export const useSessionStore = defineStore("session", {
    state: () => ({
        name: null,
        appId: null,
        access: [],
        type: null,
        isAuth: null,
        error: null
    }),
    actions: {
        async checkSession(){
            await api.get('check_access').then((result) => {
            // console.log(result);
                this.name = result.data.uName;
                this.appId = result.data.appid;
                this.access = result.data.uAccess;
                this.type = result.data.uType;
                this.isAuth = result.data.isAuth;
            }).catch((err) => {
                this.error = err;
            });
        },
        resetStore() {
            // console.log("useAuthStore: resetStore");
            this.$reset();
        },
    },
    getters: {
        getType(){
            return this.type;
        }
    },
    persist: true,
});
