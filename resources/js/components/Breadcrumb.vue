<template>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1>{{ title }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <slot name="breadcrumbs"></slot>
                    </ol>
                </div>
            </div>
        </div>
    </section>
</template>
<script setup>
    import { ref } from 'vue';

    defineProps({
        title: {type: String, default: ''}
    })
</script>