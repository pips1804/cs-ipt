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


