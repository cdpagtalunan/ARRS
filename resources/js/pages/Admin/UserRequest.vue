<template>
    <Breadcrumb title="Admin">
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">User</li>
        </template>
    </Breadcrumb>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <Card :card-body="true">
                        <template #body>
                            <DataTable
                                class="table table-sm table-bordered table-hover wrap display"
                                :columns="columns"
                                ajax="api/get_add_request"
                                ref="table"
                                :options="options"
                            />
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>
    <Modal :title="modalData.title" :backdrop="modalData.backdrop" :modalSize="modalData.size" :styleSize="modalData.styleSize">
        <template #body>
            <div class="row">
                <div class="col-9">
                    <Card :card-body="true" :card-header="true" class="overflow-auto">
                        <template #header>
                            User Request
                        </template>
                        <template #body>
                            <DataTable
                                class="table table-sm table-bordered table-hover wrap display"
                                :columns="requestDetailsColumn"
                                :ajax="{
                                    url: 'api/view_request_details',
                                    data: function (param){
                                        param.param = dtParams;
                                    }
                                }"
                                ref="tableRequestDetails"
                                :options="requestDetailsOptions"
                            />
                        </template>
                    </Card>
                </div>
                <div class="col-3">
                    <Card :card-body="true" :card-header="true">
                        <template #header>
                            User Remarks
                        </template>
                        <template #body>
                            <textarea class="form-control" cols="30" rows="10" readonly v-model="userRequestRemarks"></textarea>
                        </template>
                    </Card>
                </div>
            </div>
        </template>
        <template #footerButton>
            <div v-if="dtParams.status == 0">
                <button class="btn btn-danger mr-1" @click="sendResponse(2)">Disapprove</button>
                <button class="btn btn-success" @click="sendResponse(1)">Approve</button>
            </div>
        </template>
    </Modal>

    <ModalPrompt :title="modalPromptData.title" :backdrop="modalPromptData.backdrop">
        <template #body>
           <div v-if="modalPromptData.function == 2" class="text-center">
                <h6>
                    Are you sure you want to disapprove this request?
                    <br>
                    Please fill-up remarks.
                </h6>
                <textarea class="form-control" rows="5" placeholder="Remarks" v-model="adminDisRemarks" id="txtDisRemarks"></textarea>
           </div>
        </template>
        <template #footerButton>
            <div v-if="modalPromptData.function == 2" class="text-center">
                <button class="btn btn-success" @click="disapprove()">Submit</button>
            </div>
        </template>
    </ModalPrompt>
   
</template>
<style>

</style>
<script setup>
    import { ref, onMounted, reactive, inject } from 'vue';
    import api from '../../axios';
 
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
import { ToastSeverity } from 'primevue/api';
    

    DataTable.use(DataTablesCore);

    let dt;
    let dtTableRequest;
    const table = ref();
    const tableRequestDetails = ref();
    const modals = ref();
    const modalsPrompt = ref();
    const userRequestRemarks = ref();
    const adminDisRemarks = ref("");
    const Swal = inject('Swal');
    const toastr = inject('toastr');
    
    const dtParams = reactive({
        'ctrl_number'       : 'null',
        'ctrl_ext'          : 'null',
        'status'            : ''
    })
    const modalDataInitialState = {
        title : "",
        backdrop: "true",
        styleSize: "",
        size: ""
    }
    const modalData = reactive({...modalDataInitialState});
    
    const modalPromptInitialData = {
        title: "",
        backdrop: "true",
        styleSize: "",
        size: "",
        function: 0,
    }
    const modalPromptData = reactive({...modalPromptInitialData});
    const columns = [
        {
            data: 'action',
            title: 'Action',
            orderable: false,
            searchable: false,
            createdCell(cell) {
                cell.querySelector('.btnViewReconRequest').addEventListener('click', function(){
                    let ctrlNo = this.getAttribute('data-ctrl');
                    let ctrlExt = this.getAttribute('data-ctrlExt');
                    let status = this.getAttribute('data-status');
                    dtParams.ctrl_number = ctrlNo;
                    dtParams.ctrl_ext = ctrlExt;
                    dtParams.status = status;
                    modalData.title = `User Request Details`;
                    modalData.styleSize = 'max-width: 1750px !important; min-width: 1100px;';
                    modals.value.show();
                    dtTableRequest.ajax.reload();
                });
            },
        },
        { data: 'req_status', title: 'Status' },
        { data: 'control', title: 'Control Number' },
        { data: 'po_num', title: 'PO Number' }
    ];
    const options = {
        responsive: true,
        serverSide: true
    };

    const requestDetailsColumn = [
        { data: 'po_num', title: 'PO Number' },
        { data: 'pr_num', title: 'PR Number' },
        { data: 'invoice_no', title: 'Invoice Number' },
        { data: 'rcv_no', title: 'Receiving Number' },
        { data: 'prod_code', title: 'Code' },
        { data: 'prod_name', title: 'Name' },
        { data: 'prod_desc', title: 'Description' },
        { data: 'supplier', title: 'Supplier' },
        { data: 'unit_price', title: 'Price' },
        { data: 'uom', title: 'UOM' },
        { data: 'currency', title: 'Currency' },
        { data: 'classification', title: 'Classification' },
    ];
    const requestDetailsOptions = {
        responsive: true,
        serverSide: true,
        searching: false,
        ordering:  false,
        info: false,
        'drawCallback': function( settings ) {
            let dtApi = this.api();
            let dtDatas = dtApi.rows( {page:'current'} ).data();
            // userRequestRemarks.value.dtDatas[0]
            if(dtDatas.length > 0){
                userRequestRemarks.value = dtDatas[0]['recon_remarks']['remarks'];
            }
            // console.log(dtDatas[0]['recon_remarks']);
        }
    };
    onMounted(() => {

        modals.value = new Modal(document.querySelector('#modalComponentId'), {});
        document.getElementById("modalComponentId").addEventListener('hidden.bs.modal', event => {
            console.log('modal is closed');
            Object.assign(modalData, modalDataInitialState); // * assign default value for MODAL
        });

        modalsPrompt.value = new Modal(document.querySelector('#modalPromptComponent'), {});
        document.getElementById("modalPromptComponent").addEventListener('hidden.bs.modal', event => {
            console.log('modalPromptComponent is closed');
            adminDisRemarks.value = "";
            Object.assign(modalPromptData, modalPromptInitialData); // * assign default value for MODAL

            
        });
        dt = table.value.dt;
        dtTableRequest = tableRequestDetails.value.dt;
    });

    const sendResponse = async (adminResponse) => { // adminResponse = 1-approve, 2-disapprove
        if(adminResponse == 1){
            await Swal.fire({
                title: `Are you sure you want to approve this request?`,
                // text: "You won't be able to revert this!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    api.post('api/response_request', {dtParams, adminDisRemarks:  adminDisRemarks.value }).then((result) => {
                        toastr.success(`${result.data.msg}`);
                        modals.value.hide();
                        dt.ajax.reload();
                    }).catch((err) => {
                        
                    });
                }
            });
        }
        else{
            modalPromptData.title = `Disapprove Request`
            modalPromptData.function = 2;
            modalsPrompt.value.show();
        }
    }

    const disapprove = async () => {
        if(adminDisRemarks.value == ""){
            toastr.error('Please fill up required field');
            document.getElementById('txtDisRemarks').classList.add('is-invalid');
        }
        else{
            document.getElementById('txtDisRemarks').classList.remove('is-invalid');

            await api.post('api/response_request', {dtParams, adminDisRemarks:  adminDisRemarks.value } ).then((result) => {
                toastr.success(`${result.data.msg}`);
                modalsPrompt.value.hide();
                modals.value.hide();
                dt.ajax.reload();
            }).catch((err) => {
                
            });
        }
       
    }
</script>