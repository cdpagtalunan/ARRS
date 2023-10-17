<template>
    <div class="card">

        <div class="card-header" v-if="cardHeader">
            <slot name="header"></slot>
        </div>
        <div class="card-body" v-if="cardBody">
            <slot name="body"></slot>
            
        </div>
        <div class="card-footer" v-if="cardFooter">
            <slot name="footer"></slot>
            
        </div>
    </div>
</template>

<script setup>
    import { ref } from 'vue';

    defineProps({
        cardHeader: {type: Boolean, default: false},
        cardBody: {type: Boolean, default: false},
        cardFooter: {type: Boolean, default: false}
    })
</script>