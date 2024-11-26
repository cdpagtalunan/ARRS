<template>
    <section class="content">
        <div class="container-fluid">
            <div style="margin-top: 8%;">
                <h4 class="text-center">Auto Reconciliation Report System</h4>

                <hr class="my-4 ">

                <h1 class="display-2 text-center">DO NOT CLOSE</h1>
                
                <hr class="my-4 ">
                
                <p class="text-center">Auto Mail Notification</p>
                <p class="text-center">{{ date }}</p>
                <p class="text-center">{{ timer }}</p>
                <p class="text-center">{{ day }}</p>
            </div>
        </div>
    </section>
</template>
<script setup>
    import { ref, inject, onMounted } from 'vue';
    import api from '../axios';
    import moment from 'moment'
    const timer = ref();
    const date = ref();
    const day = ref();
    // const timer = ref(moment().format('LTS'));
    // onMounted(() => {
        let today = moment().format('MM/DD/YYYY');

        setInterval(() => {
            timer.value = moment().format('LTS');
            date.value = moment().format('l');
            // day.value = moment().format('D');

            if(timer.value === "12:05:00 AM" && moment().format('D') == '16'){ // FIRST RECON
                loadDataEPRPO(1)
            }
            if(timer.value === "12:05:00 AM" && moment().format('D') == '26'){ // SECOND RECON
            // if(timer.value === "8:12:30 AM" && moment().format('D') == '26'){ // SECOND RECON
                loadDataEPRPO(2)
            }


            if(timer.value === "7:30:00 AM"){
                if( ( today >= moment().format('MM/18/YYYY') ) && ( today < moment().format('MM/25/YYYY') )){ // FIRST RECON
                    loadChecker(1)
                }

                if( ( today == moment().format('MM/28/YYYY') ) || ( today <= moment().format('MM/15/YYYY') )){ // SECOND RECON
                    loadChecker(2)
                }
            }
        }, 1000);

        // setTimeout(() => {
        //     loadDataEPRPO(2)
        // }, 500);
    // })

    const loadDataEPRPO = async (param) => {
        await api.get('/get_eprpo_data', { params: {cutoff:param} }).then((result) => {
            console.log(`Data has been loaded ${date.value} ${timer.value} with parameter ${param}`);
            console.log(result);
        }).catch((err) => {
            
        });
    }

    const loadChecker = async (param) => {
        await api.get('/check_recon', { params: {cutoff:param} }).then((result) => {
            // console.log(`Data has been loaded ${date.value} ${timer.value} with parameter ${param}`);
            console.log(result);
        }).catch((err) => {
            
        });
    }

</script>