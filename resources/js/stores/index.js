import { createPinia } from 'pinia';
import { useSessionStore } from './useSessionStore.js';
import { createPersistedStatePlugin } from 'pinia-plugin-persistedstate-2'


const pinia = createPinia()

let persistedStateOptions = {}

pinia.use(createPersistedStatePlugin(persistedStateOptions))

export {pinia,useSessionStore}