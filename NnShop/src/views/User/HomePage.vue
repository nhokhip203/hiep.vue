<script setup>
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import Header from '@/components/User/Header.vue';
import Navbar from '@/components/User/Navbar.vue';
import Footer from '@/components/User/Footer.vue';
import { RouterView } from 'vue-router';
</script>
<template>
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <Header/>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <Navbar/>
            </div>
        </div>
        <RouterView />
        <div class="row">
            <div class="col-12">
                <!-- As a link -->
                <Footer/>
            </div>
        </div>

    </div>
</template>
<style scoped lang="scss">
.img-header{
    height: 400px;
    object-fit: cover;
}
.main-web{
    min-height: 500px;
}
</style>