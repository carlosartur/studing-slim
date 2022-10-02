<template>
    <div class="container products">
        <div class="card text-center bg-dark">
            <div class="card-header">
                <h3 class="text-center">Lista de produtos</h3>
            </div>
            <div class="card-body">
                <div class="float-end">
                    <div class="btn-group" role="group" aria-label="Basic outlined example">
                        <button type="button" class="btn btn-success" @click="editAddModal(modal)">Adicionar
                            produto</button>
                    </div>
                </div>
                <br>
                <section>
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Slug</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Criado em</th>
                                <th>Modificado em</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(product, index) in products">
                                <td>{{ product.name }}</td>
                                <td>{{ product.slug }}</td>
                                <td>
                                    {{
                                    (product.price / 100).toLocaleString("pt-BR", {
                                        style:"currency",
                                        currency:"BRL"
                                    })
                                    }}
                                </td>
                                <td>{{ product.stock }}</td>
                                <td>{{ (new Date(product.created_at.date)).toLocaleString() }}</td>
                                <td>{{ (new Date(product.updated_at.date)).toLocaleString() }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success edit-button"
                                            @click="editAddModal(modal, product)">Editar</button>
                                        <button type="button" class="btn btn-danger remove-button"
                                            @click="removeModal(modal, product)">&times;</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </div>
            <div class="card-footer text-muted">
                {{ totalOfProducts }} {{ totalOfProducts > 1 ? "produtos" : "produto" }}
            </div>
        </div>
    </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
    name: "list-products",
    methods: {
        editAddModal: function (modal, product = null) {
            modal.show();

            if (!product) {
                document.getElementById("product-name-input").value = "";
                document.getElementById("product-price-input").value = "";
                document.getElementById("product-stock-input").value = "";
                document.getElementById("product-id-input").value = "";
                return;
            }

            document.getElementById("product-name-input").value = product.name;
            document.getElementById("product-price-input").value = product.price / 100;
            document.getElementById("product-stock-input").value = product.stock;
            document.getElementById("product-id-input").value = product.id;
        },
        removeModal: (modal, product) => {

        },
    },
    data() {
        return {
            products: null,
            totalOfProducts: null,
            modal: null
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
        this.totalOfProducts = data.length;
    },
    mounted() {
        this.modal = new Modal(document.getElementById('editAddProductModal'));
    }
};
</script>