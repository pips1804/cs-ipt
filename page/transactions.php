<?php
require '../auth/verify_token.php';

if (!isset($_COOKIE['jwt'])) {
    header("Location: ../index.php");
    exit();
}

$decodedToken = verifyJWT($_COOKIE['jwt']);

if (!$decodedToken || $decodedToken['exp'] < time()) {
    setcookie("jwt", "", time() - 3600, "/", "", false, true);
    header("Location: ../index.php");
    exit();
}

$user_id = $decodedToken['id'];
$user_email = $decodedToken['email'];
?>

<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<style>
    .table-container {
        max-height: 800px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .table thead th {
        position: sticky;
        top: 0;
        background: linear-gradient(135deg, #04364A, #0D5975);
        color: white;
        z-index: 2;
        text-align: center;
    }

    th,
    td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .invalid-transaction {
        background-color: #f8d7da !important;
    }

    .valid-transaction {
        background-color: #d4edda !important;
    }

    .dashboard-title {
        font-size: 3rem;
        font-weight: 700;
        color: #333;
    }
</style>

<h2 class="mb-4 fw-bold text-dark text-center dashboard-title">Transactions</h2>

<div class="table-container">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Action</th>
                <th>Description</th>
                <th>Username</th>
                <th>Date & Time</th>
                <th>TX Hash</th>
                <th>Verify</th>
            </tr>
        </thead>
        <tbody id="transactionsBody">
            <!-- Transaction rows will be inserted here -->
        </tbody>
    </table>
</div>

<script>
    fetch('../controllers/view_transaction.php?action=get_all')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('transactionsBody');

            if (data.success && data.transactions.length > 0) {
                data.transactions.forEach(tx => {
                    const row = `
                    <tr data-id="${tx.id}">
                        <td>${tx.action}</td>
                        <td>${tx.description}</td>
                        <td>${tx.user}</td>
                        <td>${tx.timestamp}</td>
                        <td>
                            <div style="display: flex; gap: 5px; align-items: center;">
                                <span class="tx-hash" style="display:none;">${tx.tx_hash}</span>
                                <button class="btn btn-sm btn-primary toggle-hash">Show</button>
                                <button class="btn btn-sm btn-secondary copy-hash">Copy</button>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-success verify-btn"
                                data-user="${tx.user}"
                                data-action="${tx.action}"
                                data-description="${tx.description}"
                                data-timestamp="${tx.timestamp}"
                                data-tx_hash="${tx.tx_hash}">
                                Verify
                            </button>
                        </td>
                    </tr>
                `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });

                document.querySelectorAll('.toggle-hash').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const span = this.parentElement.querySelector('.tx-hash');
                        if (span.style.display === 'none') {
                            span.style.display = 'inline';
                            this.textContent = 'Hide';
                        } else {
                            span.style.display = 'none';
                            this.textContent = 'Show';
                        }
                    });
                });

                document.querySelectorAll('.copy-hash').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const span = this.parentElement.querySelector('.tx-hash');
                        navigator.clipboard.writeText(span.textContent).then(() => {
                            alert('TX Hash copied to clipboard!');
                        });
                    });
                });

                document.querySelectorAll('.verify-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const row = this.closest('tr');
                        row.classList.remove('valid-transaction', 'invalid-transaction');

                        const payload = {
                            user: this.dataset.user,
                            action: this.dataset.action,
                            description: this.dataset.description,
                            timestamp: this.dataset.timestamp,
                            tx_hash: this.dataset.tx_hash
                        };

                        fetch('../controllers/verify_transaction.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(payload)
                            })
                            .then(res => res.json())
                            .then(result => {
                                if (result.match) {
                                    row.classList.add('valid-transaction');
                                    alert('✅ Transaction is valid!');
                                } else {
                                    row.classList.add('invalid-transaction');
                                    alert('❌ Transaction is invalid!\nExpected: ' + result.recomputed_hash + '\nStored: ' + result.stored_hash);
                                }
                            })
                            .catch(err => {
                                console.error('Verification error:', err);
                                alert('❌ Error verifying transaction.');
                            });
                    });
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center">No transactions found.</td></tr>`;
            }
        })
        .catch(err => {
            console.error('Failed to fetch transactions:', err);
            document.getElementById('transactionsBody').innerHTML =
                '<tr><td colspan="6" class="text-center text-danger">Error loading transactions</td></tr>';
        });
</script>
