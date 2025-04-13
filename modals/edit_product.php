<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editProductId">
                <div class="mb-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" id="editItemName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea id="editDescription" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" id="editUnitPrice" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" id="editStock" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="saveProductChanges()">Save Changes</button>
            </div>
        </div>
    </div>
</div>