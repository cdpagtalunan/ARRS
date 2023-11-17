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
                                <div class="col-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend" width="50px">
                                            <span class="input-group-text w-100" id="basic-addon1" style="background-color: #3154b6; color: white;">Select Cut-off</span>
                                        </div>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-sm btn-info" @click="loadDataEPRPO(1)">test</button>
                                    <button type="button" class="btn btn-sm btn-info" @click="loadDataEPRPO(2)">test</button>

                                    <strong>Status:</strong>
                                </div>
                            </div>
                        </template>
                        <template #body>
                            <!-- {{ cutOffOptions }} -->
                            <ul class="nav nav-tabs">
                                <li class="nav-item" v-for="cutOffOption in cutOffOptions" :key="cutOffOption.id">
                                    <a class="nav-link" data-bs-toggle="tab" :href="'#'+cutOffOption.id" role="tab" aria-selected="true">{{ `${cutOffOption.classification}-${cutOffOption.department}` }}</a>
                                </li>
                                <!-- <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#MH-checking" role="tab" aria-controls="MH-checking" aria-selected="true">Active</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#MH-inspector" role="tab" aria-controls="MH-inspector" aria-selected="true">Link</a>
                                </li> -->
                            </ul>

                            <div class="tab-content">
                                <!-- <div v-for="cutOffOption in cutOffOptions" :key="cutOffOption.id"> -->
                                    <div class="tab-pane fade" v-for="cutOffOption in cutOffOptions" :key="cutOffOption.id" :id="cutOffOption.id" role="tabpanel" aria-labelledby="MH-checking-tab">
                                        {{ cutOffOption.id }}
                                    </div>
                                <!-- </div> -->
                               
                                <!-- <div class="tab-pane fade" id="MH-inspector" role="tabpanel" aria-labelledby="MH-inspector-tab">
                                    ellos
                                </div> -->
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { ref, reactive, inject, onBeforeMount } from 'vue';
    import api from '../axios';
    
    const cutOffOptions = ref();

    onBeforeMount(async () => {
        let injectSess = inject('store');

        await api.get('api/get_category_of_user', {params: {access: injectSess.access}} ).then((result) => {
            cutOffOptions.value = result.data.uAccess;
            // console.log(result.data.uAccess);
        }).catch((err) => {
            
        });
    });

    const loadDataEPRPO = async (date) => {
        await api.get('api/get_eprpo_data', { params: {cutoff:date} }).then((result) => {
            
        }).catch((err) => {
            
        });
    }
    // console.log(cutOffOptions.value);

    
</script>