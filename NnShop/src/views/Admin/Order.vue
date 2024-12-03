<script setup>
import axios from 'axios';
import { ref, onMounted, reactive } from 'vue';
import { RouterLink } from 'vue-router';
let urlOrderAll = "http://localhost/vuejs-server/api.php/show_order_admin";
let urlChang = "http://localhost/vuejs-server/api.php/change-status";
let orderList = ref([]);
const statusOrder = reactive({});
const callAll = async () => {
    try {
        let response = await axios.get(urlOrderAll);
        if (response.status == 200) {
            orderList.value = response.data;
            orderList.value.forEach((item) => {
                statusOrder[item.order_id] = item.status;
            });
        }
    } catch (error) {
        console.log(error);

    }
};
const converPrice = (number) => {
    return number.toLocaleString("vi-VN")
};
const conveDate = function (input) {
    const datePa = input.split("-");
    if (datePa.length !== 3) return "Looix";
    const year = datePa[0];
    const moth = datePa[1];
    const day = datePa[2];
    return `${day}/${moth}/${year}`;

};
const handleOrderDeta = async (orderId, newStatus) => {
    try {
        let response = await axios.get(urlChang + "?order_id=" + orderId + "&status_code=" + newStatus);
        if (response.status == 200) {
            alert("Cập nhật thành công");
            callAll();
        }
    } catch (error) {
        console.log(error);

    }
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
                    <th>Tên khách hàng</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Hiện thị</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in orderList" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>{{ item.customer_name }}</td>
                    <td>{{ item.email }}</td>
                    <td>{{ item.phone }}</td>
                    <td>{{ conveDate(item.order_date) }}</td>
                    <td>{{ converPrice(item.total_price) }} VNĐ</td>
                    <td>
                        <select class="form-control" v-model="statusOrder[item.order_id]"
                            @change="handleOrderDeta(item.order_id, statusOrder[item.order_id])">
                            <option value="1">Chưa xác nhận</option>
                            <option value="2"> Đã xác nhận</option>
                            <option value="3">Đã hủy</option>
                            <option value="4">Đang giao hàng</option>
                            <option value="5">Đã giao hàng</option>
                        </select>
                    </td>
                    <td>
                        <span v-if="item.status == 1" class="badge text-bg-secondary">Chưa xác nhận</span>
                        <span v-else-if="item.status == 2" class="badge text-bg-primary"> Đã xác nhận</span>
                        <span v-else-if="item.status == 3" class="badge text-bg-danger">Đã hủy</span>
                        <span v-else-if="item.status == 4" class="badge text-bg-warning">Đang giao hàng</span>
                        <span v-else class="badge text-bg-success">Đã giao hàng</span>

                    </td>
                    <td>
                        <RouterLink :to="`/orderdetail/${item.order_id}:order-id`" class="btn btn-sm btn-info">Xem chi
                            tiết</RouterLink>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<style scoped lang="css"></style>