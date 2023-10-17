
<template>
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#">
                    <!-- <i class="fas fa-bars"></i> -->
                    <icons icon="fas fa-bars" class="nav-icon"></icons>
                    
                    <!-- <font-awesome-icon :icon="['fas', 'bars']" class="nav-icon"></font-awesome-icon> -->
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="" class="nav-link">ARRS</a>
            </li>
        </ul>
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Messages Dropdown Menu -->
            <li class="nav-item">
                <a class="nav-link" style="cursor: pointer;">
                    <icons icon="fas fa-user" class="nav-icon"></icons>
                    
                    <!-- <font-awesome-icon :icon="['far', 'user']" class="nav-icon"></font-awesome-icon> -->
                    <!-- Logout -->

                </a>
            </li>
            <!-- <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                <font-awesome-icon :icon="['far','user']" class="nav-icon"></font-awesome-icon>
            </a>
            <div class="dropdown-menu dropdown-menu-sm">
                <a href="#" class="dropdown-item" id="aLogout" data-bs-toggle="modal" data-bs-target="#modalLogout">
                <i class="fas fa-user mr-2"></i> Logout
                </a>
            
            </div>
        </li> -->
        </ul>


    </nav>
    <!-- <div class="modal fade" id="modalLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id=""></h5>
                    <button type="button" class="btn-close" data-item-process="create" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Are you sure to logout?</h6>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary" data-bs-dismiss="modal"
                        @click="logout">Logout</button>
                </div>

            </div>
        </div>
    </div> -->
</template>
<script setup>
</script>
