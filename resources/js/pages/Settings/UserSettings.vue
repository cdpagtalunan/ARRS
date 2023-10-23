<template>
    <Breadcrumb title="Settings">
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
                            <table class="table table-sm table-bordered table-striped table-hover dt-responsive wrap" ref="tableUser" id="test">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <!-- <th>username</th> -->
                                        <!-- <th>Action</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in columns" :key="row.id"> 
                                        <td>{{ row.id }}</td>
                                        <td>{{ row.status }}</td>
                                        <!-- <td>{{ row.name }}</td> -->
                                        <!-- <td>{{ row.username }}</td> -->
                                        <!-- <td class="text-center">
                                            <button type="button" class="btn btn-info btn-sm mr-1" @click="editFunction(row.id)"><icons icon="fas fa-edit" class="fa-lg"></icons></button>
                                            <button type="button" :class="[row.logdel == 0 ? 'btn btn-danger btn-sm':'btn btn-success btn-sm' ]" @click="userStatusUpdate(row.id, row.logdel)">
                                                <icons icon="fas fa-user-slash" class="fa-lg" v-if="row.logdel == 0"></icons>
                                                <icons icon="fas fa-redo" class="fa-lg" v-else></icons>
                                            </button>
                                        </td> -->
                                    </tr>
                                </tbody>
                            </table>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>

   
</template>

<script setup>
    import { ref, watch, onMounted } from 'vue';
    import Breadcrumb from '../../components/Breadcrumb.vue';
    import Card from '../../components/Card.vue';
    import axios from 'axios';

    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net-bs5';
    import $ from 'jquery';
    DataTable.use(DataTablesCore);

    const columns = ref();
    const tableUser = ref();
    var dt = null;
    dt = $(tableUser.value).DataTable();

    onMounted(() => {
        getUser();
    })
     /*
        * WATCH will reload the DataTable after saving.
        * This will serve as .draw()
    */
    watch(columns, (columns) => {
        console.log(columns);
        dt.destroy();
        nextTick(() => {
            dt = $(tableUser.value).DataTable({
                "processing" : true,

            })
        }); 
    });
    const getUser = async () => {
        await axios.get('api/get_user').then((res) => {
            columns.value = res.data.data;
        })  
    };
</script>