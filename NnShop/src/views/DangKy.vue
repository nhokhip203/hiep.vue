<script setup>
import axios from "axios";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import { ref } from "vue";
import { RouterLink,useRouter } from "vue-router";
let url = "http://localhost/vuejs-server/api.php/register"
const router = useRouter();
const name = ref("");
const email=ref("");
const password=ref("");

const nameEr = ref("");
const emailEr=ref("");
const passwordEr=ref("");

const handlesubmit=async()=>{
    try {
        let data ={
        name: name.value,
        email:email.value,
        password:password.value
    }
    let response = await axios.post(url,data);
    if(response.status == 200){
        alert(response.data.message)
        router.push('/login');
    }
    } catch (error) {
        console.log(`Lỗi API ${error}`);
    }
    
};
const handleChange=(field)=>{
    switch(field){
        case'name':{
            nameEr.value=name.value==""?"Không để trống name":""
            break;
        }
        case'email':{
            emailEr.value=email.value==""?"Không để trống email":""
            break;
        }
        case'password':{
            passwordEr.value=password.value==""?"Không để trống password":""
            break;
        }
    }
}
</script>
<template>
   
<form @submit.prevent="handlesubmit">
 <h1>Đăng ký</h1>
    <div class="mb-4">
        <label for="name">Name</label>
        <input type="text" placeholder="Name" id="name" v-model="name" @keyup="handleChange('name')"class="form-control" >
        <span class="text-danger">{{ nameEr }}</span>
    </div>
    <div class="mb-4">
        <label for="email">Email</label>
        <input type="text" placeholder="Eamil" id="email" v-model="email"@keyup="handleChange('email')"class="form-control">
        <span class="text-danger">{{ emailEr }}</span>
    </div>
    <div class="mb-4">
        <label for="password">Password</label>
        <input type="password" placeholder="Password" id="password" v-model="password" @keyup="handleChange('password')"class="form-control">
        <span class="text-danger">{{ passwordEr }}</span>
    </div>
    <span class="register">
            Đã có tài khoản:
            <RouterLink to="/login">Đăng nhập</RouterLink>
        </span>
    <button class="btn btn-success">Đăng ký</button>
</form>
</template>
<style scoped lang="css">
form{
    width: 40%;
    margin: auto;
    margin-top: 50px;
    
}
.register{
        display: block;
        margin-bottom: 10px;
    }</style>