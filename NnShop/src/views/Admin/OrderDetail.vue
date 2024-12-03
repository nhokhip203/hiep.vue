<script setup>
import { ref, onMounted } from "vue";
import { useRoute, RouterLink } from "vue-router";
import axios from "axios";
let urlOrderDeta = "http://localhost/vuejs-server/api.php/order_detail";

const route = useRoute();
const orderDeta = ref([]);
const callAll = async () => {
    try {
        let response = await axios.get(urlOrderDeta + "?order_id=" + route.params.order_id);
        if (response.status == 200) {
            orderDeta.value = response.data;
            console.log(orderDeta.value);
        }
    } catch (error) {
        console.log(error);

    }
};
const converPrice = (number) => {
    return number.toLocaleString("vi-VN")
};
onMounted(() => {
    callAll();
});

</script>
<template>
    <div class="p-4" style="min-height: 800px;">
        <table class="table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên sản phẩm</th>
                    <th>Gía</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in orderDeta" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>
                        <RouterLink target="_blank" :to="`/san-pham-chi-tiet/${item.product_id}`">
                            {{ item.product_name }}
                        </RouterLink>
                    </td>
                    <td>{{ converPrice(Number(item.price)) }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>{{ converPrice(Number(item.price) * Number(item.quantity)) }} VNĐ</td>
                </tr>
            </tbody>
        </table>

    </div>
</template>