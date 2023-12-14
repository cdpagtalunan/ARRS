<template>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary" style="height: 100%;">
        <!-- Brand Logo -->
        <a href="" class="brand-link text-center text-decoration-none">
            <!-- <img src="../assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
            <span class="brand-text font-weight-light">ARRS</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex justify-content-center">
                <div class="info text-center">
                    <h6 class="d-block text-white text-capitalize"></h6>
                </div>
            </div> -->
            <!-- Sidebar Menu -->
            <nav class="mt-3">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item has-treeview">
                        <a href="../RapidX/" class="nav-link">
                            <icons icon="fas fa-arrow-left" class="nav-icon"></icons>
                            <p>Go to Rapidx</p>
                        </a>
                    </li>

                    <li class="nav-item has-treeview">
                        <router-link :to="{ name: 'Dashboard' }" class="nav-link">
                            <icons icon="fas fa-gauge" class="nav-icon"></icons>
                            <p>Dasboard</p>
                        </router-link>
                    </li>

                    <li class="nav-item has-treeview">
                        <router-link :to="{ name: 'Reconciliation' }" class="nav-link">
                            <icons icon="fab fa-steam-symbol" class="nav-icon"></icons>
                            <p>Reconciliation</p>
                        </router-link>
                    </li>

                    <!-- <li class="nav-item has-treeview">
                        <router-link :to="{ name: 'UserRequest' }" class="nav-link">
                            <icons icon="fas fa-clipboard-list" class="nav-icon"></icons>
                            <p>Request List</p>
                        </router-link>
                    </li> -->

                    <li class="nav-header font-weight-bold" style="font-size: 1rem">Admin</li>
                    
                    <!-- <div class="nav-item has-treeview" id="ppc-id">
                    </div> -->
                    <li class="nav-item  has-treeview">
                        <router-link :to="{ name: 'UserManagement' }" class="nav-link">
                            <icons icon="fas fa-user" class="nav-icon"></icons>
                            User Management
                        </router-link>
                    </li>

                    <li class="nav-item  has-treeview">
                        <router-link :to="{ name: 'ApprovalRequest' }" class="nav-link">
                            <icons icon="fas fa-file-circle-check" class="nav-icon"></icons>
                            User Requests
                        </router-link>
                    </li>
                    <li class="nav-header font-weight-bold" style="font-size: 1rem">Configuration</li>

                    <li class="nav-item">
                        <router-link :to="{ name: 'SettingsCutoff' }" class="nav-link">

                            <icons icon="fas fa-cog" class="nav-icon"></icons>
                            Cut-off
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="{ name: 'SettingsCategory' }" class="nav-link">

                            <icons icon="fas fa-cog" class="nav-icon"></icons>
                            User Category
                        </router-link>
                    </li>
                    
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>

<script setup>
    import { ref } from 'vue';
    // import { useAuthStore } from '../stores';
</script>
