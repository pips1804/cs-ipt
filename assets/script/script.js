// Updating sidebar
$(document).ready(function () {
  // Get current page from URL
  let urlParams = new URLSearchParams(window.location.search);
  let currentPage = urlParams.get("page") || "dashboard"; // Default to dashboard if no page is set

  // Remove active class from all links and add it to the current page
  $(".sidebar a").removeClass("active");
  $(".sidebar a[href='home.php?page=" + currentPage + "']").addClass("active");

  // Handle sidebar update when clicking a link
  $(".sidebar a").click(function (e) {
    e.preventDefault(); // Prevent full page reload

    let page = $(this).attr("href").split("=")[1]; // Extract page name from URL
    window.history.pushState({}, "", "home.php?page=" + page); // Update URL

    $(".sidebar a").removeClass("active");
    $(this).addClass("active");

    // Load new content dynamically
    $(".main-content").load(page + ".php");
  });

  // Handle back/forward navigation
  window.onpopstate = function () {
    let newPage =
      new URLSearchParams(window.location.search).get("page") || "dashboard";
    $(".sidebar a").removeClass("active");
    $(".sidebar a[href='home.php?page=" + newPage + "']").addClass("active");
    $(".main-content").load(newPage + ".php");
  };
});

function fetchProducts() {
  $.get("../controllers/get_products.php", function (data) {
    $("#productList").html(data); // Update the product list without reloading
    attachEventListeners(); // Reattach event listeners to new elements
  });
}

function attachEventListeners() {
  $(".edit-btn").click(function () {
    $("#productId").val($(this).data("id"));
    $("#productName").val($(this).data("name"));
    $("#productDescription").val($(this).data("description"));
    $("#productStock").val($(this).data("stock"));
    $("#productPrice").val($(this).data("price"));
    $("#productCategory").val($(this).data("category"));
    $("#productModalLabel").text("Edit Product");
    $("#productModal").modal("show");
  });

  $(".delete-btn").click(function () {
    let productId = $(this).data("id");

    if (confirm("Are you sure you want to delete this product?")) {
      $.post(
        "../controllers/delete_product.php",
        { id: productId },
        function (response) {
          alert(response); // Show response message
          fetchProducts(); // Fetch updated product list
        }
      );
    }
  });
}

// Initial attachment when the page loads
attachEventListeners();
