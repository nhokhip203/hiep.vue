<script setup>
import anh1 from "@/assets/nen3.jpg";
import anh2 from "@/assets/nen2.jpg";
import anh3 from "@/assets/nen5.jpg";
</script>
<template>
    <div id="carouselExample" class="carousel slide">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img :src="anh1" class="d-block w-100 img-header"  alt="..." >
                        </div>
                        <div class="carousel-item">
                            <img :src="anh2" class="d-block w-100 img-header" alt="...">
                        </div>
                        <div class="carousel-item">
                            <img :src="anh3" class="d-block w-100 img-header" alt="...">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
</template>
<style scoped lang="scss">
.img-header{
    height: 400px;
    object-fit: cover;
}
.main-web{
    min-height: 500px;
}
</style>