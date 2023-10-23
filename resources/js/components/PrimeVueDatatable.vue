<template>
   <DataTable :value="values" tableStyle="min-width: 50rem" showGridlines paginator stripedRows
    v-model:rows="selected" class="p-datatable-sm" v-model:filters="filters" :globalFilterFields="globalFilter"
    removableSort :loading="loading" >
        <template #header>
            <div class="d-flex justify-content-between">
                <div>
                    <label class="d-flex flex-row mt-2"> Show 
                        <select class="custom-select custom-select-sm form-control form-control-sm ml-1 mr-1" v-model.number="selected">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select> entries</label>
                </div>
                <div>
                    <span class="p-input-icon-left">
                        <icons icon="fas fa-magnifying-glass"></icons>
                        <InputText size="small" v-model="filters['global'].value" placeholder="Keyword Search" />
                    </span>
                </div>
                
            </div>
        </template> 
        <template #empty><label class="w-100 text-center"> No data available.</label></template>
        <template #loading>
                <ProgressSpinner style="width: 120px; height: 120px" strokeWidth="5" 
                    animationDuration=".5s" fill="var(--surface-ground)"/>
        </template>
        <slot name="columns"></slot>
    </DataTable>
</template>
<style>
.p-datatable-loading-overlay{
    background-color: transparent !important;
}
</style>
<script setup>
    import { ref } from 'vue';
    import { FilterMatchMode } from 'primevue/api';

    defineProps({
        values: {type:Array, default: null},
        filters: {type: Object, default:null},
        globalFilter: {type: Array, default: null},
        loading: {type: Boolean, default: false}
    });

    const selected = ref(10);
</script>