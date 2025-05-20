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
                            <button class="btn btn-success float-right" @click="openModal('Add User')" data-bs-toggle="modal" data-bs-target="#modalComponentId"><icons icon="fas fa-plus"></icons> Add User</button><br><br>
                            <!-- <table class="table table-sm table-bordered table-striped table-hover dt-responsive wrap" ref="tableUser">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="loading">
                                        <td colspan="2" class="text-center font-weight-bold">
                                            <div class="spinner-border spinner-border-sm" role="status"/>
                                        </td>
                                    </tr>
                                    <tr v-for="row in columns" :key="row.id">
                                        <td>{{ row.id }}</td>
                                        <td>{{ row.fk_preshipment_id }}</td>
                                    </tr>
                                </tbody>
                            </table> -->
                            <DataTable
                                class="table table-sm table-bordered table-hover wrap display"
                                :columns="columns"
                                ajax="api/get_user"
                                ref="table"
                                :options="options"
                            />
                        </template>
                    </Card>
                </div>
            </div>

            <Modal :title="modalDetails.title" backdrop="static">
                <template #body>
                    <!-- {{ formData }} -->
                    <input type="hidden" v-model="formData.id">
                    <label class="form-label">Employee</label>
                    <VueMultiselect
                        :class="{'invalid': empMultSel}"
                        v-model="formData.empDetails"
                        label="name"
                        placeholder="Select one"
                        :options="selectOptions"
                        selectLabel=""
                        deselectLabel=""
                        :searchable="true"
                        :allow-empty="false">
                        <template #noResult>
                            No User Found. Register User on RapidX.
                        </template>
                    </VueMultiselect>

                    <label>User Type</label>
                    <select
                        class="form-control"
                        v-model="formData.uType"
                        id="selUType"
                    >
                        <option value="1">Admin</option>
                        <option value="2">User</option>
                        <option value="3">Viewer</option>
                    </select>
                    <label>User Category</label>
                    <!-- <select
                        class="form-control"
                        v-model="formData.uCat"
                        id="selUCat"
                    >
                    <option value="0">Admin</option>
                    <option v-for="selectCatOption in selectCatOptions" :key="selectCatOption.id" :value="selectCatOption.id">{{ selectCatOption.classification + "-" + selectCatOption.department}}</option>

                    </select> -->

                    <VueMultiselect
                        :class="{'invalid': CatMultSel}"

                        v-model="formData.uCat"
                        placeholder="Search Category"
                        :custom-label="labelValue => selectCatOptions.find(x => x.id == labelValue).classification +'-'+selectCatOptions.find(x => x.id == labelValue).department"
                        :options="selectCatOptions.map(option => option.id)"
                        :multiple="true">
                    </VueMultiselect>

                    <label>Is Authorize?</label>
                    <icons icon="fas fa-circle-info" class="ml-1" data-bs-toggle="tooltip" data-bs-placement="right" 
                        title="If user can approve, disapprove the user request and done the final reconciliation">
                    </icons>
                    <select id="selAuthorized" v-model="formData.auth" class="form-control">
                        <option value="0"> Not Authorize</option>
                        <option value="1">Authorize</option>
                    </select>

                    <label>Is Superior/Head?</label>
                    <icons icon="fas fa-circle-info" class="ml-1" data-bs-toggle="tooltip" data-bs-placement="right" 
                        title="For Superior/Heads only.">
                    </icons>
                    <select id="selAuthorized" v-model="formData.supp" class="form-control">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>

                    <label class="mt-2">User Designation</label> <br>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="selDesigF1" v-model="formData.uDesig" value="Factory 1">
                        <label class="form-check-label" for="selDesigF1">
                            Factory 1
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="selDesigF3" v-model="formData.uDesig" value="Factory 3">
                        <label class="form-check-label" for="selDesigF3">
                            Factory 3
                        </label>
                    </div>
                </template>
                <template #footerButton>
                    <button type="button" class="btn btn-success" @click="saveUser">Save</button>
                </template>
            </Modal>
        </div>
    </section>


</template>
<style>

</style>
<script setup>
    import { ref, onMounted, reactive, inject } from 'vue';
    import api from '../../axios';

    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';


    DataTable.use(DataTablesCore);

    let dt;
    const table = ref();
    const modals = ref();
    const Swal = inject('Swal');
    const Toast = inject('Toast');
    const toastr = inject('toastr');

    const columns = [
        {
            data: 'action',
            title: 'Action',
            orderable: false,
            searchable: false,
            createdCell(cell) {
                cell.querySelector('.actionEditUser').addEventListener('click', function(){
                    let id = this.getAttribute('data-id');
                    getUserDetails(id);
                });
                cell.querySelector('.actionDelUser').addEventListener('click', function(){
                    let id = this.getAttribute('data-id');
                    let name = this.getAttribute('data-name');
                    var title;
                    if(name == 0){
                        title = "activate";
                    }
                    else{
                        title = "deactivate";
                    }
                    Swal.fire({
                        title: `Are you sure you want to ${title} this account?`,
                        // text: "You won't be able to revert this!",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            updateUserStat(id, name);
                        }
                    })
                });
            },
        },
        { data: 'status', title: 'Status', orderable: false,searchable: false},
        { data: 'rapidx_user_details.name', title: 'Name' },
        { data: 'rapidx_user_details.email', title: 'Email' },
        { data: 'user_type', title: 'Type' },
        { data: 'category', title: 'Assigned Category' },
        { 
            data: 'user_desig',
            title: 'Designation',
            // render: function(data){
            //     if(data == 1){
            //         return "Factory 1";
            //     }
            //     else{
            //         return "Factory 3"
            //     }
            // }
        },
        { 
            data: 'is_auth',
            title: 'Authorize',
            render: function(data){
                if(data == 0){
                    return "Not Authorize";
                }
                else{
                    return "Authorize"
                }
            }
        }

    ];
    const options = {
        responsive: true,
        serverSide: true,
        
    };
    const modalDetails = reactive({
        title : ""
    });

    const formData = ref({
        auth: 0,
        supp: 0
    });
    const selectOptions = ref([]);
    // const selectCatOptions = ref();
    var selectCatOptions = []
    const empMultSel = ref(false)
    const CatMultSel = ref(false)

    onMounted(() => {
        // getUser();
        getRapidxEmpDetails();
        getDropdownCatValues();

        modals.value = new Modal(document.querySelector('#modalComponentId'), {});
        document.getElementById("modalComponentId").addEventListener('hidden.bs.modal', event => {
            formData.value = {
                auth: 0,
                supp: 0

            };
            empMultSel.value = false
            document.querySelector('#selUType').classList.remove('is-invalid');
            CatMultSel.value = false

        });
        dt = table.value.dt;

        // console.log(document.querySelector(html));

    })

    const openModal = (title) => {
        modalDetails.title = title;
        // getRapidxEmpDetails();
    }

    const saveUser = async () => {
        await api.post('api/save_user', formData.value).then((result) => {
            // console.log(result);
            let response = result.data;
            toastr.success(response.msg);
            document.getElementById('close').click();
            dt.ajax.reload();
            // document.getElementById("modalComponentId").addEventListener('hide.bs.modal');
            // formData.value = {};
            // empMultSel.value = false;
            // document.querySelector('#selUType').classList.remove('is-invalid');
            // document.querySelector('#selUCat').classList.remove('is-invalid');


        }).catch((err) => {
            // console.log(err.response.data.errors.uType);
            if(err.response.data.errors.empDetails != undefined){
                empMultSel.value = true
                toastr.error(err.response.data.errors.empDetails);
            }
            else{
                empMultSel.value = false

            }
            if(err.response.data.errors.uType != undefined){
                // formDataErr.uTypeError = true
                document.querySelector('#selUType').classList.add('is-invalid');

            }
            else{
                // formDataErr.uTypeError = false
                document.querySelector('#selUType').classList.remove('is-invalid');

            }
            if(err.response.data.errors.uCat != undefined){
                CatMultSel.value = true
                // document.querySelector('#selUCat').classList.add('is-invalid');

            }
            else{
                CatMultSel.value = false
                // document.querySelector('#selUCat').classList.remove('is-invalid')
            }
            if(err.response.data.errors.uDesig != undefined){
                document.querySelector('#selDesigF1').classList.add('is-invalid');
                document.querySelector('#selDesigF3').classList.add('is-invalid');
                // document.querySelector('#selUCat').classList.add('is-invalid');

            }
            else{
                CatMultSel.value = false
                document.querySelector('#selDesigF1').classList.remove('is-invalid');
                document.querySelector('#selDesigF3').classList.remove('is-invalid');
            }


        });
    }

    const getRapidxEmpDetails = async () => {
        await api.get('api/get_rapidx_employee').then((result) => {
            selectOptions.value = result.data;
        }).
        catch((err) => {
        });
    }

    const getUserDetails = async (id) => {
        // console.log(modalsss.value);
        await api.get('api/get_user_details', { params: { emp: id } }).then((result) => {
            let data = result.data;
            console.log(data.userData.rapidx_user_details.name);
            formData.value.id = data.emp;
            formData.value.empDetails = data.userData.rapidx_user_details;
            formData.value.uType = data.userData.user_type;
            formData.value.uCat = data.forSelCat;
            formData.value.uDesig = data.userData.user_desig;
            formData.value.auth = data.userData.is_auth;
            formData.value.supp = data.userData.is_superior;
            modals.value.show();
        }).catch((err) => {

        });
    }

    const updateUserStat = async (id, fnName) => {
        await api.post('api/update_user_stat', { emp:id, fn_name: fnName }).then((res) => {
            let response = res.data;
            toastr.success(response.msg);
            dt.ajax.reload();

        }).catch((err) => {

        });
    }

    const getDropdownCatValues = async () => {
        await api.get('api/get_cat').then((result) => {
            console.log(result);
            // selectCatOptions.value = result.data;
            // selectCatOptions.push(result.data);
            selectCatOptions = result.data;
            selectCatOptions.push({'id':0, 'classification' : 'Admin', 'department' : ''});
        }).catch((err) => {

        });
    }
</script>
