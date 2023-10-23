<template>
    <Breadcrumb title="Settings">
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Cut-off</li>
        </template>
    </Breadcrumb>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <Card :card-body="true">
                        <template #body>
                            <button type="button" class="btn btn-success float-right" @click="openModal('Add Cut-off')" data-bs-toggle="modal" data-bs-target="#exampleModal"><icons icon="fas fa-plus"></icons> Add Cut-off</button><br><br>
                            <!-- <button type="button" class="btn btn-success float-right" data-bs-toggle="modal" data-bs-target="#exampleModal"><icons icon="fas fa-plus"></icons> Add Cut-off</button><br><br> -->
                            <PrimeVueDatatable 
                            :values="dataTableProps.values" 
                            :filters="dataTableProps.filters" 
                            :globalFilter="dataTableProps.globalFilter" 
                            :loading="dataTableProps.loading">
                                <template #columns>
                                    <Column field="id">
                                        <template #header>
                                            Action
                                        </template>
                                        <template #body="{ data, field }">
                                            <label class="w-100 text-center">
                                                <Button @click="test(data[field])" class="btn btn-info btn-sm" style="margin-right: .5em"><icons icon="fas fa-edit" class="fa-sm"></icons></Button>
                                                <Button class="btn btn-danger btn-sm"><icons icon="fas fa-trash" class="fa-sm"></icons></Button>
                                            </label>
                                        </template>
                                    </Column>
                                    <Column field="id" header="ID" sortable></Column>
                                    <Column field="fullname" header="Name" sortable></Column>
                                </template>
                            </PrimeVueDatatable>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
        <Modal :title="modalDetails.title" @save-event="saveCutoff">
            <template #body>
                <!-- {{ formCutoff }} -->
                <input type="hidden" v-model="formCutoff.id">
                <div class="input-group mb-3">
                    <div class="input-group-prepend w-50">
                        <span class="input-group-text w-100" id="basic-addon1" style="background-color: #3154b6; color: white;">Cut-off</span>
                    </div>
                    <select v-model="formCutoff.cutoff" class="form-control">
                        <option value="1">1st Reconciliation</option>
                        <option value="2">2nd Reconciliation</option>
                    </select>

                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend w-50">
                        <span class="input-group-text w-100" id="basic-addon1" style="background-color: #3154b6; color: white;">From</span>
                    </div>
                    <input type="number" min="1" max="31" v-model="formCutoff.froms" @input="() => { if(formCutoff.froms > 31 || formCutoff.froms < 0) { formCutoff.froms = 31 }}" class="form-control">

                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend w-50">
                        <span class="input-group-text w-100" id="basic-addon1" style="background-color: #3154b6; color: white;">To</span>
                    </div>
                    <input type="number" min="1" max="31" v-model="formCutoff.to" @input="() => { if(formCutoff.to > 31 || formCutoff.to < 0) { formCutoff.to = 31 }}" class="form-control">
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend w-50">
                        <span class="input-group-text w-100" id="basic-addon1" style="background-color: #3154b6; color: white;">Send email on</span>
                    </div>
                    <input type="number" min="1" max="31" v-model="formCutoff.dateEmail" class="form-control">
                </div>
            </template>

            <template #footerButton>
                <button type="button" class="btn btn-success" @click="saveCutoff">Save</button>
            </template>
        </Modal>

    </section>
</template>
<!-- <style>
.p-datatable-loading-overlay{
    background-color: transparent !important;
}
</style> -->
<script setup>
    import { ref, onMounted, reactive, markRaw } from 'vue';
    import Breadcrumb from '../../components/Breadcrumb.vue';
    import Card from '../../components/Card.vue';
    import axios from 'axios';
    import { FilterMatchMode } from 'primevue/api'; // * This is for datatable search
    import PrimeVueDatatable from '../../components/PrimeVueDatatable.vue';
    import Modal from '../../components/Modal.vue';

    const modalDetails = reactive({
        title : '',
    });
    let formCutoff = reactive({
    });

    onMounted(() => {
        axios.get('api/get_cutoff').then((res)=>{
            dataTableProps.values = res.data.data;
            dataTableProps.loading = false;
        });
    });
    const dataTableProps = reactive({
        values : [],
        filters: {
            global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            id: { value: null, matchMode: FilterMatchMode.IN },
            fullname: { value: null, matchMode: FilterMatchMode.IN },
        },
        globalFilter: ['id','fullname'],
        loading: true
    })
    const openModal = (title) => {
        // thisModal.value.show();
        modalDetails.title = title;
    }
   
    const saveCutoff = async () => {
        formCutoff.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        await axios.post('api/save_cutoff', formCutoff).then((res) => {

            console.log(res);
        }).catch((err) => {
            
        });
    }
</script>