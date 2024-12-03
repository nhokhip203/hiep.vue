<script setup>
import { useRouter } from 'vue-router';
const props = defineProps({
    titleHeder:{
        type: String,
        required: true,
        default: "Admin",
    },
});
const router = useRouter();
const handleLogoui = ()=>{
    localStorage.removeItem('userLogin');
    router.push("/login");
}
</script>

<template>
<div class="bg-primary ps-4 pt-4 " style="height: 100px; width: 100%;">
    <div class="d-flex justify-content-between ps-2 pe-3">
        <h3 class="text-light">{{ props.titleHeder }}</h3>
        <button @click="handleLogoui" class="btn btn-info">Đăng xuất</button>
    </div>
    
</div>
</template>