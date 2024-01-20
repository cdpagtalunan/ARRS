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
                           <div class="row">
                                <div class="d-flex justify-content-center">
                                    <div class="col-sm-4">
                                        <label for="">Select Cut-off</label>
                                        <!-- <div class="input-group mb-3"> -->
                                            <!-- <select class="form-control" aria-describedby="button-addon2" v-model="cutoffSelect.selected">
                                                <option v-for="cutoffOption in cutoffSelect.option">{{ cutoffOption }}</option>
                                            </select> -->
                                            <VueMultiselect 
                                                v-model="cutoffSelect.selected"
                                                :custom-label="labelValue => cutoffSelect.option.find(x => x == labelValue)"
                                                placeholder="Select one"
                                                :options="cutoffSelect.option"
                                                selectLabel=""
                                                deselectLabel=""
                                                :searchable="false"
                                                :allow-empty="false">
                                            </VueMultiselect>
                                            
                                            <button class="btn btn-primary mt-1 float-end form-control" type="button" @click="adminExport()">Export</button>
                                        <!-- </div> -->
                                    </div>
                                </div>
                           </div>
                        </template>
                    </Card>
                </div>
            </div>

            
        </div>
    </section>
</template>
<style>

</style>
<script setup>
    import { ref, onMounted, reactive } from 'vue';
    import api from '../../axios';
import { faWindows } from '@fortawesome/free-brands-svg-icons';

    const cutoffSelect = reactive({
        option: [],
        selected: ""
    });

    onMounted( async () => {
        await getCutoffDate();
        console.log(cutoffSelect.option);
    })

    const getCutoffDate = async () => {
        await api.get('api/get_recon_dates').then((result) => {
            cutoffSelect.option = result.data;
            cutoffSelect.selected = result.data[0];
        });
    }
    
    const adminExport = async () => {
        if(cutoffSelect.selected == undefined){
            toastr.error('Please Select Cutoff');
        }
        else{
            window.open(`api/export_admin/${cutoffSelect.selected}`, '_blank');
        }
    }
</script>