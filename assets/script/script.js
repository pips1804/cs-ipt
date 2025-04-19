$(document).ready(function () {
  // Load page with animation and pushState
  function loadPage(page) {
    if (!page) return;

    $("#loadingOverlay").fadeIn();

    $(".sidebar a").removeClass("active");
    $(".sidebar a[href='home.php?page=" + page + "']").addClass("active");

    $(".main-content").html("");

    setTimeout(() => {
      $(".main-content").load(page + ".php", function () {
        $("#loadingOverlay").fadeOut();
        history.pushState(null, "", "home.php?page=" + page);

        // Reattach events if it's the products page
        if (page === "products") {
          attachEventListeners();
        }
      });
    }, 500);
  }

  // Handle sidebar link clicks
  $(".sidebar a").click(function (e) {
    const href = $(this).attr("href");

    // Let logout link work normally
    if (href.includes("logout=true")) return;

    e.preventDefault();
    const page = href.split("=")[1];
    loadPage(page);
  });

  // Handle back/forward navigation
  window.onpopstate = function () {
    const newPage =
      new URLSearchParams(window.location.search).get("page") || "dashboard";
    loadPage(newPage);
  };

  // Initial load
  const initialPage =
    new URLSearchParams(window.location.search).get("page") || "dashboard";
  loadPage(initialPage);

  // Fetch updated product list
  function fetchProducts() {
    $.get("../controllers/get_products.php", function (data) {
      $("#productList").html(data);
      attachEventListeners(); // Reattach after fetch
    });
  }

  // Attach events to product edit/delete buttons
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
      const productId = $(this).data("id");

      if (confirm("Are you sure you want to delete this product?")) {
        $.post(
          "../controllers/delete_product.php",
          { id: productId },
          function (response) {
            alert(response);
            fetchProducts(); // Refresh list after deletion
          }
        );
      }
    });
  }

  // Initial check if on products page
  if (initialPage === "products") {
    attachEventListeners();
  }
});
