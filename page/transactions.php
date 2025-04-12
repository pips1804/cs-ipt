<h2 class="mb-4">TRANSACTIONS</h2>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Action</th>
                <th>Description</th>
                <th>Username</th>
                <th>Date & Time</th>
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
                        <tr>
                            <td>${tx.transact_ID}</td>
                            <td>${tx.action}</td>
                            <td>${tx.description}</td>
                            <td>${tx.user}</td>
                            <td>${tx.timestamp}</td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center">No transactions found.</td></tr>`;
            }
        })
        .catch(err => {
            console.error('Failed to fetch transactions:', err);
            document.getElementById('transactionsBody').innerHTML =
                '<tr><td colspan="5" class="text-center text-danger">Error loading transactions</td></tr>';
        });
</script>
