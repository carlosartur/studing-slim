<script setup>
import ProductList from "./components/ProductList.vue";
import ProductAddEditVue from "./components/ProductAddEdit.vue";
import { RouterView } from "vue-router";
import "bootstrap/dist/css/bootstrap.min.css";
</script>

<template>
    <div class="container">
        <ProductList></ProductList>
        <ProductAddEditVue></ProductAddEditVue>
    </div>
</template>

<script>
export default {
    name: "App",
}
</script>

<style>
body {
    background-color: #272822;
    color: white;
}
</style>