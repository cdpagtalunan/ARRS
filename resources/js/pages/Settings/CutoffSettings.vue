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
                            <PrimeVueDatatable 
                                :users="dataTableProps.users" 
                                :filters="dataTableProps.filters" 
                                :globalFilter="dataTableProps.globalFilter" 
                                :loading="dataTableProps.loading"
                            >
                            <template #columns>
                                <Column field="id" header="ID" sortable></Column>
                                <Column field="fullname" header="Name" sortable></Column>
                                <Column field="id">
                                    <template #header>
                                        Action
                                    </template>
                                    <template #body="{ data, field }">
                                        <label class="w-100 text-center">
                                            <Button @click="test(data[field])" class="btn btn-info btn-sm" style="margin-right: .5em"><icons icon="fas fa-edit" class="fa-sm"></icons></Button>
                                            <Button class="btn btn-danger btn-sm"><icons icon="fas fa-trash" class="fa-sm"></icons></Button>
                                        </label>
                                    </template>
                                </Column>
                            </template>
                            </PrimeVueDatatable>
                            <!-- <DataTable :value="users" tableStyle="min-width: 50rem" showGridlines paginator stripedRows
                            v-model:rows="selected" class="p-datatable-sm" v-model:filters="filters" :globalFilterFields="['id', 'status']"
                            removableSort :loading="loading">
                                <template #header>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <label class="d-flex flex-row mt-2"> Show 
                                                <select class="custom-select custom-select-sm form-control form-control-sm ml-1 mr-1" v-model.number="selected">
                                                    <option>10</option>
                                                    <option>25</option>
                                                    <option>50</option>
                                                    <option>100</option>
                                                </select> entries</label>
                                        </div>
                                        <div>
                                            <span class="p-input-icon-left">
                                                <icons icon="fas fa-magnifying-glass"></icons>
                                                <InputText size="small" v-model="filters['global'].value" placeholder="Keyword Search" />
                                            </span>
                                        </div>
                                       
                                    </div>
                                </template> 
                                <template #empty><label class="w-100 text-center"> No data available.</label></template>
                                <template #loading>
                                        <ProgressSpinner style="width: 120px; height: 120px" strokeWidth="5" 
                                            animationDuration=".5s" fill="var(--surface-ground)"/>
                                </template>

                                <Column field="id" header="ID" sortable></Column>
                                <Column field="firstname" header="Name" sortable></Column>
                                <Column field="id">
                                    <template #header>
                                        Action
                                    </template>
                                    <template #body="{ data, field }">
                                        <label class="w-100 text-center">
                                            <Button @click="test(data[field])" class="btn btn-info btn-sm" style="margin-right: .5em"><icons icon="fas fa-edit" class="fa-sm"></icons></Button>
                                            <Button class="btn btn-danger btn-sm"><icons icon="fas fa-trash" class="fa-sm"></icons></Button>
                                        </label>
                                    </template>
                                </Column>
                            </DataTable> -->
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>
</template>
<!-- <style>
.p-datatable-loading-overlay{
    background-color: transparent !important;
}
</style> -->
<script setup>
    import { ref, onMounted, reactive } from 'vue';
    import Breadcrumb from '../../components/Breadcrumb.vue';
    import Card from '../../components/Card.vue';
    import axios from 'axios';
    import { FilterMatchMode } from 'primevue/api'; // * This is for datatable search
    import PrimeVueDatatable from '../../components/PrimeVueDatatable.vue';

    // const loading = ref(true);
    // const users = ref();

    onMounted(() => {
        axios.get('api/get_user').then((res)=>{
            // users.value = res.data.data;
            dataTableProps.users = res.data.data;
            // loading.value = false;
            dataTableProps.loading = false;
        });
    });
    // const filters = ref({
    //     global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    //     id: { value: null, matchMode: FilterMatchMode.IN },
    //     firsname: { value: null, matchMode: FilterMatchMode.IN },
    // });
    // const globalFilter = ref(['id','firstname']);

    const dataTableProps = reactive({
        users : [],
        filters: {
            global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            id: { value: null, matchMode: FilterMatchMode.IN },
            fullname: { value: null, matchMode: FilterMatchMode.IN },
        },
        globalFilter: ['id','fullname'],
        loading: true
    })
    // console.log(filters);
    
</script>