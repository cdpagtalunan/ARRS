<template>
    <div class="wrapper">

        <Sidebar></Sidebar>

        <Header></Header>
        <div class="content-wrapper" style="height: auto !important;">
            <router-view />
        </div>
        <Footer></Footer>
    </div>

</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import Sidebar from '../../template/Sidebar.vue';
    import Header from '../../template/Header.vue';
    import Footer from '../../template/Footer.vue';


    
      
</script>