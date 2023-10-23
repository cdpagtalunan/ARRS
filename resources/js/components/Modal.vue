<template>
    <div class="modal fade" id="exampleModal" tabindex="-1" :data-bs-backdrop="backdrop" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <!-- <form v-if="form" method="post" @click.prevent="$emit('someEvent')"> -->
            <form @submit.prevent="$emit('saveEvent')" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ title }}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <slot name="body" />
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <slot name="footerButton"></slot>

                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
    import { onMounted, ref } from "vue";
    import { Modal } from "bootstrap";

    defineProps({
        title: {
            type: String,
            default: "<<Title goes here>>",
        },
        backdrop: {
            type: Boolean,
            default: true,
        },
        // formRef: {
        //     type: String,
        //     default: "",
        // }
    });
</script>