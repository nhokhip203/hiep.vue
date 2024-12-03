<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import {useRouter} from 'vue-router';
import axios from 'axios';
let urlCartDe = "http://localhost/vuejs-server/api.php/cart-detail";
let urlCheckOut = "http://localhost/vuejs-server/api.php/check-out";
const name = ref("");
const address = ref("");
const phone = ref("");
const email = ref("");
const cartDetai = ref([]);
const router = useRouter();
const callAll = async () => {
    try {
        let user_id = JSON.parse(localStorage.getItem("userLogin")).id;
        let response = await axios.get(urlCartDe + "?user_id=" + user_id);
        if (response.status == 200) {
            cartDetai.value = response.data.cart_details;
        }
    } catch (error) {
        console.log(error);

    }
}
onMounted(() => {
    callAll();
});
const converPrice = (number) => {
    return number.toLocaleString("vi-VN")
}
const handleSubmitt = async () => {


    try {
        let user_id = JSON.parse(localStorage.getItem("userLogin")).id;
        let formData = new FormData();
        formData.append("user_id", user_id)
        formData.append("name", name.value)
        formData.append("address",address.value)
        formData.append("phone",phone.value)
        formData.append("email",email.value)

        let response = await axios.post(urlCheckOut, formData);
        
        if (response.status == 200) {
            alert("Thanh toán thành công");
            router.push("/donhang");
        }
    } catch (error) {
        console.log(error);

    }
}
</script>
<template>
    <div class="row main-web justify-content-center mt-5">
        <div class="col-8">
            <div class="container">
                <div class="row">
                    <div class="col-7">
                        <h4>Thông tin thanh toán</h4>
                        <form @submit.prevent="handleSubmitt">
                            <label for="name">Name</label>
                            <input type="text" placeholder="Name" id="name" v-model="name" class="form-control">
                            <label for="address">Address</label>
                            <input type="text" placeholder="Address" id="address" v-model="address"
                                class="form-control">
                            <label for="email">Email</label>
                            <input type="text" placeholder="Email" id="email" v-model="email" class="form-control">
                            <label for="phone">Phone</label>
                            <input type="text" placeholder="Phone" id="phone" v-model="phone" class="form-control">
                            <br>
                            <button class="btn btn-success">Xác nhận thánh toán</button>
                        </form>
                    </div>
                    <div class="col-5">
                        <h3>Danh sách sản phẩm</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Name</th>
                                    <th>Gía</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in cartDetai" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.product_name }}</td>
                                    <td>{{ converPrice(Number(item.price)) }}</td>
                                    <td>{{ item.quantity }}</td>
                                    <td>{{ converPrice(Number(item.price) * Number(item.quantity)) }} VNĐ</td>
                                </tr>
                            </tbody>
                        </table>


                    </div>

                </div>
            </div>
        </div>
    </div>

</template>