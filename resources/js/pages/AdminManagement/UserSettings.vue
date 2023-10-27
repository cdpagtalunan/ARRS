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
                            />
                        </template>
                    </Card>
                </div>
            </div>

            <Modal :title="modalDetails.title" backdrop="static">
                <template #body>
                    <input type="hidden" v-model="formData.id"> 
                    <!-- {{formData}} -->

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
                    </select>
                    <label>User Category</label>
                    <select 
                        class="form-control"  
                        v-model="formData.uCat"
                        id="selUCat"
                    >
                        <option value="1">Admin</option>
                        <option value="2">RMI-TS-PPC</option>
                        <option value="3">RMI-CN-PPC</option>
                        <option value="4">RMI-YF-PPC</option>
                    </select>
                </template>
                <template #footerButton>
                    <button type="button" class="btn btn-success" @click="saveUser">Save</button>
                </template>
            </Modal>
        </div>
    </section>

   
</template>
<style>
.multiselect.invalid .multiselect__tags {
  border: 1px solid #f86c6b !important;
}
</style>
<!-- <style src="vue-multiselect/dist/vue-multiselect.css"></style> -->

<script setup>
    import { ref, onMounted, reactive, inject } from 'vue';
    import api from '../../axios';
    import VueMultiselect from 'vue-multiselect'
 
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
    

    DataTable.use(DataTablesCore);

    let dt;
    const table = ref();
    const modals = ref();
    const columns = [
        {
            data: 'action',
            title: 'Action',
            orderable: false,
            searchable: false,
            createdCell(cell) {

                cell.querySelector('.actionEditSystemDevelopment').addEventListener('click', function(){
                    let id = this.getAttribute('dev-id');
                    // console.log(id);
                    getUserDetails(id);


                });
            },
        },
        { data: 'rapidx_user_details.name', title: 'Name' },
        { data: 'user_type', title: 'Type' }
        
    ];
    const Swal = inject('Swal');
    const Toast = inject('Toast');
    const toastr = inject('toastr');
    const modalDetails = reactive({
        title : ""
    });

    const formData = ref({
    });
    const selectOptions = ref([]);
    const empMultSel = ref(false)

    onMounted(() => {
        // getUser();
        getRapidxEmpDetails();

        modals.value = new Modal(document.querySelector('#modalComponentId'), {});
        document.getElementById("modalComponentId").addEventListener('hidden.bs.modal', event => {
            formData.value = {};
        });
        dt = table.value.dt;

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
                // formDataErr.uCatError = true
                document.querySelector('#selUCat').classList.add('is-invalid');
               
            }
            else{
                // formDataErr.uCatError = false
                document.querySelector('#selUCat').classList.remove('is-invalid');
                
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
            formData.value.uCat = data.userData.category_id;
            modals.value.show();
        }).catch((err) => {

        });
    }

</script>