$(document).ready(function () {
  // Search Functionality
  $("#searchInput").on("keyup", function () {
    let value = $(this).val().toLowerCase();
    $(".product-item").each(function () {
      let found = $(this).text().toLowerCase().includes(value);
      $(this).toggle(found);
    });
  });

  // Category Filter
  $(document).on("click", ".category-btn", function (event) {
    event.preventDefault(); // Prevents page jump

    let category = $(this).data("category");

    // Update dropdown button text
    $("#categoryDropdown").text($(this).text());

    // Reset button styles
    $(".category-btn")
      .removeClass("btn-secondary")
      .addClass("btn-outline-primary");

    // Highlight the selected category
    $(this).removeClass("btn-outline-primary").addClass("btn-secondary");

    // Filter products
    if (category === "all") {
      $(".product-item").show();
    } else {
      $(".product-item").each(function () {
        $(this).toggle($(this).data("category") == category);
      });
    }
  });

  // Show modal for adding a new product
  $(document).on("click", "#addProductBtn", function () {
    $("#productForm")[0].reset();
    $("#productId").val("");
    $("#productModalLabel").text("Add Product");
    $("#productModal").modal("show");
  });

  // Show modal for editing a product
  $(document).on("click", ".edit-btn", function () {
    $("#productId").val($(this).data("id"));
    $("#productName").val($(this).data("name"));
    $("#productDescription").val($(this).data("description"));
    $("#productStock").val($(this).data("stock"));
    $("#productPrice").val($(this).data("price"));
    $("#productCategory").val($(this).data("category"));
    $("#productModalLabel").text("Edit Product");
    $("#productModal").modal("show");
  });

  // Handle form submission (Add/Edit Product)
  $("#productForm").submit(function (event) {
    event.preventDefault();

    let formData = $(this).serialize();
    let actionUrl = $("#productId").val()
      ? "../controllers/update_product.php"
      : "../controllers/add_product.php";

    $.post(actionUrl, formData, function (response) {
      console.log("Response from server:", response); // Debugging log
      $("#productModal").modal("hide");
      $("#productForm")[0].reset();
      fetchProducts();
    });
  });

  // Delete product
  $(document).on("click", ".delete-btn", function () {
    let productId = $(this).data("id");

    if (confirm("Are you sure you want to delete this product?")) {
      $.post(
        "../controllers/delete_product.php",
        {
          id: productId,
        },
        function (response) {
          alert(response);
          fetchProducts();
        }
      );
    }
  });

  // Fetch updated product list
  function fetchProducts() {
    $.get("../controllers/get_products.php", function (data) {
      console.log("Fetched Products:", data);
      $("#productList").html(data);
    });
  }
});

function attachEventListeners() {
  $(document)
    .off("click", ".edit-btn")
    .on("click", ".edit-btn", function () {
      $("#productId").val($(this).data("id"));
      $("#productName").val($(this).data("name"));
      $("#productDescription").val($(this).data("description"));
      $("#productStock").val($(this).data("stock"));
      $("#productPrice").val($(this).data("price"));
      $("#productCategory").val($(this).data("category"));
      $("#productModalLabel").text("Edit Product");
      $("#productModal").modal("show");
    });

  $(document)
    .off("click", ".delete-btn")
    .on("click", ".delete-btn", function () {
      let productId = $(this).data("id");
      if (confirm("Are you sure you want to delete this product?")) {
        $.post(
          "../controllers/delete_product.php",
          {
            id: productId,
          },
          function (response) {
            alert(response);
            fetchProducts();
          }
        );
      }
    });
}

// Initial event listener attachment
attachEventListeners();

document
  .getElementById("addCategoryBtn")
  .addEventListener("click", function () {
    var categoryModal = new bootstrap.Modal(
      document.getElementById("categoryModal")
    );
    categoryModal.show();
  });

document
  .getElementById("categoryForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    var categoryName = document.getElementById("categoryName").value;

    fetch("../controllers/add_category.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "category_name=" + encodeURIComponent(categoryName),
    })
      .then((response) => response.text())
      .then((data) => {
        alert(data); // Show response message
        location.reload(); // Reload to update categories
      });
  });

$(document).on("click", ".delete-category", function () {
  let categoryId = $(this).data("id");
  let categoryItem = $(this).closest("li");

  if (confirm("Are you sure you want to delete this category?")) {
    $.ajax({
      url: "../controllers/delete_category.php",
      type: "POST",
      data: { category_id: categoryId },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          categoryItem.remove(); // Remove category from dropdown
        } else {
          alert(response.message);
        }
      },
      error: function () {
        alert("An error occurred. Please try again.");
      },
    });
  }
});
