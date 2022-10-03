<template>
    <!-- Modal -->
    <div class="modal fade" id="productRemoval" tabindex="-1" aria-labelledby="productRemovalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <input type="hidden" id="product-id-to-remove-input">

            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="productRemovalLabel">Produto</h5>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>

                <div class="modal-body">
                    Tem certeza que deseja excluir o produto <span id="product-name-removal"></span>?
                    Essa ação não pode ser desfeita.
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-danger" @click="removeProduct()">Remover produto</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
#product-name-removal {
    font-weight: bold;
}
</style>

<script>
export default {
    name: "modal-product",
    methods: {
        async removeProduct() {
            const url = "http://localhost/product/" + document.getElementById("product-id-to-remove-input").value;
            const response = await fetch(url, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    'Access-Control-Allow-Origin': '*',
                    'Access-Control-Allow-Methods': '*',
                    'Access-Control-Request-Headers': 'Content-Type, Authorization',
                    'Access-Control-Allow-Methods': 'PUT, POST, GET, DELETE, OPTIONS'
                }
            });

            const data = await response.json();
            window.location.reload();
        }
    }
};
</script>