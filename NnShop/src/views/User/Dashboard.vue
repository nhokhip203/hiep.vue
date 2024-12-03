<script setup>
let url = "http://localhost/vuejs-server/api.php/products";
let urlSearch = "http://localhost/vuejs-server/api.php/search-product";
let urlCate = "http://localhost/vuejs-server/api.php/categories";
let urlImage = "http://localhost/vuejs-server/uploads/product/";
import { RouterLink } from 'vue-router';
import { ref, onMounted } from 'vue';
import axios from 'axios';
const listProduct = ref([]);
const callAPI = async () => {
    try {
        let response = await axios.get(url);
        listProduct.value = response.data;
    } catch (error) {
        console.log(`Lỗi API ${error}`);
    }
};
const converPrice = (number) => {
    return number.toLocaleString("vi-VN")
}

const listCategory = ref([]);
const callAPICate = async () => {
    try {
        let response = await axios.get(urlCate);
        listCategory.value = response.data;
    } catch (error) {
        console.log(`Lỗi API ${error}`);
    }
};

onMounted(() => {
    callAPI();
    callAPICate();
});


const productName = ref('');
const hangdSearch = async () => {
    if (productName.value != "") {
        try {
            let data = {
                product_name: productName.value,
            };
            let response = await axios.post(urlSearch, data);
            if (response.data.status == true) {
                productName.value = "";
                listProduct.value = response.data.products;
            }
        } catch (error) {
            console.log(error);
        }

    } else {
        callAPI();
    }
};
</script>
<template>
    <div class="row main-web">
        <div class="col-8">
            <div class="d-flex flex-wrap">
                <div class="card" style="width: 18rem;" v-for="(product, index) in listProduct" :key="index">
                    <img :src="urlImage + product.image" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">{{ product.name }}</h5>
                        <p class="text-price">{{ converPrice(Number(product.price)) }} VNĐ</p>
                        <p class="card-text">{{ product.description }}</p>
                        <RouterLink :to="`san-pham-chi-tiet/${product.id}`" class="btn btn-primary">Mua hàng</RouterLink>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3 offset-1 p-4">
            <div class="card md-5">
                <div class="card-body d-flex">
                    <input type="text" class="form-control me-2" v-model="productName" />
                    <button class="btn btn-success" @click="hangdSearch">Search</button>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Danh sách danh mục</div>
                <div class="list-group">
                    <RouterLink v-for="(category, index) in listCategory" :key="index" :to="`/san-pham/${category.id}`"
                        class="list-group-item list-group-item-action">
                        {{ category.name }}
                    </RouterLink>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped lang="css">
.card {
    margin-top: 30px;
    margin: 10px;
}

.text-price {
    font-weight: bold;
    color: rgb(163, 4, 4);
    margin: 0;
    padding: 0;
}
</style>