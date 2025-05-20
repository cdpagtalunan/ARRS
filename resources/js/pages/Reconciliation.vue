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
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend" width="50px">
                                            <span class="input-group-text w-100" id="basic-addon1" style="background-color: #3154b6; color: white;">Select Cut-off</span>
                                        </div>
                                        <select class="form-control w-25" v-model="cutoffSelect.selected" @change="()=>{ dt.ajax.reload() }">
                                            <option v-for="cutOffSelOption in cutoffSelect.option">{{ cutOffSelOption }}</option>
                                        </select>
                                        <button class="btn btn-secondary" type="button" id="button-addon2" @click="reconExport()"><icons icon="fas fa-file-excel"></icons></button>
                                    </div>
                                </div>
                                <!-- <div class="col-3 d-flex flex-row justify-content-between align-items-center"> -->
                                <div class="col-sm-4">
                                    <!-- <strong>Status: </strong>
                                    <strong :class="{
                                            'text-success': catStatus.status,
                                            'text-danger': !catStatus.status
                                    }">{{ catStatus.label }}</strong> -->
                                    <!-- <strong>Status: {{ catStatus }}</strong> -->
                                    <router-link :to="{ name: 'UserRequest' }" class="float-end">
                                        <button class="btn btn-primary btn-sm"><icons icon="fas fa-clipboard-list"></icons> See Request List</button>
                                    </router-link>   
                                </div>
                            </div>
                        </template>
                        <template #body>
                            <ul class="nav nav-tabs justify-content-center">
                                <div class="col-sm-4 mb-1">
                                    <select class="form-control" @change="onChange($event)">
                                        <option selected disabled>-- Select --</option>
                                        <option v-for="cutOffOption in userAccesses" :key="cutOffOption.id" :classification="cutOffOption.classification" :department="cutOffOption.department">
                                            {{ `${cutOffOption.classification}-${cutOffOption.department}` }}
                                        </option>
                                    </select>
                                </div>
                                
                                <!-- <li class="nav-item" v-for="cutOffOption in userAccesses" :key="cutOffOption.id">
                                    <a class="nav-link" data-bs-toggle="tab" href="#reconDataTable" role="tab" aria-selected="true" @click="loadDataTable(cutOffOption.classification,cutOffOption.department)">{{ `${cutOffOption.classification}-${cutOffOption.department}` }}</a>
                                </li> -->
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane show active fade" id="reconDataTable" role="tabpanel" aria-labelledby="reconDataTable-tab">
                                    <div class="mt-3 overflow-x-auto">
                                        <div class="row">
                                            <div class="col-sm-12 d-flex align-items-center justify-content-between">
                                            <div>
                                                <strong>Status: </strong>
                                            <strong :class="{
                                                    'text-success': catStatus.status,
                                                    'text-danger': !catStatus.status
                                            }">{{ catStatus.label }}</strong>
                                            </div>
                                            
                                            <button type="button" class="btn btn-primary float-end" id="btnRequestAddRecon" @click="btnAddRecon(dtParams)"><icons icon="fas fa-plus"></icons> Add Recon</button>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <DataTable
                                                    class="table table-sm table-bordered table-hover text-wrap display tableRecon"
                                                    :columns="columns"
                                                    :ajax="{
                                                        url: 'api/get_recon',
                                                        data: function (param){
                                                            param.param = dtParams;
                                                            param.cutoff_date = cutoffSelect.selected;
                                                            param.access =  injectSess.access;
                                                            param.user_type =  injectSess.type;
                                                        }
                                                    }"
                                                    ref="table"
                                                    :options="options"
                                                />
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>
    <Modal :title="modalData.title" :backdrop="modalData.backdrop" :modal-size="modalData.size" :style-size="modalData.styleSize" style="overflow: auto;">
        <!-- 
            * BODY
        -->
        <template #body v-if="modalData.viewing == 1 || modalData.viewing == 2 || modalData.viewing == 3"> <!--1-viewing, 2-add recon data, 3-update -->
            <div class="row">
                <div class="col-sm-12">
                    <Card :card-body="true" :card-header="true">
                        <template #header>
                            <h6>LOGISTICS-PURCHASING DATA (Extracted from EPRPO-Receiving Module)</h6>
                        </template>
                        <template #body>
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">PO Date:</label>
                                            <input type="text" class="form-control" v-model="reconData.poDate"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">PO Number:</label>
                                            <input type="text" class="form-control" v-model="reconData.poNum"
                                                readonly>
                                        </div>
                                        <div class="col-2">
                                            <label for="">Delivery Date:</label>
                                            <input type="text" class="form-control" v-model="reconData.delDate"
                                                readonly>
                                        </div>
                                        <div class="col-2">
                                            <label for="">Received Date:</label>
                                            <input type="text" class="form-control" v-model="reconData.receivedDate"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">PR Number:</label>
                                            <input type="text" class="form-control" v-model="reconData.prNum"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Received Number:</label>
                                            <input type="text" class="form-control" v-model="reconData.receivedNum"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Person In-charge:</label>
                                            <input type="text" class="form-control" v-model="reconData.pic" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">Code:</label>
                                            <input type="text" class="form-control" v-model="reconData.code"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Name:</label>
                                            <input type="text" class="form-control" v-model="reconData.name"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Received By:</label>
                                            <input type="text" class="form-control" v-model="reconData.receivedBy" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">Supplier:</label>
                                            <!-- <input type="text" class="form-control" v-model="reconData.supplier" readonly> -->
                                            <textarea class="form-control" v-model="reconData.supplier"
                                                style="resize: none;" readonly></textarea>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Description:</label>
                                            <!-- <input type="text" class="form-control" v-model="reconData.desc" readonly> -->
                                            <textarea class="form-control" v-model="reconData.desc"
                                                style="resize: none;" readonly></textarea>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Invoice No.:</label>
                                            <textarea class="form-control" v-model="reconData.invoiceNum"
                                                style="resize: none;" readonly></textarea>
                                            <!-- <input type="text" class="form-control" v-model="reconData.invoiceNum" readonly> -->
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">Unit Price:</label>
                                            <input type="text" class="form-control" v-model="reconData.uPrice"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Currency:</label>
                                            <input type="text" class="form-control" v-model="reconData.currency"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Classification Code:</label>
                                            <input type="text" class="form-control" v-model="reconData.class" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-2">
                                            <label for="">Received Qty:</label>
                                            <input type="text" class="form-control" v-model="reconData.receivedQty"
                                                readonly>
                                        </div>
                                        <div class="col-2">
                                            <label for="">UOM:</label>
                                            <input type="text" class="form-control" v-model="reconData.uom"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">PO Balance:</label>
                                            <input type="text" class="form-control" v-model="reconData.poBal"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Allocation:</label>
                                            <input type="text" class="form-control" v-model="reconData.alloc" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
                <!-- <div class="col-4">
                    <Card :card-body="true" :card-header="true">
                        <template #header>
                            <h6>Reconciliation by User</h6>
                        </template>
                        <template #body>
                            <form autocomplete="off">
                                <input type="hidden" v-model="uReconData.recon">
                                <label for="">Invoice Number:</label>
                                <input type="text" class="form-control" id="txtInvoiceNum" v-model="uReconData.invoiceNum" :readonly="modalData.viewing == 1">

                                <label for="">Received Quantity:</label>
                                <input type="number" class="form-control" id="txtReceivedQty" v-model="uReconData.receivedQty" :readonly="modalData.viewing == 1">

                                <label for="">Amount:</label>
                                <input type="number" class="form-control" id="txtAmount" v-model="uReconData.amount" :readonly="modalData.viewing == 1">
                            </form>
                        </template>
                    </Card>
                </div> -->
            </div>
        </template>
        <template #body v-else-if="modalData.viewing == 4">  <!-- 4-remove -->
            <input type="hidden" v-model="removeReconData.reconId">
            <div>
                <label>Remove Type: 
                    <icons icon="fas fa-circle-question" tabindex="0" data-bs-toggle="tooltip" data-bs-html="true" class="text-left"
                    title="
Cutoff Removal - will be removed to current cutoff and be inserted to the next cutoff
Permanent Delete - will be removed to current cutoff and will not insert to the next cutoff
                    "></icons>
                </label>
                <select id="selRemoveType" v-model="removeReconData.removeType" class="form-control" placeholder="Select">
                    <option value="" disabled>--Select--</option>
                    <option value="0">Cutoff Removal</option>
                    <option value="1">Permanent Delete</option>
                </select>
            </div>
            <div>
                <label>Reasons:</label>
                <textarea id="txtRemoveReasons" rows="5" class="form-control" v-model="removeReconData.reasons" required></textarea>
            </div>
        </template>
        <template #body v-else-if="modalData.viewing == 5"> <!-- 5- add data from eprpo -->
            <div class="row justify-content-center mb-2">
                <div class="col-4"> 
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #17a2b8; color: white;">Invoice Number:</span>
                        <input type="text" class="form-control" v-model="addReconData.invoiceNo" @keyup.enter="reloadDt()">
                        <button class="btn btn-success" type="button" @click="reloadDt()"><icons icon="fas fa-magnifying-glass"></icons></button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-9">
                    <Card :card-body="true" :card-header="true">
                        <template #header>
                            Reconciliation List
                        </template>
                        <template #body>
                            <DataTable
                                class="table table-sm table-bordered table-hover wrap display"
                                :columns="columnsAdd"
                                :ajax="{
                                    url: 'api/get_recon_for_add',
                                    data: function (param){
                                        param.param = dtParams;
                                        param.invoice_no = addReconData.invoiceNo;
                                    }
                                }"
                                ref="tableAdd"
                                :options="options1"
                            />
                        </template>
                    </Card>
                </div>
                <div class="col-3">
                    <Card :card-body="true" :card-header="true">
                        <template #header>
                            Remarks
                        </template>
                        <template #body>
                            <textarea class="form-control" cols="30" rows="10" id="userRemarks" v-model="addEprpoData.userRemarks"></textarea>
                        </template>
                    </Card>
                </div>
            </div>
           
        </template>
        <template #body v-else-if="modalData.viewing == 6"> <!-- 6- Edit data from arrs -->
            <input type="hidden" v-model="requestEditData.reconId">
            <div>
                <label>Reasons:</label>
                <textarea id="txtReqEditReasons" rows="5" class="form-control" v-model="requestEditData.reasons" required></textarea>
            </div>
        </template>
        <!-- 
            * FOOTER
        -->
        <!-- <template #footerButton v-if="modalData.viewing == 2 || modalData.viewing == 3"> 2-add recon data, 3-update -->
            <!-- <button type="button" class="btn btn-success" @click="saveReconData()">Save</button> -->
        <!-- </template> -->
        <template #footerButton v-if="modalData.viewing == 4"> <!-- 4-remove -->
            <button type="button" class="btn btn-success" @click="requestForRemove()" id="btnRemoveRecon">Send</button>
        </template>
        <template #footerButton v-else-if="modalData.viewing == 5">
            <button type="button" class="btn btn-success" @click="requestForAddition()" id="btnAddRecon">Send</button>
        </template>
        <template #footerButton v-else-if="modalData.viewing == 6">
            <button type="button" class="btn btn-success" @click="requestForEdit()" id="btnRequestEdit">Send</button>
        </template>
    </Modal>

</template>

<script setup>
    import { ref, reactive, inject, onBeforeMount, onMounted } from 'vue';
    import api from '../axios';
    
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
    DataTable.use(DataTablesCore);

    const userAccesses = ref();
    const cutoffSelect = reactive({
        option: [],
        selected: ""
    });
    const catStatus = reactive({
        status: false,
        label : ""
    });
    // const catStatusColor = ref(false);
    const columns = [
        {
            data: 'action',
            title: 'Action',
            orderable: false,
            searchable: false,
            createdCell(cell) {
                // * Button View
                cell.querySelector('.btnOpenReconDetails').addEventListener('click', function(){
                    let id = this.getAttribute('data-id');
                    getReconDetails(id, 1);
                });
                // // * Button Add Reconcile
                // if(cell.querySelector('.btnReconcileData')){
                //     cell.querySelector('.btnReconcileData').addEventListener('click', function(){
                //         let id = this.getAttribute('data-id');
                //         getReconDetails(id, 2);
                //     });
                // }

                // * Done Recon
                if(cell.querySelector('.btnDoneRecon')){
                    cell.querySelector('.btnDoneRecon').addEventListener('click', function(){
                        let id = this.getAttribute('data-id');
                        Swal.fire({
                            title: `Are you sure you want to done this data?`,
                            // text: "Request will go to logistics for approval.",
                            icon: 'question',
                            position: 'top',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                saveDoneRecon(id);
                            }
                        })
                    });
                }
                // *  Button Edit Reconcile
                if(cell.querySelector('.btnEditReconcileData')){
                    cell.querySelector('.btnEditReconcileData').addEventListener('click', function(){
                        let id = this.getAttribute('data-id');
                        getReconDetails(id, 3);
                    });
                }
                // *  Button Removing of Reconcile
                if(cell.querySelector('.btnRemoveData')){
                    cell.querySelector('.btnRemoveData').addEventListener('click', function(){
                        let id = this.getAttribute('data-id');
                        modalData.viewing = 4;
                        modalData.styleSize = '';
                        modalData.title = 'Request for Removal';
                        removeReconData.value.reconId = id;
                        modals.show();
                        // console.log(id);
                    });
                }
                // * Button Request for Edit
                if(cell.querySelector('.btnRequestToEdit')){
                    cell.querySelector('.btnRequestToEdit').addEventListener('click', function(){
                        let id = this.getAttribute('data-id');
                        modalData.viewing = 6;
                        modalData.styleSize = '';
                        modalData.title = 'Request for Edit';
                        requestEditData.value.reconId = id;
                        modals.show();
                        console.log('Request for Edit', modalData.viewing);
                    });
                }
            },
        },
        { data: 'status', title: 'Recon Status'},
        // { data: 'po_num', title: 'PO Number'},
        // { data: 'pr_num', title: 'PR Number'},
        // { data: 'prod_code', title: 'Code'},
        // { data: 'prod_name', title: 'Name'},
        // { data: 'prod_desc', title: 'Description'},
        // { data: 'supplier', title: 'Supplier'},
        // { data: 'received_date', title: 'Received Date'},
        // { data: 'classification', title: 'Classification'},
        { data: 'prod_name', title: 'Item Name'},
        { data: 'prod_desc', title: 'Description'},
        { data: 'invoice_no', title: 'Invoice No.'},
        { data: 'delivery_date', title: 'Delivery Date'},
        { data: 'received_qty', title: 'Received Qty'},
        { data: 'supplier', title: 'Supplier'},
        { data: 'unit_price', title: 'Unit Price'},
        { 
            data: 'unit_price',
            title: 'Amount',
            "render": function(data, type, row, meta) {
                let total = row.unit_price * row.received_qty;
                // return total.toFixed(2);
                let num = Math.round(total + "e" + 3);
                let num2 =  Number(num + "e" + -3);
                return num2.toFixed(2)
            }
        },
        { data: 'user_date_done', title: 'Date/Time Done'},
        { data: 'user_done', title: 'Done PIC'},
        
    ];

    const addEprpoDataInitialState = {
        'data' : [],
        'reconClassification' : "",
        'userRemarks': ""
    }
    const addEprpoData = reactive({...addEprpoDataInitialState});

    const columnsAdd = [
        { data: 'action', title: 'Action'},
      
        { data: 'reference_po_number', title: 'PO Number'},
        { data: 'po_number', title: 'PR Number'},
        { data: 'other_reference', title: 'Invoice Number'},
        { data: 'item_code', title: 'Code'},
        { data: 'item_name', title: 'Name'},
        { data: 'description', title: 'Description'},
        { data: 'supplier_name', title: 'Supplier'},
        { data: 'quantity_received', title: 'Received Qty'},
        { data: 'classification_code', title: 'Classification'},
        
    ];
    const options = {
        responsive: true,
        serverSide: true,
        processing: true,
        order: [[1, 'desc']],
        // "rowCallback": function(row,data,index ){
        //     console.log('data', data)
        // },
        'drawCallback': function( settings ) {
            let dtApi = this.api();
            let dtDatas = dtApi.rows( {page:'current'} ).data();
            let dtArrayStatus = [];
            let dtArrayFinalReconStatus = [];
            if(dtDatas.length>0){
                if(dtDatas[0]['raw_final_status'] == 1){
                    catStatus.status = false;
                    catStatus.label = "Pending";
                }
                else if(dtDatas[0]['raw_final_status'] == 2){
                    catStatus.status = false;
                    catStatus.label = "Pending on logistics";
                }
                else{
                    catStatus.status = true;
                    catStatus.label = "Tally";
                }
            }
        }
    };

    const options1 = {
        searching: false,
        ordering:  false,
        serverSide: true,
        processing: true,
        info: false
    };
    let dt;
    let dtAdd;
    let injectSess;
    const table = ref();
    const tableAdd = ref();
    const dtParams = reactive({
        'classification'    : 'null',
        'department'        : 'null'
    });
    const modalInitialState = {
        title : "",
        backdrop: "true",
        viewing: 0, // 0-none, 1-viewing, 2-add recon data, 3-update, 4-remove, 5- add data from eprpo
        size: "",
        styleSize: "max-width: 1750px !important; min-width: 1100px;"
    };
    const modalData = reactive({...modalInitialState});
    let modals

    const removeReconData = ref({});
    const requestEditData = ref({});
    const addReconData = ref({});

    const reconData = ref({});
    // const uReconData = ref({});
    const toastr = inject('toastr');
    const Swal = inject('Swal');

    onBeforeMount(async () => {
        console.log('before mount');
         injectSess = inject('store');

        await setTimeout( async () => {
            await api.get('api/get_category_of_user', {params: {access: injectSess.access}} ).then((result) => {
                userAccesses.value = result.data.uAccess;
                // console.log(result.data.uAccess);
            }).catch((err) => {
                
            });
        }, 200);
      
    });

    onMounted( async () => {
        dt = table.value.dt;
        console.log('mount');


        modals = new Modal(document.querySelector('#modalComponentId'));
        document.getElementById("modalComponentId").addEventListener('hidden.bs.modal', event => {
            
            console.log('modal is closed');
            Object.assign(modalData, modalInitialState); // * assign default value
            reconData.value = {}; // * Reset reconData ref data
            // uReconData.value = {}; // * Reset uReconData ref data
            removeReconData.value = {}; // * Reset removeReconData ref data
            requestEditData.value = {}; // * Reset requestEditData ref data
            addReconData.value = {};
            Object.assign(addEprpoData, addEprpoDataInitialState); // * assign default value
            addEprpoData.data = [];
        });
        getCutoffDate();
    });

    
    const loadDataTable = async (classification, department) => {

        dtParams.classification = classification;
        dtParams.department = department;
        
        dt.ajax.reload();
    }
    const getReconDetails = async (id, btnFunction) => {
        api.get('api/get_recon_details', { params:{ recon:id }}).then((result) => {
            // console.log(result.data.reconDetails);
            let reconDetails = result.data.reconDetails;

            modalData.title = "Reconciliation"; // Title of modal
            modalData.viewing = btnFunction;
            
            // uReconData.value.recon = result.data.recon; // Hash ID

            reconData.value.poDate = reconDetails.po_date;
            reconData.value.poNum = reconDetails.po_num;
            reconData.value.prNum = reconDetails.pr_num;
            reconData.value.receivedNum = reconDetails.rcv_no;
            reconData.value.code = reconDetails.prod_code;
            reconData.value.name = reconDetails.prod_name;
            reconData.value.supplier = reconDetails.supplier;
            reconData.value.desc = reconDetails.prod_desc;
            reconData.value.uPrice = reconDetails.unit_price;
            reconData.value.currency = reconDetails.currency;
            reconData.value.receivedQty = reconDetails.received_qty;
            reconData.value.uom = reconDetails.uom;
            reconData.value.poBal = reconDetails.po_balance;
            reconData.value.delDate = reconDetails.delivery_date;
            reconData.value.receivedDate = reconDetails.received_date;
            reconData.value.receivedBy = reconDetails.received_by;
            reconData.value.invoiceNum = reconDetails.invoice_no;
            reconData.value.class = reconDetails.classification;
            reconData.value.alloc = reconDetails.allocation;

            // if(btnFunction != 2){
            //     uReconData.value.invoiceNum   = reconDetails.recon_invoice_no
            //     uReconData.value.receivedQty  = reconDetails.recon_received_qty
            //     uReconData.value.amount       = reconDetails.recon_amount
            // }
            modals.show();

        }).catch((err) => {
            
        });
    }

    // const saveReconData = async () => {
    //     await api.post('api/save_recon', uReconData.value).then((result) => {
    //         document.querySelector('#txtAmount').classList.remove('is-invalid');
    //         document.querySelector('#txtInvoiceNum').classList.remove('is-invalid');
    //         document.querySelector('#txtReceivedQty').classList.remove('is-invalid');

    //         let results = result.data;

    //         if(results.result == 1){
    //             toastr.success(`${results.msg}`);
    //             modals.hide();
    //             dt.ajax.reload();
    //         }


    //     }).catch((err) => {
    //         // console.log(err.response);
    //         if(err.response.data.errors.amount != undefined){
    //             document.querySelector('#txtAmount').classList.add('is-invalid');

    //         }
    //         else{
    //             document.querySelector('#txtAmount').classList.remove('is-invalid');

    //         }

    //         if(err.response.data.errors.invoiceNum != undefined){
    //             document.querySelector('#txtInvoiceNum').classList.add('is-invalid');

    //         }
    //         else{
    //             document.querySelector('#txtInvoiceNum').classList.remove('is-invalid');

    //         }

    //         if(err.response.data.errors.receivedQty != undefined){
    //             document.querySelector('#txtReceivedQty').classList.add('is-invalid');
                
    //         }
    //         else{
    //             document.querySelector('#txtReceivedQty').classList.remove('is-invalid');
                
    //         }
            
    //         toastr.error('Please fill-up required fields.')
    //     });
    // }
    
    const requestForRemove = async () => {
        await Swal.fire({
            title: `Are you sure you want to proceed this request?`,
            text: "Request will go to logistics for approval.",
            icon: 'question',
            position: 'top',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                removeReconData.value.extraParams = dtParams;
                api.post('api/request_remove_recon', removeReconData.value).then((result) => {
                    document.querySelector('#txtRemoveReasons').classList.remove('is-invalid');
                    modals.hide();
                    toastr.success(`${result.data.msg}`);
                    dt.ajax.reload();
                    
                }).catch((err) => {
                    // console.log(err.response.data);
                    if(err.response.data.errors.reasons != undefined){
                        document.querySelector('#txtRemoveReasons').classList.add('is-invalid');
                    }
                    else{
                        document.querySelector('#txtRemoveReasons').classList.remove('is-invalid');
                    }

                    if(err.response.data.errors.removeType != undefined){
                        document.querySelector('#selRemoveType').classList.add('is-invalid');
                    }
                    else{
                        document.querySelector('#selRemoveType').classList.remove('is-invalid');
                    }
                    toastr.error('Please fill-up required fields.')
                    
                });
            }
            
        })
       
    }

    const btnAddRecon = async (params) => {
        if(catStatus.label == 'Tally'){
            Swal.fire({
                title: `Invalid`,
                text: `Reconciliation is tally on logistics`,
                icon: "error",
                position: "top",
            })
            return;
        }
        
        modalData.viewing = 5;
        modalData.styleSize = 'max-width: 1750px !important; min-width: 1100px;';
        modalData.size = '';
        modalData.backdrop = 'static';
        modalData.title = `Add Reconciliation Data for ${dtParams.classification}-${dtParams.department}`;
        
        setTimeout(() => {
            dtAdd = tableAdd.value.dt;
        }, 500);

        modals.show();
    }
    
    const reloadDt = () => {
        dtAdd.ajax.reload();
    }
    
    const requestForAddition = async () => {

        if(addEprpoData.userRemarks == ""){
            toastr.error('Please fill required field!');
            document.querySelector('#userRemarks').classList.add('is-invalid');
            return;
        }
        else{
            document.querySelector('#userRemarks').classList.remove('is-invalid');
            await Swal.fire({
                title: `Are you sure you want to proceed this request?`,
                text: "Request will go to logistics for approval.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    var inputElements = document.getElementsByClassName('checkedRecon');
                    for(var i=0; inputElements[i]; ++i){
                        if(inputElements[i].checked){
                            addEprpoData.data.push( inputElements[i].getAttribute('data-eprpo'));
                        }
                    }
                    addEprpoData.reconClassification = dtParams;
                    if(addEprpoData.data.length == 0){
                        toastr.error('Error! Please select reconciliation.');
                    }
                    else{
                        api.post('api/request_for_addition', { addEprpoData, cutoff_date: cutoffSelect.selected }).then((result) => {
                            let res = result.data;
                            toastr.success(`${res.msg}`);
                            modals.hide();
                        }).catch((err) => {
                            toastr.error(`${err.response.data.msg}`);
                        });
                    }
                }
            })
        }
        
    }

    const getCutoffDate = async () => {
        await api.get('api/get_recon_dates').then((result) => {
            cutoffSelect.option = result.data;
            cutoffSelect.selected = result.data[0];
        }).catch((err) => {
            
        });
    }
    
    const reconExport = () => {

        if(cutoffSelect.selected == undefined){
            toastr.error('Please Select Cutoff');
        }
        else{
            window.open(`api/export/${injectSess.appId}/${cutoffSelect.selected}/${injectSess.access}`, '_blank');
        }

        
    }

    const requestForEdit = (params) => {
        Swal.fire({
            title: `Are you sure you want to proceed this request?`,
            text: "Request will go to logistics for approval.",
            icon: 'question',
            position: 'top',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                requestEditData.value.extraParams = dtParams;
                api.post('api/request_for_edit', requestEditData.value).then((result) => {
                    toastr.success(`${result.data.msg}`);
                    modals.hide();
                    dt.ajax.reload();

                }).catch((err) => {
                    
                });
            }
            
        })
    }
    
    const saveDoneRecon = (id) => {
        // param.param = dtParams;
        // param.cutoff_date = cutoffSelect.selected
        api.post('api/save_done_recon', {rec_id: id, dt_params : dtParams, cutoff_date: cutoffSelect.selected}).then((result) => {
            toastr.success(`${result.data.msg}`);
            dt.ajax.reload();

        }).catch((err) => {
            console.log(err.response);
            if(err.response.status == 422){
                // toastr.error(`${err.response.data.msg}`);
                Swal.fire({
                    html: `${err.response.data.msg}`,
                    icon: "info",
                    position: "top",
                })
            }
            else{
                toastr.error(`${err}`);
            }
            
        });
    }

    const onChange = (event) => {
        // console.log(event.target.value);
        let classification = event.target.options[event.target.options.selectedIndex].getAttribute('classification')
        let department = event.target.options[event.target.options.selectedIndex].getAttribute('department')

        loadDataTable(classification,department)
    }
    
</script>