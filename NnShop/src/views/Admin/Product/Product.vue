<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import axios from "axios";
const productList = ref([])
let urlAPI = "http://localhost/vuejs-server/api.php/products";
let urlImage = "http://localhost/vuejs-server/uploads/product/";

onMounted(() => {
    CallAPI();
});


const CallAPI = async () => {
    try {
        let response = await axios.get(urlAPI);
        productList.value = response.data;
        console.log(productList.value);
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
        <h1>Quản lí sản phẩm</h1>
        <RouterLink to="/admin/add-product" class="btn btn-primary">Thêm mới</RouterLink>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category_ID</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(product, index) in productList">
                    <td>{{ index + 1 }}</td>
                    <td>{{ product.name }}</td>
                    <td>{{ product.description }}</td>
                    <td>{{ product.price }}</td>
                    <td>{{ product.categoryName}}</td>
                    <td>
                        <img v-if="product.image != null" :src="urlImage + product.image" alt="" width="50">
                        <span v-else class="badge text-bg-warning">Không có ảnh</span>
                    </td>
                    <td>
                        <RouterLink :to="`update-product/${product.id}`"
                         class="btn btn-warning">Sửa</RouterLink>
                        <button @click="handleClick(product.id)" class="btn btn-danger">Xóa</button>
                    </td>

                </tr>
            </tbody>
        </table>
    </div>
</template>