<script setup>
import { RouterView, RouterLink } from 'vue-router';
import SideBar from '@/components/Admin/SideBar.vue';
import Header from '@/components/Admin/Header.vue';
import Footer from '@/components/Admin/Footer.vue';
import { useRoute } from 'vue-router';
import { watch,ref } from 'vue';

const route = new useRoute();
const titleHeder = ref("");
watch(
    () => route.name,(newName)=>{
        switch(newName){
            case "dashboard": {
                titleHeder.value="Admin | Dashboard";
                document.title ="Admin Dashboard";
                break;
            }
            case "account": {
                titleHeder.value="Admin | Account";
                document.title ="Admin Account";
                break;
            }
            case "product": {
                titleHeder.value="Admin | Product";
                document.title ="Admin Product";
                break;
            }
            case "category": {
                titleHeder.value="Admin | Category";
                document.title ="Admin Category";
                break;
            }
            case "order": {
                titleHeder.value="Admin | Order";
                document.title ="Admin Order";
                break;
            }
        }
    },
    { immediate:true}
);
</script>
<template>
    <div class="container-fluid">
        <div class="row">
            <!--SideBar-->
            <SideBar/>
            <div class="col-9 offset-3 p-0 position-relative">
                <Header :titleHeder="titleHeder"/>
                <RouterView/>
                <Footer/>
            </div>
        </div>
    </div>
</template>
<style scoped lang="css"></style>