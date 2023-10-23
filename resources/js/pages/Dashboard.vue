<template>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1>Administrator</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item active">User</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content mt-2">
        <div class="container-fluid">
            <input type="text">
        </div>
    </section>
</template>

<script setup>
    import { onMounted, ref, onBeforeMount } from 'vue';
    // import axios from 'axios'
    import api from '../axios';
    onBeforeMount( () => {
        validateUser();
    });

    const validateUser = () => {
        // console.log('qwe');
        api.get('api/get_user_login').then((result) => {
                // console.log(result);
        }).catch((err) => { 
                
        });
    }
   
</script>