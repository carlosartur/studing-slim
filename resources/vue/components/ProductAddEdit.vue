<template>
    <!-- Modal -->
    <div class="modal fade" id="editAddProductModal" tabindex="-1" aria-labelledby="editAddProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAddProductModalLabel">Produto</h5>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="product-id-input">

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="input-product-name-group">
                            Nome&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </span>
                        <input type="text" class="form-control" aria-label="Nome Produto" id="product-name-input"
                            aria-describedby="input-product-name-group">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="input-product-stock-group">
                            Estoque&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </span>
                        <input type="number" min="0" class="form-control" aria-label="Estoque" id="product-stock-input"
                            aria-describedby="input-product-stock-group">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="input-product-price-group">
                            Preço
                        </span>
                        <span class="input-group-text">R$</span>
                        <input type="number" min="0" step="0.01" class="form-control" aria-label="Preço"
                            id="product-price-input" aria-describedby="input-product-price-group">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" @click="saveProduct()">Salvar produto</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

class Product {
    name = "";
    price = 0;
    stock = 0;
    method = "POST";

    constructor(name, price, stock, id) {
        this.name = name;
        this.price = price * 100;
        this.stock = stock;
        this.id = id;
        if (this.id) {
            this.method = "PUT";
        }
    }

    async saveProduct() {
        try {
            const method = this.method;
            const url = "http://localhost/product" + (this.id ? `/${this.id}` : "");

            const response = await fetch(url, {
                method,
                headers: {
                    "Content-Type": "application/json",
                    'Access-Control-Allow-Origin': '*',
                    'Access-Control-Allow-Methods': '*',
                    'Access-Control-Request-Headers': 'Content-Type, Authorization',
                    'Access-Control-Allow-Methods': 'PUT, POST, GET, DELETE, OPTIONS'
                },
                body: JSON.stringify(this),
            });

            const data = await response.json();
            window.location.reload();
        } catch (error) {
            alert("Não foi possível salvar o produto.");
            console.error(error);
        }
    }

    toJSON() {
        let data = Object.assign({}, this);
        delete data["method"];
        return data;
    }
}

export default {
    name: "modal-product",
    methods: {
        saveProduct() {
            const prod = new Product(
                document.getElementById("product-name-input").value,
                document.getElementById("product-price-input").value,
                document.getElementById("product-stock-input").value,
                document.getElementById("product-id-input").value,
            );
            prod.saveProduct();
        }
    }
};
</script>