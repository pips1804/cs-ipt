<?php
$apiUrl = "http://localhost:5000/api/products";
$response = file_get_contents($apiUrl);
$products = json_decode($response, true);
?>

<div class="container mt-5">
    <h2 class="mb-4">QR CODE STOCK MANAGEMENT</h2>

    <!-- Webcam Scanner -->
    <div class="mb-4">
        <h5>Scan QR Code</h5>

        <div id="preview" style="width: 400px; height: 300px;"></div>
        <div class="mt-2">
            <button class="btn btn-success" onclick="startScanner()">Start Scanner</button>
            <button class="btn btn-danger" onclick="stopScanner()">Stop Scanner</button>
        </div>
    </div>

    <!-- Drag Zone -->
    <div class="mb-4">
        <h5>Drag QR Code Here to Add Stock</h5>
        <div id="dropZone"
            class="border p-3 text-center"
            ondrop="handleDrop(event)"
            ondragover="event.preventDefault()">
            Drop QR Code here
        </div>
    </div>

    <!-- Products with QR Display -->
    <div class="row" id="qr-product-list">
        <?php foreach ($products as $product): ?>
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h6><?= htmlspecialchars($product['itemName']) ?></h6>
                        <?php if (!empty($product['qrFullURL'])): ?>
                            <img src="<?= $product['qrFullURL'] ?>"
                                class="qr-draggable img-fluid"
                                draggable="true"
                                ondragstart="drag(event)"
                                data-product-id="<?= $product['productID'] ?>">
                        <?php else: ?>
                            <p>No QR Code</p>
                        <?php endif; ?>
                        <p><small>Stock: <?= $product['stock'] ?></small></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    // Make PHP products accessible to JS
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

    const productList = <?php echo json_encode($products); ?>;

    function drag(event) {
        const productID = event.target.dataset.productId;
        event.dataTransfer.setData("text/plain", productID);
        console.log("Dragging product ID:", productID);
    }

    function handleDrop(event) {
        event.preventDefault();
        const productID = event.dataTransfer.getData("text/plain");
        const product = productList.find(p => p.productID == productID);

        if (!product) {
            alert("Product not found.");
            return;
        }

        const quantityStr = prompt(`Add stock to "${product.itemName}". Enter quantity:`);
        const quantity = parseInt(quantityStr);

        if (isNaN(quantity) || quantity <= 0) {
            alert("Invalid quantity.");
            return;
        }

        const updatedStock = parseInt(product.stock) + quantity;

        const updatedData = {
            itemName: product.itemName,
            description: product.description,
            unitPrice: parseFloat(product.unitPrice),
            stock: updatedStock
        };

        console.log("Sending updated product:", updatedData);

        fetch(`http://localhost:5000/api/products/${productID}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(updatedData)
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.message === "Product updated successfully") {
                    const logDescription = `Added ${quantity} to product ID: ${productID}`;

                    // Log to PHP backend
                    fetch("../controllers/log_transaction.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: new URLSearchParams({
                                action: "Added stock",
                                username: currentUsername,
                                description: logDescription
                            })
                        })
                        .then(response => response.json())
                        .then(logData => {
                            console.log("Log:", logData.message);
                            location.reload();
                        });
                }
            })
            .catch(err => {
                console.error("Error updating stock:", err);
                alert("Failed to update stock.");
            });
    }

    let html5QrCode; // Declare the variable outside the function

    function startScanner() {
        const qrRegionId = "preview"; // This is the div where the video feed will show up
        html5QrCode = new Html5Qrcode(qrRegionId);

        html5QrCode.start({
                facingMode: "environment"
            }, // Use environment camera (back camera)
            {
                fps: 10,
                qrbox: 250
            }, // Set frames per second and QR box size
            qrCodeMessage => {
                console.log("QR Code detected:", qrCodeMessage);
                alert(`Scanned: ${qrCodeMessage}`); // Show scanned QR code message

                // Assuming the QR code message contains just the number like "6"
                const qrCodeFileName = `${qrCodeMessage}.png`; // Append ".png" to the scanned number

                // Fetch product based on qrCode file name (e.g., "6.png")
                fetch(`http://localhost:5000/api/products/by-qrcode?qrCode=${qrCodeFileName}`)
                    .then(res => res.json())
                    .then(products => {
                        console.log("Fetched products:", products); // Log the entire response here
                        if (!products || products.length === 0) {
                            alert("Product not found.");
                            return;
                        }

                        const product = products[0]; // Assuming only one product is returned
                        const productID = product.productID;

                        console.log(productID);


                        const quantityStr = prompt("Enter quantity to add to stock:");
                        const quantity = parseInt(quantityStr);

                        if (isNaN(quantity) || quantity <= 0) {
                            alert("Invalid quantity.");
                            return;
                        }

                        const updatedStock = parseInt(product.stock) + quantity;
                        const updatedData = {
                            itemName: product.itemName,
                            description: product.description,
                            unitPrice: parseFloat(product.unitPrice),
                            stock: updatedStock
                        };

                        // Send PUT request to update stock
                        fetch(`http://localhost:5000/api/products/${productID}`, {
                                method: "PUT",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify(updatedData)
                            })
                            .then(res => res.json())
                            .then(data => {
                                alert(data.message);
                                if (data.message === "Product updated successfully") {
                                    const logDescription = `Added ${quantity} to product ID: ${productID}`;
                                    fetch("../controllers/log_transaction.php", {
                                            method: "POST",
                                            headers: {
                                                "Content-Type": "application/x-www-form-urlencoded"
                                            },
                                            body: new URLSearchParams({
                                                action: "Added stock",
                                                username: currentUsername, // Make sure this is defined
                                                description: logDescription
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(logData => {
                                            console.log("Log:", logData.message);
                                            location.reload(); // Refresh page after updating stock
                                        });
                                }
                            })
                            .catch(err => {
                                console.error("Error updating stock:", err);
                                alert("Failed to update stock.");
                            });
                    })
                    .catch(err => {
                        console.error("Error fetching product data:", err);
                        alert("Failed to fetch product details.");
                    });

                stopScanner(); // Optionally stop the scanner after detecting the QR code
            },
            errorMessage => {
                console.log("Error:", errorMessage);
            }
        ).catch(err => {
            console.error("Failed to start scanner:", err);
            alert("Failed to start scanner: " + err);
        });
    }

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                console.log("Scanner stopped");
            }).catch(err => {
                console.error("Error stopping the scanner:", err);
            });
        }
    }


    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
            });
        }
    }
</script>
