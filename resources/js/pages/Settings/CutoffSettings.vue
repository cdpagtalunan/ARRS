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
                            <button type="button" class="btn btn-success float-right" @click="openModal('Add Cut-off')" data-bs-toggle="modal" data-bs-target="#modalComponentId"><icons icon="fas fa-plus"></icons> Add Cut-off</button><br><br>
                            <!-- <button type="button" class="btn btn-success float-right" data-bs-toggle="modal" data-bs-target="#exampleModal"><icons icon="fas fa-plus"></icons> Add Cut-off</button><br><br> -->
                            <PrimeVueDatatable 
                            :values="dataTableProps.values" 
                            :filters="dataTableProps.filters" 
                            :globalFilter="dataTableProps.globalFilter" 
                            :loading="dataTableProps.loading">
                                <template #columns>
                                    <Column field="id" class="border border-2">
                                        <template #header>
                                            Action
                                        </template>
                                        <template #body="{ data, field }">
                                            <div class="d-flex justify-content-center">
                                                <Button @click="getCutoffDetails(data[field])" class="btn btn-info btn-sm" style="margin-right: .5em"><icons icon="fas fa-edit" class="fa-sm"></icons></Button>

                                                <Button @click="updateCuffoffStat(data[field], 0)" class="btn btn-danger btn-sm" v-if="data['deleted_at'] == null"><icons icon="fas fa-trash" class="fa-sm"></icons></Button>
                                                <Button @click="updateCuffoffStat(data[field], 1)" class="btn btn-success btn-sm" v-else><icons icon="fas fa-arrow-rotate-right" class="fa-sm"></icons></Button>
                                            </div>
                                        </template>
                                    </Column>
                                    <Column class="border border-2">
                                        <template #header>
                                            Status
                                        </template>
                                        <template #body="{ data }">
                                            <label class="w-100 text-center">
                                                <!-- {{ data }} -->
                                                <span class="badge rounded-pill text-bg-success" v-if="data['deleted_at'] == null">Active</span>
                                                <span class="badge rounded-pill text-bg-danger" v-else>Deactivated</span>
                                            </label>
                                        </template>
                                    </Column>

                                    <Column class="border border-2">
                                        <template #header>
                                            Cut-off
                                        </template>
                                        <template #body="{ data }">
                                            <label class="w-100 text-center">

                                                {{ data['cut_off'] }}
                                            </label>
                                        </template>
                                    </Column>

                                    <Column class="border border-2">
                                        <template #header>
                                            Cut-off day range
                                        </template>
                                        <template #body="{ data }">
                                            <label class="w-100 text-center">
                                                {{ `${data['day_from']}-${data['day_to']}` }}
                                            </label>
                                        </template>
                                    </Column>
                                    <Column class="border border-2">
                                        <template #header>
                                            Email day
                                        </template>
                                        <template #body="{ data }">
                                            <label class="w-100 text-center">
                                                {{ data['day_email'] }}
                                            </label>
                                        </template>
                                    </Column>
                                    <!-- <Column field="day_from, day_to" header="Name" class="border border-2" sortable></Column> -->
                                </template>
                            </PrimeVueDatatable>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
        <Modal :title="modalDetails.title" backdrop="static"  @save-event="saveCutoff">
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
                    <input type="number" min="1" max="31" v-model="formCutoff.dateEmail" @input="() => { if(formCutoff.dateEmail > 31 || formCutoff.dateEmail < 0) { formCutoff.dateEmail = 31 }}" class="form-control">
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
    import { ref, onMounted, reactive, inject } from 'vue';
    // import Breadcrumb from '../../components/Breadcrumb.vue';
    // import Card from '../../components/Card.vue';
    // import axios from 'axios';
    import { FilterMatchMode } from 'primevue/api'; // * This is for datatable search
    import api from '../../axios';
    // import PrimeVueDatatable from '../../components/PrimeVueDatatable.vue';
    // import Modal from '../../components/Modal.vue';

    const modalDetails = reactive({
        title : '',
    });
    const formCutoff = ref({});
    const toastr = inject('toastr');
    const Swal = inject('Swal');
    let modals
    onMounted(() => {
        modals = new Modal(document.querySelector('#modalComponentId'));
        document.getElementById("modalComponentId").addEventListener('hidden.bs.modal', event => {
            formCutoff.value = {};
        });
        getCutoffData();
    });
    const dataTableProps = reactive({
        values : [],
        // filters: {
        //     global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        //     froms: { value: null, matchMode: FilterMatchMode.IN },
        //     // to: { value: null, matchMode: FilterMatchMode.IN },
        // },
        // globalFilter: ['id','fullname'],
        loading: true
    })
    const openModal = (title) => {
        // thisModal.value.show();
        modalDetails.title = title;
    }
    const getCutoffData = async () => {
        await api.get('api/get_cutoff').then((res)=>{
            dataTableProps.values = res.data.data;
            dataTableProps.loading = false;
        });
    }
    const saveCutoff = async () => {
        await api.post('api/save_cutoff', formCutoff.value).then((res) => {
            toastr.success(res.data.msg);
            formCutoff.value = {};
            modals.hide();
            getCutoffData();
        }).catch((err) => {
            toastr.error(err.response.data.msg);
        });
    }

    const getCutoffDetails = async (id) => {
        await api.get('api/get_cutoff_details', { params: { id: id } }).then((result) => {
            modals.show();
            formCutoff.value.id = result.data.cutoffDetails.id;
            formCutoff.value.cutoff = result.data.cutoffDetails.cut_off;
            formCutoff.value.froms  = result.data.cutoffDetails.day_from;
            formCutoff.value.to = result.data.cutoffDetails.day_to;
            formCutoff.value.dateEmail  = result.data.cutoffDetails.day_email;
        }).catch((err) => {
            // toastr.error(err.response.data.msg);
        });
    }

    const updateCuffoffStat = (id, func) => {
        let title;
        if(func == 0){
            title = "Deactivate";
        }else{
            title = "activate";
        }
        Swal.fire({
            title: `Are you sure you want to ${title} this?`,
            // text: "You won't be able to revert this!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                api.post('api/update_status', {id:id, func:func}).then((result) => {
                    getCutoffData();
                    toastr.success(result.data.msg);
                }).catch((err) => {
                    
                });
            }
        })
       
    }
</script>