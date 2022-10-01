<template>
    <table class="products">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Created at</th>
                <th>Updated at</th>
                <th style="min-width: 150px;"></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(product, index) in products">
                <td>{{ product.name }}</td>
                <td>{{ product.slug }}</td>
                <td>{{ (product.price / 100).toLocaleString("pt-BR", {style:"currency", currency:"BRL"}) }}</td>
                <td>{{ product.stock }}</td>
                <td>{{ (new Date(product.created_at.date)).toLocaleString() }}</td>
                <td>{{ (new Date(product.updated_at.date)).toLocaleString() }}</td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="button edit-button" @click="edit(product)">Editar</button>
                        <button type="button" class="button remove-button" @click="edit(product)">&times;</button>
                    </div>
                    <p style="clear:both"></p>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    name: "list-products",
    methods: {
        edit: function (product) {
            console.log(product.name);
            alert(product.name);
        }
    },
    data() {
        return {
            products: null
        };
    },
    async created() {
        // GET request using fetch with async/await
        const response = await fetch("http://localhost/product", {
            headers: {
                "Content-Type": "application/json",
                'Access-Control-Allow-Origin': '*',
                'Access-Control-Allow-Methods': '*',
                'Access-Control-Request-Headers': 'Content-Type, Authorization',
            }
        });

        const data = await response.json();
        this.products = data;
    }
};
</script>

<style>
.edit-button {
    background-color: #4CAF50;
}

.edit-button:hover {
    box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
    background-color: #2d682f;
}

.button {
    display: inline-block;
    border: none;
    color: white;
    padding: 10px 10px;
    text-align: center;
    border-radius: 3px;
}

.button:active {
    transform: translateY(1px);
}

.btn-group .button {
    display: inline-block;
    cursor: pointer;
}

.remove-button {
    background-color: rgb(192, 15, 15);
}
</style>