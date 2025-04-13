<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Item Number</label>
                    <input type="text" id="addItemNumber" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" id="addItemName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea id="addDescription" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" id="addUnitPrice" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" id="addStock" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Discount</label>
                    <input type="number" id="addDiscount" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select id="addStatus" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <input type="file" id="addImage" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="addProduct()">Add Product</button>
            </div>
        </div>
    </div>
</div>