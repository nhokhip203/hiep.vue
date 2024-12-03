<script setup>
import { useRoute } from 'vue-router';
import { reactive,ref,onMounted,computed } from 'vue';
import axios from 'axios';
import { RouterLink } from 'vue-router';
let url ="http://localhost/vuejs-server/api.php/cart-detail";
let urlUp="http://localhost/vuejs-server/api.php/cart-update";
const cart = ref({});
const cartDetail = ref([]);
const callAPI = async ()=>{
    let user_id =JSON.parse(localStorage.getItem("userLogin")).id;
    try {
        let response =await axios.get(url+"/?user_id="+user_id);
        if(response.data.status){
            cart.value=response.data.cart;
            cartDetail.value=response.data.cart_details;
        }
    } catch (error) {
        console.log(error);
    }
};
const converPrice = (number) => {
    return number.toLocaleString("vi-VN")
};
const handleSubmitt=()=>{};


const tptaPrice = computed(()=>{
    let total = 0;
    cartDetail.value.forEach((item) =>{
        total=total+item.price*item.quantity;
    });
    return total;
});
const handleCartP = async(action,product_id)=>{
    try {
        let link =urlUp+"?cart_id="+cart.value.id + "&action="+action+"&product_id="+product_id;
        let response= await axios.get(link);
        if(response.status==200){
            alert("Cập nhập giỏ hàng thành công");
            callAPI();
        }
    } catch (error) {
        console.log(error);
    }
};
onMounted(()=>{
    callAPI();
});
</script>
<template>
    <div class="row main-web justify-content-center mt-5">
        <div class="col-8">
            <h1>Giỏ hàng</h1>
    <table class="table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên sản phẩm</th>
                <th>Gía sản phẩm</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="cartDetail.length==0">
                <td colspan="6" >
                    <p>Không có sản phẩm trong giỏ hàng</p>
                    <RouterLink to="/">Tiếp tục mua hàng</RouterLink>
                </td>
            </tr>
            <tr v-else v-for="(item,index) in cartDetail" :key="index">
                <td>{{ index+1 }}</td>
                <td>{{ item.product_name }}</td>
                <td>{{ converPrice(Number(item.price)) }} VNĐ</td>
                <td>
                    <button @click="handleCartP('decrease',item.product_id)">-</button>
                    {{ item.quantity }}
                    <button @click="handleCartP('increase',item.product_id)">+</button>
                </td>
                <td>{{ converPrice(Number(item.price) * Number(item.quantity)) }} VNĐ</td>
                <td>
                    <button @click="handleCartP('delete',item.product_id)" class="btn btn-danger">Xóa</button>
                </td>
            </tr>
            <tr v-if="cartDetail.length!=0">
                <td colspan="4"></td>
                <td>Tổng tiền: {{ converPrice(tptaPrice) }} VNĐ</td>
                <td>
                    <RouterLink to="/thanhtoan" class="btn btn-warning">Thanh toán</RouterLink>
                </td>
            </tr>
        </tbody>
    </table>

</div>
</div>
</template>