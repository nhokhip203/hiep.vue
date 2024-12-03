<script setup>
let url = "http://localhost/vuejs-server/api.php/products";
let urlCate = "http://localhost/vuejs-server/api.php/categories";
let urlImage = "http://localhost/vuejs-server/uploads/product/";
import { RouterLink } from 'vue-router';
import { ref, onMounted,watch } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';
const listProduct = ref([]);
const route = useRoute();
const callAPI = async () => {
    try {
        let urll =url+'?idCategory='+ route.params.idCategory;
        let response = await axios.get(urll);
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
watch(() => route.params.idCategory,(newIdCategory) =>{callAPI();}
);
</script>
<template>
    <div class="row main-web">
        <div class="col-8">
            <div class="d-flex flex-wrap" v-if="listProduct.length > 0">
                <div class="card" style="width: 18rem;" v-for="(product, index) in listProduct" :key="index">
                    <img :src="urlImage + product.image" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">{{ product.name }}</h5>
                        <p class="text-price">{{ converPrice(Number(product.price)) }} VNĐ</p>
                        <p class="card-text">{{ product.description }}</p>
                        <a href="#" class="btn btn-primary">Mua hàng</a>
                    </div>
                </div>
            </div>
            <div v-else class="alert alert-danger mt-4" role="alert">
                Không có sản phẩm
            </div>
        </div>
        <div class="col-3 offset-1">
            <div class="card">
                <div class="card-header">Danh sách danh mục</div>
                    <div class="list-group">
                        <RouterLink
                        v-for="(category,index) in listCategory"
                        :key="index"
                        :to="`/san-pham/${category.id}`"
                        class="list-group-item list-group-item-action"
                        :class="{
                            active: category.id == route.params.idCategory,
                        }">
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