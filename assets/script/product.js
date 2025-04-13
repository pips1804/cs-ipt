document.addEventListener("DOMContentLoaded", () => {
  console.log("product.js loaded");

  let currentUsername = "";

  fetch("../auth/get_user_info.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        currentUsername = data.user.username;
        console.log("Logged in as:", currentUsername);
      } else {
        console.warn("User info not found:", data.message);
      }
    });

  // Make global
  window.togglePriceFilter = function () {
    let value = document.getElementById("priceFilterToggle").value;
    let section = document.getElementById("priceFilterSection");
    section.style.display = value === "on" ? "block" : "none";
    window.filterProducts(); // also global
  };

  // Make global
  window.updatePriceDisplay = function () {
    let priceVal = document.getElementById("priceRange").value;
    document.getElementById("priceValue").innerText = priceVal;
  };

  // Make global
  window.filterProducts = function () {
    let searchValue = document.getElementById("search").value.toLowerCase();
    let stockFilter = document.getElementById("stockFilter").value;
    let priceFilterActive =
      document.getElementById("priceFilterToggle").value === "on";
    let maxPrice =
      parseFloat(document.getElementById("priceRange").value) || Infinity;

    let products = document.querySelectorAll(".product-item");

    products.forEach((product) => {
      let title = product
        .querySelector(".card-title")
        .textContent.toLowerCase();
      let description = product
        .querySelector(".card-text")
        .textContent.toLowerCase();
      let stockText = product.querySelectorAll(".card-text")[1].textContent;
      let priceMatch = stockText.match(/Price: ₱([\d,\.]+)/);
      let stockMatch = stockText.match(/Stock: (\d+)/);

      let stock = stockMatch ? parseInt(stockMatch[1]) : 0;
      let price = priceMatch ? parseFloat(priceMatch[1].replace(/,/g, "")) : 0;

      let isVisible = true;
      if (!title.includes(searchValue) && !description.includes(searchValue))
        isVisible = false;
      if (stockFilter === "in" && stock <= 0) isVisible = false;
      if (stockFilter === "out" && stock > 0) isVisible = false;
      if (priceFilterActive && price > maxPrice) isVisible = false;

      product.style.display = isVisible ? "" : "none";
    });
  };

  // Make global
  window.deleteProduct = function (productId) {
    if (confirm("Are you sure you want to delete this product?")) {
      fetch(`http://localhost:5000/api/products/${productId}`, {
        method: "DELETE",
      })
        .then((response) => response.json())
        .then((data) => {
          alert(data.message);
          if (data.message.includes("deleted")) {
            const logDescription = `Deleted product with ID: ${productId}`;

            fetch("../controllers/log_transaction.php", {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: new URLSearchParams({
                action: "Deleted product",
                username: currentUsername,
                description: logDescription,
              }),
            })
              .then((response) => response.json())
              .then((logData) => {
                console.log("Log:", logData.message);
                location.reload();
              });
          }
        })
        .catch((error) => {
          alert("Error deleting product.");
          console.error(error);
        });
    }
  };

  // Make global
  window.openEditModal = function (product) {
    document.getElementById("editProductId").value = product.productID;
    document.getElementById("editItemName").value = product.itemName;
    document.getElementById("editDescription").value = product.description;
    document.getElementById("editUnitPrice").value = product.unitPrice;
    document.getElementById("editStock").value = product.stock;
    let modal = new bootstrap.Modal(
      document.getElementById("editProductModal")
    );
    modal.show();
  };

  // Make global
  window.saveProductChanges = function () {
    const id = document.getElementById("editProductId").value;
    const updatedData = {
      itemName: document.getElementById("editItemName").value,
      description: document.getElementById("editDescription").value,
      unitPrice: parseFloat(document.getElementById("editUnitPrice").value),
      stock: parseInt(document.getElementById("editStock").value),
    };

    fetch(`http://localhost:5000/api/products/${id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(updatedData),
    })
      .then((res) => res.json())
      .then((data) => {
        alert(data.message);
        if (data.message === "Product updated successfully") {
          const logDescription = `
                  Updated product ID: ${id},
                  New Name: ${updatedData.itemName},
                  New Description: ${updatedData.description},
                  New Price: ₱${updatedData.unitPrice.toFixed(2)},
                  New Stock: ${updatedData.stock}
              `.trim();

          fetch("../controllers/log_transaction.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
              action: "Edited product",
              username: currentUsername,
              description: logDescription,
            }),
          })
            .then((response) => response.json())
            .then((logData) => {
              console.log("Log:", logData.message);
              bootstrap.Modal.getInstance(
                document.getElementById("editProductModal")
              ).hide();
              location.reload();
            });
        }
      })
      .catch((err) => {
        alert("Update failed.");
        console.error(err);
      });
  };

  // Make global
  window.addProduct = function () {
    const newProductData = {
      itemNumber: document.getElementById("addItemNumber").value,
      itemName: document.getElementById("addItemName").value,
      description: document.getElementById("addDescription").value,
      unitPrice: parseFloat(document.getElementById("addUnitPrice").value),
      stock: parseInt(document.getElementById("addStock").value),
      discount: parseFloat(document.getElementById("addDiscount").value) || 0,
      status: document.getElementById("addStatus").value,
    };

    const formData = new FormData();
    formData.append("itemNumber", newProductData.itemNumber);
    formData.append("itemName", newProductData.itemName);
    formData.append("description", newProductData.description);
    formData.append("unitPrice", newProductData.unitPrice);
    formData.append("stock", newProductData.stock);
    formData.append("discount", newProductData.discount);
    formData.append("status", newProductData.status);

    const imageInput = document.getElementById("addImage");
    if (imageInput.files.length > 0) {
      formData.append("itemImage", imageInput.files[0]);
    }

    fetch("http://localhost:5000/api/add_product", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message);
        if (data.message === "Item added successfully!") {
          const logDescription = `
                  Item Number: ${newProductData.itemNumber},
                  Name: ${newProductData.itemName},
                  Description: ${newProductData.description},
                  Price: ₱${newProductData.unitPrice.toFixed(2)},
                  Stock: ${newProductData.stock},
                  Discount: ${newProductData.discount}%, 
                  Status: ${newProductData.status}
              `.trim();

          fetch("../controllers/log_transaction.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
              action: "Added product",
              description: logDescription,
              username: currentUsername,
            }),
          })
            .then((response) => response.json())
            .then((logData) => {
              console.log("Log:", logData.message);
              location.reload();
            });
        }
      })
      .catch((error) => {
        alert("Error adding product.");
        console.error(error);
      });
  };
});
