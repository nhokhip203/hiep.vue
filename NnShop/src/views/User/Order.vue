<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter,RouterLink } from "vue-router";

let urlChang = "http://localhost/vuejs-server/api.php/change-status";
let urlOrder = "http://localhost/vuejs-server/api.php/show_order";
const order = ref([]);
const callAll = async () => {
    try {
        let user_id = JSON.parse(localStorage.getItem("userLogin")).id;
        let response = await axios.get(urlOrder + "?user_id=" + user_id);
        if (response.status == 200) {
            order.value = response.data;
        }
    } catch (error) {
        console.log(error);

    }
};
const conveDate = function (input) {
    const datePa = input.split("-");
    if (datePa.length !== 3) return "Looix";
    const year = datePa[0];
    const moth = datePa[1];
    const day = datePa[2];
    return `${day}/${moth}/${year}`;

};
const converPrice = (number) => {
    return number.toLocaleString("vi-VN")
};
const handleHuy = async (order_id) => {
    const check = confirm("Bạn có muốn hủy đơn hàng không");
    if (check) {
        try {
            let response = await axios.get(urlChang + "?order_id=" + order_id + "&status_code=3");
            if (response.status == 200) {
                alert("Hủy đơn hàng thành công");
                callAll();
            }
        } catch (error) {
            console.log(error);
            
        }
    }
};
onMounted(() => {
    callAll();
});
</script>
<template>
    <div class="row main-web justify-content-center mt-5">
        <div class="col-8">
            <table class="table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Ngày đặt hàng</th>
                        <th>Trạng thái</th>
                        <th>Tổng tiền</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in order" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td>{{ conveDate(item.order_date) }}</td>
                        <td>
                            <span v-if="item.status == 1" class="badge text-bg-secondary">Chưa xác nhận</span>
                            <span v-else-if="item.status == 2" class="badge text-bg-primary"> Đã xác nhận</span>
                            <span v-else-if="item.status == 3" class="badge text-bg-danger">Đã hủy</span>
                            <span v-else-if="item.status == 4" class="badge text-bg-warning">Đang giao hàng</span>
                            <span v-else class="badge text-bg-success">Đã giao hàng</span>
                        </td>
                        <td>{{ converPrice(item.total_price) }} VND</td>
                        <td>
                            <RouterLink :to="`/chitietdonhang/${item.order_id}`" class="btn btn-info">Xem chi tiết</RouterLink>
                            <button v-if="item.status == 1" class="btn btn-danger" @click="handleHuy(item.order_id)">Hủy
                                đơn</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>