<template>
    <div class="wrapper">

        <Sidebar></Sidebar>

        <Header :username="store.name"></Header>
        <div class="content-wrapper" style="height: auto !important;">
            <router-view />
        </div>
        <Footer></Footer>
    </div>

</template>
<script setup>
    
    import { onBeforeMount, ref, provide, onMounted } from 'vue';
    import Sidebar from '../../template/Sidebar.vue';
    import Header from '../../template/Header.vue';
    import Footer from '../../template/Footer.vue';
    import { useSessionStore } from '../../stores/useSessionStore.js'
    const store = useSessionStore();
    provide('store', store);

    const beforeUnloadHandler = (event) => {
        // Recommended
        // * this will serve as alert before exiting the system
        // event.preventDefault();

        // Included for legacy support, e.g. Chrome/Edge < 119
        // event.returnValue = true;
        // console.log('qwe');
        store.resetStore();
    };
    if(store.name != ""){

        window.addEventListener("beforeunload", beforeUnloadHandler);
    }
    else{

        window.removeEventListener("beforeunload", beforeUnloadHandler);
    }
</script>