<template>
    <div class="modal fade" id="modalComponentId" tabindex="-1" :data-bs-backdrop="backdrop" aria-labelledby="modalComponentIdLabel" aria-hidden="true">
        <div class="modal-dialog" :class="modalSize" :style="styleSize">
            <!-- <form v-if="form" method="post" @click.prevent="$emit('someEvent')"> -->
            <form @submit.prevent="$emit('saveEvent')" method="post" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalComponentIdLabel">{{ title }}</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <slot name="body" />
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" id="close" data-bs-dismiss="modal">
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

    defineProps({
        title: {
            type: String,
            default: "<<Title goes here>>",
        },
        backdrop: {
            type: String,
            default: '',
        },
        modalSize: {
            type: String,
            default: '',
        },
        styleSize: {
            type: String,
            default: ''
        }
    });
</script>