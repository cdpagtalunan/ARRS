<template>
     <Breadcrumb title="Admin">
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Final Reconciliation</li>
        </template>
    </Breadcrumb>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <Card :card-body="true" :card-header="true">
                        <template #header>
                            <div class="row justify-content-between">
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <div class="input-group-prepend" width="50px">
                                            <span class="input-group-text w-100" id="basic-addon1" style="background-color: #3154b6; color: white;">Select Cut-off</span>
                                        </div>
                                        <select class="form-control w-25" v-model="cutoffSelect.selected" @change="()=>{ dtTableFinalRecon.ajax.reload() }">
                                            <option v-for="cutOffSelOption in cutoffSelect.option">{{ cutOffSelOption }}</option>
                                        </select>
                                        <select class="form-control w-25" v-model="shipTo" @change="()=>{ dtTableFinalRecon.ajax.reload() }">
                                            <option>Factory 1</option>
                                            <option>Factory 3</option>
                                        </select>
                                        
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template #body>
                            <DataTable
                                class="table table-sm table-bordered table-hover text-wrap display tableFinalRecon"
                                :columns="columnFinalRecon"
                                :ajax="{
                                    url: 'api/get_all_recon_cat',
                                    data: function (param){ 
                                        param.cutoff_date = cutoffSelect.selected;
                                        param.shipTo = shipTo;
                                        // param.is_auth = injectSess.isAuth;
                                    }
                                }"
                                ref="tableFinalRecon"
                                :options="optionsFinalRecon"
                            />
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { ref, reactive, onMounted, inject } from 'vue';
    import { onBeforeMount } from 'vue';
    import api from '../../axios';
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
    DataTable.use(DataTablesCore);

    const toastr = inject('toastr');
    const Swal = inject('Swal');
    const shipTo = ref('Factory 1');

    let dtTableFinalRecon;
    const tableFinalRecon = ref();
    const cutoffSelect = reactive({
        option: [],
        selected: ""
    });

    const optionsFinalRecon = {
        responsive: true,
        serverSide: true,
        processing: true,
        order: [[1, 'asc']]
    };

    const columnFinalRecon = [
        { 
            data: 'action', 
            title: 'Action', 
            orderable: false,
            searchable: false,
            createdCell(cell) {
                cell.querySelector('.btnDoneUserReconcile').addEventListener('click', function(){
                    let dept = this.getAttribute('data-dept');
                    let classification = this.getAttribute('data-classification');
                    let dateTo = this.getAttribute('data-to');
                    let dateFrom = this.getAttribute('data-from');

                    updateUserReconciliation(dept, classification, dateTo, dateFrom, shipTo.value);
                });

                if(cell.querySelector('.btnOpenRecon')){
                    cell.querySelector('.btnOpenRecon').addEventListener('click', function(){
                        let dept = this.getAttribute('data-dept');
                        let classification = this.getAttribute('data-classification');
                        let dateTo = this.getAttribute('data-to');
                        let dateFrom = this.getAttribute('data-from');

                        openUserReconciliation(dept,classification,dateTo,dateFrom)
                    });
                }
             
            }
        },
        { data: 'status', title: 'Status'},
        { data: 'general_category', title: 'Category'},
        { data: 'date_time_done', title: 'Date/Time Done'},
        { data: 'u_charge', title: 'User in-charge'},
    ];

    let injectSess;
    onBeforeMount( async() => {
        console.log('before mount');
        injectSess = inject('store');

        await getCutoffDate();


    });
    
    onMounted(() => {
        dtTableFinalRecon = tableFinalRecon.value.dt;
        setTimeout(() => {
            dtTableFinalRecon.ajax.reload();
            // console.log(injectSess.isAuth);

        }, 200);
    });

    const getCutoffDate = async () => {
        await api.get('api/get_recon_dates').then((result) => {
            cutoffSelect.option = result.data;
            cutoffSelect.selected = result.data[0];
        }).catch((err) => {
            toastr.error(err);
        });
    }

    const updateUserReconciliation = (dept, classification, dateTo, dateFrom, shipTo) => {

        if(injectSess.isAuth == 0){
            Swal.fire({
                title: "Error",
                text: "You are not authorized!",
                icon: "error"
            });
        }
        else{
            Swal.fire({
                title: `Are you sure you want to proceed?`,
                // text: "This.",
                icon: 'question',
                position: 'top',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    api.post('api/update_user_reconciliation', {dept: dept,classification: classification, to: dateTo, from: dateFrom, ship_to: shipTo}).then((result) => {
                        // console.log(result.data.result);
                        if(result.data.result == true){
                            toastr.success(result.data.msg);
                            dtTableFinalRecon.ajax.reload();
                        }
                    }).catch((err) => {
                        
                    });
                }
                
            })
        }
    }

    const openUserReconciliation = (dept,classification,dateTo,dateFrom) => {
        if(injectSess.isAuth == 0){
            Swal.fire({
                title: "Error",
                text: "You are not authorized!",
                icon: "error"
            });
        }
        else{
            Swal.fire({
                title: `Are you sure you want to proceed?`,
                // text: "This.",
                icon: 'question',
                position: 'top',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    api.post('api/update_open_recon', {dept: dept,classification: classification, to: dateTo, from: dateFrom, ship_to: shipTo.value}).then((result) => {
                        // console.log(result.data.result);
                        if(result.data.result == true){
                            toastr.success(result.data.msg);
                            dtTableFinalRecon.ajax.reload();
                        }
                    }).catch((err) => {
                        
                    });
                }
                
            })
        }
    }
</script>