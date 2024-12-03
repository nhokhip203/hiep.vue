<script setup>
let url = "http://localhost/vuejs-server/api.php/categories";
import { useRoute, useRouter } from 'vue-router';
import { ref, onMounted } from "vue";
import axios from 'axios';
const route = useRoute()
const router = useRouter();
console.log(route.params.idCategory);


const name = ref("")
const description = ref("");

onMounted(async () => {
    let response = await axios.get(url + "/" + route.params.idCategory)
    if (response) {
        name.value = response.data.name
        description.value = response.data.description;
    }
});
const handleSubmit = async () => {
    checkvalidate();
    if (nameError.value == "" && descriptionError.value == "") {
        let formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("name", name.value);
        formData.append("description", description.value);


        let response = await axios.post(
            url + "/" + route.params.idCategory,
            formData,
            {
                headers: {
                    "Content-Type": "mutipart/form-data",
                },
            }
        );
        if (response) {
            alert(response.data.message);
            router.push("/admin/category");
        }

    }

};


const nameError = ref("");
const descriptionError = ref("");

const checkvalidate = () => {
    if (name.value == "") {
        nameError.value = "Không được để trống name"
    } else {
        nameError.value = ""

    }
    if (description.value == "") {
        descriptionError.value = "Không được để trống mô tả danh mục"

    } else {
        descriptionError.value = ""

    }
};

</script>


<template>
    <div class="p-4" style="min-height: 800px;">
        <h1>TRANG CHỈNH SỬA</h1>
        <form @submit.prevent="handleSubmit">
            <div class="mb-3">
                <label for="name">Name</label>
                <input type="text" placeholder="Tên Danh Mục" id="name" v-model="name" class="form-control">

                <span v-if="nameError != ''" class="text-danger">{{ nameError }}</span>
            </div>
            <div class="mb-3">
                <label for="description">Description</label>
                <input type="text" placeholder="Mô tả Danh Mục" id="description" v-model="description"
                    class="form-control">
                <span v-if="descriptionError != ''" class="text-danger">{{ descriptionError }}</span>
            </div>
            <button class="btn btn-warning">Chỉnh sửa</button>

        </form>
    </div>
</template>