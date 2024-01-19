<template>
    <center>
        <input type="text" v-model="encryptedId">
        <button @click="decryptId()">Decrypt</button>
        <br>
        <label>ID is: {{ decryptedId }}</label>
    </center>
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
</script>