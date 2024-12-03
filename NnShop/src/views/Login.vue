<script setup>
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import { useRouter } from "vue-router";
import { ref } from "vue";
import axios from "axios";
import { RouterLink } from "vue-router";
let url = "http://localhost/vuejs-server/api.php/login"

const router = useRouter();
const email = ref("");
const password = ref("");

const emailError = ref("");
const passwordError = ref("");

const handleSubmitt = async () => {
    try {
        let data = {
            email: email.value,
            password: password.value,
        };
        let response = await axios.post(url, data);
        let message = response.data;

        if (message.status == true) {
            localStorage.removeItem("userLogin");
            let userLogin = {
                id: message.data.id,
                name: message.data.name,
                email: message.data.email,
                role: message.data.role,
            };
            localStorage.setItem("userLogin", JSON.stringify(userLogin));

            if (message.data.role == "user") {
                router.push('/');
            } else if (message.data.role == "admin") {
                router.push('/admin');

            }
        } else {
            alert("Tài khoản hoặc mật khẩu không đúng")
        }
    } catch (error) {
        console.log(`Lỗi API ${error}`);

    }

}
const handleVali = (field) => {
    switch (field) {
        case 'email': {
            emailError.value = email.value == "" ? "Không được để trống Email" : ""
            break;
        }
        case 'password': {
            passwordError.value = password.value == "" ? "Không được để trống Password" : ""
            break;
        }
    }
};
</script>
<template>
    <form @submit.prevent="handleSubmitt">
        <h1>Đăng nhập</h1>
        <div class="m-4">
            <label for="email">Email</label>
            <input type="text" placeholder="Email" id="email" class="form-control" v-model="email"
                @keyup="handleVali('email')" />
            <span v-if="emailError != ''" class="text-danger">{{ emailError }}</span>
        </div>
        <div class="m-4">
            <label for="password">Password</label>
            <input type="password" placeholder="Password" id="password" class="form-control" v-model="password"
                @keyup="handleVali('password')" />
            <span v-if="passwordError != ''" class="text-danger">{{ passwordError }}</span>
        </div>
        <span class="register">
            Chưa có tài khoản:
            <RouterLink to="/dang-ky">Đăng ký</RouterLink>
        </span>
        <button class="btn btn-primary">Đăng nhập</button>
    </form>
</template>

<style scoped lang="css">
form {
    width: 40%;
    margin: auto;
    margin-top: 50px;

}

.register {
    display: block;
    margin-bottom: 10px;
}
</style>