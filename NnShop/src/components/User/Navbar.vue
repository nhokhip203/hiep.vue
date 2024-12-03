<script setup>
import { reactive,ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
const router = useRouter();
let userLogin = reactive({});
if(localStorage.getItem("userLogin") != null){
    userLogin = JSON.parse(localStorage.getItem("userLogin"));
    console.log(userLogin);
    console.log(userLogin.id);
    
    
}
const handleLogout = ()=>{
    localStorage.removeItem("userLogin");
    router.push("/login");
}
</script>
<template>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <RouterLink class="navbar-brand" to="/">Trang chủ</RouterLink>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Dropdown
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" aria-disabled="true">Disabled</a>
                    </li>
                </ul>
                <div v-if="userLogin.id" class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" 
                    type="button" 
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                       {{ userLogin.name }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Tài khoản</a></li>
                        <li><RouterLink class="dropdown-item" to="/giohang">Giỏ hàng</RouterLink></li>
                        <li><RouterLink class="dropdown-item" to="/donhang">Đơn hàng</RouterLink></li>
                        <li><a class="dropdown-item" href="#" @click.prevent="handleLogout">Đăng xuất</a></li>
                    </ul>
                </div>
                <RouterLink v-else to="/login" class="btn btn-success">Đăng nhập</RouterLink>
            </div>
        </div>
    </nav>
</template>
<style scoped lang="scss">
.img-header {
    height: 400px;
    object-fit: cover;
}

.main-web {
    min-height: 500px;
}
</style>