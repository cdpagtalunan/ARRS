<template>
    <Breadcrumb >
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Request List</li>
        </template>
    </Breadcrumb>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <Card :card-body="true" :card-header="true">
                        <template #header>
                            <router-link :to="{ name: 'Reconciliation' }">
                                <button class="btn btn-info btn-sm">
                                    <icons icon="fas fa-arrow-left"></icons> Go Back
                                </button>
                            </router-link>   
                        </template>
                        <template #body>
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#pending" role="tab" aria-selected="true" @click="reloadTable(1)">Pending</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#pending" role="tab" aria-selected="true" @click="reloadTable(2)">Approved</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                                    <br>
                                    <!-- ajax="api/pending_request" -->

                                    <DataTable
                                        class="table table-sm table-bordered table-hover wrap display"
                                        :columns="columns"
                                        :ajax="{
                                            url: 'api/get_request',
                                            data: function (param){
                                                param.param = tableParam;
                                                param.access = access;
                                            }
                                        }"
                                        ref="table"
                                        :options="options"
                                    />
                                </div>
                                <!-- <div class="tab-pane fade" id="approve" role="tabpanel" aria-labelledby="approve-tab">
                                    <br>
                                    <DataTable
                                        class="table table-sm table-bordered table-hover wrap display"
                                        :columns="columns"
                                        :ajax="{
                                            url: 'api/get_request',
                                            data: function (param){
                                                param.param = tableParam;
                                                param.access = access;

                                                // param.access = injectSess.access;
                                            }
                                        }"
                                        ref="table"
                                        :options="options"
                                    />
                                </div> -->
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>
    <Modal :title="modalData.title" :backdrop="modalData.backdrop" :modal-size="modalData.size" :style-size="modalData.styleSize">
        <template #body>
            <div class="row">
                <div class="col-9"  v-if="dtParams.type == 0"> <!-- Add Request Approval -->
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
                <div class="col-9" v-else>  <!-- Remove Request Approval -->
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
                                            <input type="text" class="form-control" v-model="removeReq.poDate"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">PO Number:</label>
                                            <input type="text" class="form-control" v-model="removeReq.poNum"
                                                readonly>
                                        </div>
                                        <div class="col-2">
                                            <label for="">Delivery Date:</label>
                                            <input type="text" class="form-control" v-model="removeReq.delDate"
                                                readonly>
                                        </div>
                                        <div class="col-2">
                                            <label for="">Received Date:</label>
                                            <input type="text" class="form-control" v-model="removeReq.receiveDate"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">PR Number:</label>
                                            <input type="text" class="form-control" v-model="removeReq.prNum"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Received Number:</label>
                                            <input type="text" class="form-control" v-model="removeReq.receiveNum"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Person In-charge:</label>
                                            <input type="text" class="form-control" v-model="removeReq.pic" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">Code:</label>
                                            <input type="text" class="form-control" v-model="removeReq.code"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Name:</label>
                                            <input type="text" class="form-control" v-model="removeReq.name"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Received By:</label>
                                            <input type="text" class="form-control" v-model="removeReq.receiveBy" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">Supplier:</label>
                                            <!-- <input type="text" class="form-control" v-model="reconData.supplier" readonly> -->
                                            <textarea class="form-control" v-model="removeReq.supplier"
                                                style="resize: none;" readonly></textarea>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Description:</label>
                                            <!-- <input type="text" class="form-control" v-model="reconData.desc" readonly> -->
                                            <textarea class="form-control" v-model="removeReq.desc"
                                                style="resize: none;" readonly></textarea>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Invoice No.:</label>
                                            <textarea class="form-control" v-model="removeReq.invNo"
                                                style="resize: none;" readonly></textarea>
                                            <!-- <input type="text" class="form-control" v-model="reconData.invoiceNum" readonly> -->
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="">Unit Price:</label>
                                            <input type="text" class="form-control" v-model="removeReq.price"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Currency:</label>
                                            <input type="text" class="form-control" v-model="removeReq.currency"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Classification Code:</label>
                                            <input type="text" class="form-control" v-model="removeReq.class" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-2">
                                            <label for="">Received Qty:</label>
                                            <input type="text" class="form-control" v-model="removeReq.qty"
                                                readonly>
                                        </div>
                                        <div class="col-2">
                                            <label for="">UOM:</label>
                                            <input type="text" class="form-control" v-model="removeReq.uom"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">PO Balance:</label>
                                            <input type="text" class="form-control" v-model="removeReq.poBal"
                                                readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Allocation:</label>
                                            <input type="text" class="form-control" v-model="removeReq.alloc" readonly>
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
                            <textarea class="form-control" cols="30" rows="10" readonly v-model="userRequestRemarks" id="txtUserRemarks"></textarea>
                        </template>
                    </Card>
                </div>
            </div>
        </template>
    </Modal>    
</template>

<script setup>
    import { onMounted, ref, reactive, onBeforeMount } from 'vue';
    import { inject } from 'vue';
    import api from '../axios';
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
    
    DataTable.use(DataTablesCore);

    let modals;
    let dt;
    let dtTableRequest;
    const access = ref([]);
    const table = ref();
    const tableRequestDetails = ref();
    const removeReq = ref({});
    const userRequestRemarks = ref();
    const tableParam = ref([0,2]);
    const dtParams = reactive({
        'ctrl_number'       : 'null',
        'ctrl_ext'          : 'null',
        'status'            : '',
        'type'              : 0 // 0-Add, 1-remove
    });
    const columns= [
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
                        let type = this.getAttribute('data-type');
                        dtParams.ctrl_number = ctrlNo;
                        dtParams.ctrl_ext = ctrlExt;
                        dtParams.status = status;
                        dtParams.type = type;
                        modals.show();
                    if(type == 0){
                        dtTableRequest.ajax.reload();
                    }
                    else{
                        api.get('api/get_requested_recon_details', {params: {param: dtParams} }).then((result) => {
                            console.log('success get_requested_recon_details');
                            let res = result.data.data;
                            removeReq.value.poDate      = res.recon_details.po_date;
                            removeReq.value.poNum       = res.recon_details.po_num;
                            removeReq.value.delDate     = res.recon_details.delivery_date;
                            removeReq.value.receiveDate = res.recon_details.received_date;
                            removeReq.value.prNum       = res.recon_details.pr_num;
                            removeReq.value.receiveNum  = res.recon_details.rcv_no;
                            removeReq.value.pic         = res.recon_details.pic;
                            removeReq.value.code        = res.recon_details.prod_code;
                            removeReq.value.name        = res.recon_details.prod_name;
                            removeReq.value.receiveBy   = res.recon_details.received_by;
                            removeReq.value.supplier    = res.recon_details.supplier;
                            removeReq.value.desc        = res.recon_details.prod_desc;
                            removeReq.value.invNo       = res.recon_details.invoice_no;
                            removeReq.value.price       = res.recon_details.unit_price;
                            removeReq.value.currency    = res.recon_details.currency;
                            removeReq.value.class       = res.recon_details.classification;
                            removeReq.value.qty         = res.recon_details.received_qty;
                            removeReq.value.uom         = res.recon_details.uom;
                            removeReq.value.poBal       = res.recon_details.po_balance;
                            removeReq.value.alloc       = res.recon_details.allocation;


                            userRequestRemarks.value = res.recon_remarks.remarks;
                        }).catch((err) => {
                            
                        });
                    }
                });
            },
        },
        { data: 'req_status', title: 'Status' },
        { data: 'control', title: 'Control Number' },
        { data: 'invoice_no', title: 'Invoice Number' },
        // { data: 'recon_details.prod_desc', title: 'Description' },
        // { data: 'recon_details.invoice_no', title: 'Invoice Number' },
        { data: 'po', title: 'PO Number' }
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

    const modalInitialState = {
        title : "",
        backdrop: "true",
        viewing: 0, // 0-none, 1-viewing, 2-add recon data, 3-update, 4-remove, 5- add data from eprpo
        size: "",
        styleSize: "max-width: 1750px !important; min-width: 1100px;"
    };
    const modalData = reactive({...modalInitialState});

    onMounted(()=>{
        modals = new Modal(document.querySelector('#modalComponentId'));
        document.getElementById("modalComponentId").addEventListener('hidden.bs.modal', event => {
            Object.assign(modalData, modalInitialState); // * assign default value
        });

        dtTableRequest = tableRequestDetails.value.dt;
        dt = table.value.dt;
        // console.log('mounted', access);

    })
    onBeforeMount( async () => {
        let injectSess = inject('store');
        await setTimeout(() => {
            access.value = injectSess.access;
        }, 200);
    })
    
    const reloadTable = (fn) => {
        // * fn [0,2] = pending and disapprove, [1]- approved
        if(fn == 1){
            tableParam.value = [0,2];
        }
        else{
            tableParam.value = [1];
        }
        dt.ajax.reload();
    }
    
</script>