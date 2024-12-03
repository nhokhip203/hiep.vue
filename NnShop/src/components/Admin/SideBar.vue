<script setup>
import { reactive,ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
let userLogin = reactive({});
if(localStorage.getItem("userLogin") != null){
    userLogin = JSON.parse(localStorage.getItem("userLogin"));
}
const router = useRouter();
</script>
<template>
    <div class="col-3 bg-info position-fixed top-0 start-0" style="height: 100vh;">
                <h3 class="mt-4 text-center text-light">Admin: {{ userLogin.name }}</h3>
        <div class="list-group mt-4">
                    <RouterLink to="/admin" class="list-group-item ">
                        Dashoard
                    </RouterLink>
                    <RouterLink to="/admin/account" class="list-group-item">
                        Quản lý tài khoản
                    </RouterLink>
                    <RouterLink to="/admin/category" class="list-group-item ">
                        Quản lý danh mục
                    </RouterLink>
                    <RouterLink to="/admin/product" class="list-group-item ">
                        Quản lý sản phẩm
                    </RouterLink>
                    <RouterLink to="/admin/order" class="list-group-item ">
                        Quản lý đơn hàng
                    </RouterLink>
                </div>
            </div>
</template>