<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import axios from "axios";
const usertList = ref([])
let urlAPI = "http://localhost/vuejs-server/api.php/users";

onMounted(() => {
    CallAPI();
});


const CallAPI = async () => {
    try {
        let response = await axios.get(urlAPI);
        usertList.value = response.data;
        console.log(usertList.value);
    } catch (error) {
        console.log("CallAPI lỗi");
    }
}


const handleClick = async (id) => {
    let check = confirm("bạn có muốn xóa không");
    if (check) {
        try {
            let response = await axios.delete(urlAPI + "/" + id);
            if (response.status === 200) {
                alert("xóa thành công");
                CallAPI();
            }
        } catch (error) {
            console.log("CallAPI lỗi");
        }
    }
}
</script>

<template>
    <div class="p-4" style="min-height: 800px;">
        <h1>Quản lí Account</h1>
        <RouterLink to="/admin/add-account" class="btn btn-primary">Thêm mới</RouterLink>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(user, index) in usertList">
                    <td>{{ index + 1 }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.role }}</td>
                    
                    <td>
                        <RouterLink :to="``"
                         class="btn btn-warning">Sửa</RouterLink>
                        <button @click="handleClick" class="btn btn-danger">Xóa</button>
                    </td>

                </tr>
            </tbody>
        </table>
    </div>
</template>