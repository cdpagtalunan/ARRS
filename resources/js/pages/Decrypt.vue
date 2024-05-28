<template>
    <div class="text-center mt-3">
        <label><strong>Decryption of ID</strong></label>
        <div class="input-group d-flex justify-content-center">
            <input type="text" v-model="encryptedId" aria-describedby="button-addon2">
            <button @click="decryptId()" class="btn btn-sm btn-success" id="button-addon2">Decrypt</button>
        </div>
        <label>ID is: {{ decryptedId }}</label>
    </div>
    

    <div class="text-center mt-2">
        <label><strong>Manual Generation of Recon.</strong></label><br>

        <button type="button" class="btn btn-sm btn-info mr-1" @click="loadDataEPRPO(1)">Load 1st cutoff</button>
        <button type="button" class="btn btn-sm btn-info" @click="loadDataEPRPO(2)">Load 2nd cutoff</button>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import api from '../axios';
    const encryptedId = ref();
    const decryptedId = ref();

    const decryptId = async (decryptId) => {
        await api.get('api/decrypt_id', {params: {Id: encryptedId.value}}).then((result) => {
            decryptedId.value = result.data;
        }).catch((err) => {
            
        });
    }

    const loadDataEPRPO = async (date) => {
        await api.get('/get_eprpo_data', { params: {cutoff:date, bypass: 1} }).then((result) => {
            // console.log(result);
            if(result['data']['response'] == true){
                alert('Success!!');
            }
        }).catch((err) => {
            
        });
    }
</script>