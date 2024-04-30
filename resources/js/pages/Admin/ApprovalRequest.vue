<template>
    <Breadcrumb title="Admin">
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">User Requests</li>
        </template>
    </Breadcrumb>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <Card :card-body="true">
                        <template #body>
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#uRequestAdd" @click="dtTableRequest.ajax.reload()" role="tab" aria-selected="true">Add Request</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#uRequestRemove" @click="dtTableRemove.ajax.reload()" role="tab" aria-selected="true">Remove Request</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#uRequestEdit" @click="dtTableEdit.ajax.reload()" role="tab" aria-selected="true">Edit Request</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="uRequestAdd" role="tabpanel" aria-labelledby="uRequestAdd-tab">
                                    <br>
                                    <DataTable
                                        class="table table-sm table-bordered table-hover wrap display "
                                        :columns="addColumns"
                                        ajax="api/get_add_request"
                                        ref="table"
                                        :options="options"
                                    />
                                </div>
                                <div class="tab-pane fade" id="uRequestRemove" role="tabpanel" aria-labelledby="uRequestRemove-tab">
                                    <br>
                                    <DataTable
                                        class="table table-sm table-bordered table-hover wrap display "
                                        :columns="removeColumns"
                                        ajax="api/get_remove_request"
                                        ref="tableRemove"
                                        :options="options"
                                    />
                                </div>
                                <div class="tab-pane fade" id="uRequestEdit" role="tabpanel" aria-labelledby="uRequestEdit-tab">
                                    <br>
                                    <DataTable
                                        class="table table-sm table-bordered table-hover wrap display "
                                        :columns="editColumns"
                                        ajax="api/get_edit_request"
                                        ref="tableEdit"
                                        :options="options"
                                    />
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>
    <Modal :title="modalData.title" :backdrop="modalData.backdrop" :modalSize="modalData.size" :styleSize="modalData.styleSize">
        <template #body>
            <div class="row">
                <div class="col-9"  v-if="dtParams.type == 1"> <!-- Add Request Approval -->
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
                <div class="col-9" v-else-if="dtParams.type == 2 || dtParams.type == 3">  <!-- Remove Request Approval -->
                    <Card :card-body="true" :card-header="true">
                        <template #header>
                            <h6></h6>
                        </template>
                        <template #body>
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">PO Date:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.poDate"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">PO Number:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.poNum"
                                                readonly>
                                        </div>
                                        <div class="col-2">
                                            <label for="">Delivery Date:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.delDate"
                                                readonly>
                                        </div>
                                        <div class="col-2">
                                            <label for="">Received Date:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.receiveDate"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">PR Number:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.prNum"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Received Number:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.receiveNum"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Person In-charge:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.pic" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">Code:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.code"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Name:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.name"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Received By:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.receiveBy" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">Supplier:</label>
                                            <!-- <input type="text" class="form-control" v-model="reconData.supplier" readonly> -->
                                            <textarea class="form-control" v-model="reconDetailsById.supplier"
                                                style="resize: none;" readonly></textarea>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Description:</label>
                                            <!-- <input type="text" class="form-control" v-model="reconData.desc" readonly> -->
                                            <textarea class="form-control" v-model="reconDetailsById.desc"
                                                style="resize: none;" readonly></textarea>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Invoice No.:</label>
                                            <textarea class="form-control" v-model="reconDetailsById.invNo"
                                                style="resize: none;" readonly></textarea>
                                            <!-- <input type="text" class="form-control" v-model="reconData.invoiceNum" readonly> -->
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">Unit Price:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.price"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Currency:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.currency"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Classification Code:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.class" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-2">
                                            <label for="">Received Qty:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.qty"
                                                readonly>
                                        </div>
                                        <div class="col-2">
                                            <label for="">UOM:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.uom"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">PO Balance:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.poBal"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Allocation:</label>
                                            <input type="text" class="form-control" v-model="reconDetailsById.alloc" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                <button class="btn btn-success" @click="sendResponse(1)" v-if="dtParams.type == 1 || dtParams.type == 2">Approve</button>
                <button class="btn btn-success" @click="sendResponse(3)" v-if="dtParams.type == 3">Update</button>
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
                <button class="btn btn-success" @click="disapprove()" id="btnModalPromtSubmit">Submit</button>
            </div>
        </template>
    </ModalPrompt>
   
</template>
<style>

</style>
<script setup>
    import { ref, onMounted, reactive, inject, onBeforeMount } from 'vue';
    import api from '../../axios';
 
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
    

    DataTable.use(DataTablesCore);

    let dt;
    let dtTableRemove;
    let dtTableRequest;
    let dtTableEdit;
    const table = ref();
    const tableRemove = ref();
    const tableEdit = ref();
    const tableRequestDetails = ref();
    const modals = ref();
    const modalsPrompt = ref();
    const userRequestRemarks = ref();
    const adminDisRemarks = ref("");
    const Swal = inject('Swal');
    const toastr = inject('toastr');
    const reconDetailsById = ref({});

    const dtParams = reactive({
        'ctrl_number'       : 'null',
        'ctrl_ext'          : 'null',
        'status'            : '',
        'type'              : 1 // 1-Add, 2-remove
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
    const addColumns = [
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
                    dtParams.type = 1;
                    modalData.title = `User Request Details`;
                    modalData.styleSize = 'max-width: 1750px !important; min-width: 1100px;';
                    modals.value.show();
                    dtTableRequest.ajax.reload();
                });
            },
        },
        { data: 'req_status', title: 'Status' },
        { data: 'po_num', title: 'PO Number' },
        { data: 'invoice_no', title: 'Invoice Number' },
        { data: 'control', title: 'Control Number' }
    ];
   
    const options = {
        responsive: true,
        serverSide: true,
        order: [[1, 'desc']]
    }; 

    const removeColumns = [
        {
            data: 'action',
            title: 'Action',
            orderable: false,
            searchable: false,
            createdCell(cell) {
                cell.querySelector('.btnViewRequestRemove').addEventListener('click', function(){
                    let ctrlNo = this.getAttribute('data-ctrl');
                    let ctrlExt = this.getAttribute('data-ctrlExt');
                    let status = this.getAttribute('data-status');
                    dtParams.ctrl_number = ctrlNo;
                    dtParams.ctrl_ext = ctrlExt;
                    dtParams.status = status;
                    dtParams.type = 2;
                    api.get('api/get_requested_recon_details', {params: {param: dtParams} }).then((result) => {
                        let res = result.data.data;
                        reconDetailsById.value.poDate      = res.recon_details.po_date;
                        reconDetailsById.value.poNum       = res.recon_details.po_num;
                        reconDetailsById.value.delDate     = res.recon_details.delivery_date;
                        reconDetailsById.value.receiveDate = res.recon_details.received_date;
                        reconDetailsById.value.prNum       = res.recon_details.pr_num;
                        reconDetailsById.value.receiveNum  = res.recon_details.rcv_no;
                        reconDetailsById.value.pic         = res.recon_details.pic;
                        reconDetailsById.value.code        = res.recon_details.prod_code;
                        reconDetailsById.value.name        = res.recon_details.prod_name;
                        reconDetailsById.value.receiveBy   = res.recon_details.received_by;
                        reconDetailsById.value.supplier    = res.recon_details.supplier;
                        reconDetailsById.value.desc        = res.recon_details.prod_desc;
                        reconDetailsById.value.invNo       = res.recon_details.invoice_no;
                        reconDetailsById.value.price       = res.recon_details.unit_price;
                        reconDetailsById.value.currency    = res.recon_details.currency;
                        reconDetailsById.value.class       = res.recon_details.classification;
                        reconDetailsById.value.qty         = res.recon_details.received_qty;
                        reconDetailsById.value.uom         = res.recon_details.uom;
                        reconDetailsById.value.poBal       = res.recon_details.po_balance;
                        reconDetailsById.value.alloc       = res.recon_details.allocation;

                        userRequestRemarks.value = res.recon_remarks.remarks;
                    }).catch((err) => {
                        
                    });
                    modalData.title = `User Request Details`;
                    modalData.styleSize = 'max-width: 1750px !important; min-width: 1100px;';

                    modals.value.show();
                });
            },
        },
        { data: 'status', title: 'Status' },
        { data: 'recon_details.po_num', title: 'PO Number' },
        { data: 'recon_details.invoice_no', title: 'Invoice Number' },
        { data: 'control', title: 'Control Number' }
    ];
    const editColumns = [
        {
            data: 'action',
            title: 'Action',
            orderable: false,
            searchable: false,
            createdCell(cell) {
                cell.querySelector('.btnViewDetails').addEventListener('click', function(){
                    let ctrlNo = this.getAttribute('data-ctrl');
                    let ctrlExt = this.getAttribute('data-ctrlExt');
                    let status = this.getAttribute('data-status');
                    dtParams.ctrl_number = ctrlNo;
                    dtParams.ctrl_ext = ctrlExt;
                    dtParams.status = status;
                    dtParams.type = 3;
                    api.get('api/get_requested_recon_details', {params: {param: dtParams} }).then((result) => {
                        let res = result.data.data;

                        reconDetailsById.value.reqId          = result.data.reqId;
                        reconDetailsById.value.poDate      = res.recon_details.po_date;
                        reconDetailsById.value.poNum       = res.recon_details.po_num;
                        reconDetailsById.value.delDate     = res.recon_details.delivery_date;
                        reconDetailsById.value.receiveDate = res.recon_details.received_date;
                        reconDetailsById.value.prNum       = res.recon_details.pr_num;
                        reconDetailsById.value.receiveNum  = res.recon_details.rcv_no;
                        reconDetailsById.value.pic         = res.recon_details.pic;
                        reconDetailsById.value.code        = res.recon_details.prod_code;
                        reconDetailsById.value.name        = res.recon_details.prod_name;
                        reconDetailsById.value.receiveBy   = res.recon_details.received_by;
                        reconDetailsById.value.supplier    = res.recon_details.supplier;
                        reconDetailsById.value.desc        = res.recon_details.prod_desc;
                        reconDetailsById.value.invNo       = res.recon_details.invoice_no;
                        reconDetailsById.value.price       = res.recon_details.unit_price;
                        reconDetailsById.value.currency    = res.recon_details.currency;
                        reconDetailsById.value.class       = res.recon_details.classification;
                        reconDetailsById.value.qty         = res.recon_details.received_qty;
                        reconDetailsById.value.uom         = res.recon_details.uom;
                        reconDetailsById.value.poBal       = res.recon_details.po_balance;
                        reconDetailsById.value.alloc       = res.recon_details.allocation;

                        userRequestRemarks.value = res.recon_remarks.remarks;
                    }).catch((err) => {
                        
                    });
                    modalData.title = `User Request Details`;
                    modalData.styleSize = 'max-width: 1750px !important; min-width: 1100px;';

                    modals.value.show();
                });
            },
        },
        { data: 'status', title: 'Status' },
        { data: 'recon_details.po_num', title: 'PO Number' },
        { data: 'recon_details.invoice_no', title: 'Invoice Number' },
        { data: 'control', title: 'Control Number' }

    ];

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

    let injectSess;
    onBeforeMount( async() => {
        console.log('before mount');
        injectSess = inject('store');
    });
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
        dtTableRemove = tableRemove.value.dt;
        dtTableEdit = tableEdit.value.dt;
    });

    const sendResponse = async (adminResponse) => {
        if(injectSess.isAuth == 0){
            Swal.fire({
                title: "Error",
                text: "You are not authorized!",
                icon: "error"
            });
            return false;
        }
        // * adminResponse = 1-approve, 2-disapprove, 3-For update
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
                        // if(result.data.result == 1){
                        dt.ajax.reload();
                        dtTableRemove.ajax.reload();
                        // }
                        
                    }).catch((err) => {
                        
                    });
                }
            });
        }
        else if(adminResponse == 2){
            modalPromptData.title = `Disapprove Request`
            modalPromptData.function = 2;
            modalsPrompt.value.show();
        }
        else if(adminResponse == 3){ // EDIT
            // console.log(reconDetailsById.value.reqId);
            api.post('api/update_request_data', {req_id: reconDetailsById.value.reqId}).then((result) => {
                // console.log(result);
                if(result.data.result == true){
                    toastr.success(result.data.msg);
                    modals.value.hide();
                    dtTableEdit.ajax.reload()
                }
                
            }).catch((err) => {
                if(err.response.status == 422){
                    toastr.error(err.response.data.msg);
                }
            });
        }
    }

    const disapprove = async () => {
        if(adminDisRemarks.value == ""){
            toastr.error('Please fill up required fields');
            toastr.error('test');
            document.getElementById('txtDisRemarks').classList.add('is-invalid');
        }
        else{
            document.getElementById('txtDisRemarks').classList.remove('is-invalid');

            await api.post('api/response_request', {dtParams, adminDisRemarks:  adminDisRemarks.value } ).then((result) => {
                toastr.success(`${result.data.msg}`);
                modalsPrompt.value.hide();
                modals.value.hide();
                dtTableRemove.ajax.reload();
            }).catch((err) => {
                
            });
        }
       
    }
</script>