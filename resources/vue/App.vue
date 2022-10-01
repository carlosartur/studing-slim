<script setup>
import ProductList from "./components/ProductList.vue";
</script>

<template>
    <div class="container">
        <ProductList></ProductList>
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

table {
    border: 2px solid #FFF;
    border-radius: 6px;
    padding: 0px;
    border-spacing: 0
}

td,
th {
    padding: 10px;
    border: 2px solid #FFF;
}

tr:nth-child(even) {
    background-color: #404040;
}
</style>