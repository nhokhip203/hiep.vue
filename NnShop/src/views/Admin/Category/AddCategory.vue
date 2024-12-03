<script setup>
import { handleError, ref } from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';
const router = useRouter();
let url = 'http://localhost/vuejs-server/api.php/categories';


const name = ref("")
const description = ref("")
const handleSubmit = async () => {
    checkvalidate();
    if (nameError.value == "" && descriptionError.value == "") {
        let formData = new FormData();
        formData.append("name", name.value);
        formData.append("description", description.value);

        let response = await axios.post(url, formData, {
            headers: {
                "Content-Type": "mutipart/form-data",
            },
        });
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
        <h1>Trang thêm danh mục</h1>
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
            <button class="btn btn-success">Thêm Mới</button>

        </form>
    </div>
</template>