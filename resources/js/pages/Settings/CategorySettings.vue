<template>
    <Breadcrumb title="Settings">
        <template #breadcrumbs>
            <li class="breadcrumb-item"><a href="">Dashboard</a></li>
            <li class="breadcrumb-item active">Category</li>
        </template>
    </Breadcrumb>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <Card :card-body="true">
                        <template #body>
                            <button class="btn btn-success float-right" @click="modalDetails.title = 'Add Category'" data-bs-toggle="modal" data-bs-target="#modalComponentId"><icons icon="fas fa-plus"></icons> Add Category</button><br><br>

                            <DataTable
                                class="table table-sm table-bordered table-hover wrap display"
                                :columns="columns"
                                ajax="api/get_category"
                                ref="table"
                                :options="options"
                            />
                        </template>

                    </Card>
                </div>
            </div>
        </div>
    </section>
    <Modal :title="modalDetails.title" backdrop="static" @save-event="saveCategory">
        <template #body>
            <input type="hidden" v-model="formCat.catId">
            <div class="alert alert-warning" role="alert">
                Please be reminded that this will reflect on excel export file.
                And will be the system reference for EPRPO.
            </div>
            <label for="catName" class="form-label">Classification</label>
            <VueMultiselect 
                :class="{'invalid': multSelect.classification}"
                v-model="formCat.classification"
                placeholder="Select one"
                :options="selectOptionsClass"
                selectLabel=""
                deselectLabel=""
                :searchable="true"
                :allow-empty="false">
                <template #noResult>
                    No classification found in EPRPO.
                </template>
            </VueMultiselect>

            <label class="form-label">Department</label>
            <VueMultiselect 
                :class="{'invalid': multSelect.department}"
                v-model="formCat.department" 
                placeholder="Select one"
                :options="selectOptionsDept"
                selectLabel=""
                deselectLabel=""
                :searchable="true"
                :allow-empty="false">
                <template #noResult>
                    No department found in EPRPO.
                </template>
            </VueMultiselect>
        </template>
        <template #footerButton>
            <button type="submit" class="btn btn-success" id="btnSave">Save</button>
        </template>
    </Modal>
</template>

<script setup>
    import { ref, onMounted, reactive, inject } from 'vue';
    import Breadcrumb from '../../components/Breadcrumb.vue';
    import api from '../../axios';

    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
    DataTable.use(DataTablesCore);

    let dt;
    const table = ref();
    const formCat = ref({});
    const columns = [
        {
            data: 'action',
            title: 'Action',
            orderable: false,
            searchable: false,
            createdCell(cell) {
                cell.querySelector('.btnEditCat').addEventListener('click', function(){
                    let id = this.getAttribute('data-cat');
                    getCatDetails(id);
                });

                cell.querySelector('.btnUpdatecatStat').addEventListener('click', function(){
                    let id = this.getAttribute('data-cat');
                    let btnFunction = this.getAttribute('data-name');
                    let title;
                    if(btnFunction == 0){
                        title = "deactivate";
                    }
                    else{
                        title = "activate";

                    }
                    Swal.fire({
                        title: `Are you sure you want to ${title} this category?`,
                        // text: "You won't be able to revert this!",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            updateCatStat(id, btnFunction);
                        }
                    })
                    // getCatDetails(id);
                });
            },
        },
        { data: 'status', title: 'Status'},
        { data: 'classification', title: 'Classification'},
        { data: 'department', title: 'Department'},
        
    ];
    const options = {
        responsive: true,
        serverSide: true
    }
    const toastr = inject('toastr');
    const Swal = inject('Swal');
    const modalDetails = ref({
        title: ""
    })
    const selectOptionsDept = ref([]);
    const selectOptionsClass = ref([]);
    const multSelect = reactive({
        classification: false,
        department: false
    });
    // const modals = ref();
    let modals
    onMounted(() => {
        dt = table.value.dt;
        modals = new Modal(document.querySelector('#modalComponentId'));
        document.getElementById("modalComponentId").addEventListener('hidden.bs.modal', event => {
            console.log('formCat reset');
            formCat.value = {};
        });
        getDropdownData();
    });
    const getDropdownData = async() => {
        await api.get('api/get_dropdown_data').then((result) => {
            // console.log(result);
            let res = result.data;
            selectOptionsDept.value     = res.secDept;
            selectOptionsClass.value    = res.classCode;
        }).catch((err) => {
            
        });
    }
    const saveCategory = async () => {
        document.querySelector('#btnSave').setAttribute("disabled", true);
        await api.post('api/save_cat', formCat.value).then((result) => {
            let res = result.data;
            multSelect.classification = false;
            multSelect.department = false;
            toastr.success(result.data.msg);
            document.querySelector('#btnSave').removeAttribute("disabled");
            modals.hide();
            dt.ajax.reload();
        }).catch((err) => {
            // console.log(err.response);
            let error = err.response.data.errors;
            if(error.classification != undefined){
                multSelect.classification = true;
            }
            else{
                multSelect.classification = false;
            }

            if(error.department != undefined){
                multSelect.department = true;

            }
            else{
                multSelect.department = false;
            }

            toastr.error('Please fill up required fields!');
            document.querySelector('#btnSave').removeAttribute("disabled");

        });
    }

    const getCatDetails = async (id) => {
        await api.get('api/get_category_details',{ params: { cat: id } }).then((result) => {
            let res = result.data;
            formCat.value.catId = res.cat;
            formCat.value.classification = res.catDetails.classification;
            formCat.value.department = res.catDetails.department;
            modals.show();
        }).catch((err) => {
            
        });
    }

    const updateCatStat = async (id, func) => {
        await api.post('api/update_cat_status', { cat: id, function:func }).then((result) => {
            toastr.success(result.data.msg);
            dt.ajax.reload();
        }).catch((err) => {
            
        });
    }

</script>