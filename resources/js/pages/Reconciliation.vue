<template>
    <Breadcrumb >
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Reconciliation</li>
        </template>
    </Breadcrumb>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <Card :card-body="true" :card-header="true">
                        <template #header>
                            <div class="row justify-content-between">
                                <div class="col-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend" width="50px">
                                            <span class="input-group-text w-100" id="basic-addon1" style="background-color: #3154b6; color: white;">Select Cut-off</span>
                                        </div>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-sm btn-info" @click="loadDataEPRPO(1)">Load 1st cutoff</button>
                                    <button type="button" class="btn btn-sm btn-info" @click="loadDataEPRPO(2)">Load 2nd cutoff</button>

                                    <strong>Status:</strong>
                                </div>
                            </div>
                        </template>
                        <template #body>
                            <ul class="nav nav-tabs">
                                <li class="nav-item" v-for="cutOffOption in cutOffOptions" :key="cutOffOption.id">
                                    <a class="nav-link" data-bs-toggle="tab" href="#reconDataTable" role="tab" aria-selected="true" @click="loadDataTable(cutOffOption.classification,cutOffOption.department)">{{ `${cutOffOption.classification}-${cutOffOption.department}` }}</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade" id="reconDataTable" role="tabpanel" aria-labelledby="reconDataTable-tab">
                                    <div class="mt-3">
                                        <DataTable
                                            class="table table-sm table-bordered table-hover wrap display"
                                            :columns="columns"
                                            :ajax="{
                                                url: 'api/get_recon',
                                                data: function (param){
                                                    param.param = dtParams;
                                                }
                                            }"
                                            ref="table"
                                            :options="options"
                                        />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>
    <Modal title="test" backdrop="true">
    </Modal>

</template>

<script setup>
    import { ref, reactive, inject, onBeforeMount, onMounted } from 'vue';
    import api from '../axios';
    
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
    DataTable.use(DataTablesCore);

    const cutOffOptions = ref();
    const columns = [
        {
            data: 'action',
            title: 'Action',
            orderable: false,
            searchable: false,
            createdCell(cell) {
                cell.querySelector('.btnOpenReconDetails').addEventListener('click', function(){
                    let id = this.getAttribute('data-id');
                    getReconDetails(id);
                });
            },
        },
        { data: 'po_num', title: 'PO Number'},
        { data: 'pr_num', title: 'PR Number'},
        { data: 'prod_code', title: 'Code'},
        { data: 'prod_name', title: 'Name'},
        { data: 'prod_desc', title: 'Description'},
        { data: 'supplier', title: 'Supplier'},
        { data: 'received_date', title: 'Received Date'},
        { data: 'classification', title: 'Classification'},
        
    ];
    const options = {
        responsive: true,
        serverSide: true
    };
    let dt;
    const table = ref();
    const dtParams = reactive({
        'classification'    : 'null',
        'department'        : 'null'
    });
    let modals
// import { Modal } from 'bootstrap';

    onMounted(() => {
        dt = table.value.dt;
        modals = new Modal(document.querySelector('#modalComponentId'));

    });

    onBeforeMount(async () => {
        let injectSess = inject('store');

        await api.get('api/get_category_of_user', {params: {access: injectSess.access}} ).then((result) => {
            cutOffOptions.value = result.data.uAccess;
            // console.log(result.data.uAccess);
        }).catch((err) => {
            
        });
    });

    const loadDataEPRPO = async (date) => {
        await api.get('api/get_eprpo_data', { params: {cutoff:date} }).then((result) => {
            
        }).catch((err) => {
            
        });
    }
    const loadDataTable = async (classification, department) => {
        // console.log(id);
        // dtParams.value = id;
        dtParams.classification = classification;
        dtParams.department = department;
        dt.ajax.reload();
    }
    const getReconDetails = async (id) => {
        api.get('api/get_recon_details', { params:{ recon:id }}).then((result) => {
            modals.show();
        }).catch((err) => {
            
        });
    }
    
</script>