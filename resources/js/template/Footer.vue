<template>
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <!-- <b id="footerTimer"></b> -->
            <b>{{ dt }}</b>
        </div>
        <strong><a href="https://www.pricon.com.ph/index.php/en/" target="_blank">Pricon Microelectronics, Inc.</a></strong>
    </footer>
</template>
<script setup>
    import { ref } from 'vue';
    const dt = ref();
    setInterval(function(){
		var now = new Date();
		dt.value = now.toLocaleString('en-US', { timeZone: 'Asia/Manila' });
	}, 1000);
</script>