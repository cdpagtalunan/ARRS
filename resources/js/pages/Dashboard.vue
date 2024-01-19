<template>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <!-- <h1>Administrator</h1> -->
                </div>
                <!-- <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item active">User</li>
                    </ol>
                </div> -->
            </div>
        </div>
    </section>

    <section class="content mt-2">
        <div class="container-fluid">
            <Card :card-body="true">
                <template #body>
                    <div class="row">

                        <div class="col-12">
                            <h1>Welcome to Auto Reconciliation Report System</h1>
                        </div>
                        <div class="col-12">
                            <h5>For concerns/issues, please contact ISS at local numbers 205, 206, or 208.
                            </h5>
                        </div>
                        <div class="col-12">
                            <h5>Or you may send us e-mail at servicerequest@pricon.ph. Thank you! </h5>
                        </div>



                    </div>
                </template>
            </Card>

        </div>
    </section>
</template>

<script setup>
    import { onMounted, ref, onBeforeMount } from 'vue';
    // import axios from 'axios'
    import api from '../axios';
   
   
</script>