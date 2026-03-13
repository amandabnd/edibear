<?php
  session_start();
  require_once("../classes/class.user.php");
  require_once("../classes/class.header.php");

  $adminHeader = new HEADER("orders");
  $user = new USER();

  // Authentication check (keep consistent with other admin listing pages)
  if (!$user->is_loggedin()) {
    $user->doLogout();
  }

  // Summary counts from orders table
  try {
    $pdo = $user->getConnection();
    $summaryStmt = $pdo->query("
      SELECT 
        COUNT(*) AS total_orders,
        SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) AS failed
      FROM orders
    ");
    $summaryRow = $summaryStmt ? $summaryStmt->fetch(PDO::FETCH_ASSOC) : array(
      'total_orders' => 0,
      'completed'    => 0,
      'pending'      => 0,
      'failed'       => 0
    );
    $totalOrders   = (int) $summaryRow['total_orders'];
    $totalCompleted = (int) $summaryRow['completed'];
    $totalPending   = (int) $summaryRow['pending'];
    $totalFailed    = (int) $summaryRow['failed'];
  } catch (Exception $e) {
    $totalOrders = $totalCompleted = $totalPending = $totalFailed = 0;
  }

  // Returns not tracked yet
  $totalReturn = 0;

  // Search filter (by order number / date / customer)
  $search = isset($_GET['search']) ? trim($_GET['search']) : "";

  $query = "SELECT 
              id,
              order_number,
              first_name,
              last_name,
              email,
              mobile,
              payment_method,
              payment_status,
              subtotal,
              shipping,
              total,
              created_at
            FROM orders";

  $params = array();
  if ($search !== "") {
    $query .= " WHERE order_number LIKE :search
                OR email LIKE :search
                OR mobile LIKE :search
                OR first_name LIKE :search
                OR last_name LIKE :search
                OR DATE(created_at) LIKE :search";
    $params[':search'] = "%" . $search . "%";
  }

  $query .= " ORDER BY id DESC";

  try {
    $stmt = $user->runQuery($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (Exception $e) {
    $orders = array();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php echo $adminHeader->printAdminHeader(); ?>
  <style>
    .orders-title {
      font-size: 1.4rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #f97316;
      margin-bottom: 1.5rem;
    }

    .orders-summary-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
      gap: 1.2rem;
      margin-bottom: 2rem;
    }

    .orders-summary-card {
      background-color: #ffffff;
      border-radius: 18px;
      border: 1px solid #e5e7eb;
      padding: 28px 16px;
      text-align: center;
      box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
      min-height: 140px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .orders-summary-value {
      font-size: 1.9rem;
      font-weight: 700;
      color: #111827;
      margin-bottom: 4px;
    }

    .orders-summary-label {
      font-size: 0.8rem;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: #9ca3af;
    }

    .orders-filter-row {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.25rem;
      gap: 0.75rem;
    }

    .orders-search-label {
      font-size: 0.8rem;
      text-transform: uppercase;
      color: #9ca3af;
      margin-right: 0.5rem;
    }

    .orders-search-input {
      max-width: 260px;
    }

    .orders-export-select {
      max-width: 100px;
    }

    .orders-table thead th {
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 0.14em;
      color: #9ca3af;
      border-bottom: 1px solid #e5e7eb;
    }

    .orders-table tbody td {
      font-size: 0.85rem;
      vertical-align: middle;
    }

    .orders-status-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 3px 10px;
      border-radius: 999px;
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      background-color: #eff6ff;
      color: #1d4ed8;
    }

    @media (max-width: 576px) {
      .orders-summary-card {
        padding: 22px 12px;
        min-height: 120px;
      }

      .orders-summary-value {
        font-size: 1.6rem;
      }
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <div class="min-height-300 position-absolute w-100"></div>
  <?php echo $adminHeader->printAdminNav(); ?>

  <main class="main-content position-relative border-radius-lg ">
    <?php echo $adminHeader->printAdminNav2("Orders"); ?>

    <div class="container-fluid py-4">
      <div class="orders-title">Orders</div>

      <div class="orders-summary-grid">
        <div class="orders-summary-card">
          <div class="orders-summary-value">
            <?php echo number_format($totalOrders); ?>
          </div>
          <div class="orders-summary-label">Total Orders</div>
        </div>

        <div class="orders-summary-card">
          <div class="orders-summary-value">
            <?php echo number_format($totalCompleted); ?>
          </div>
          <div class="orders-summary-label">Completed</div>
        </div>

        <div class="orders-summary-card">
          <div class="orders-summary-value">
            <?php echo number_format($totalPending); ?>
          </div>
          <div class="orders-summary-label">Pending</div>
        </div>

        <div class="orders-summary-card">
          <div class="orders-summary-value">
            <?php echo number_format($totalFailed); ?>
          </div>
          <div class="orders-summary-label">Failed</div>
        </div>

        <div class="orders-summary-card">
          <div class="orders-summary-value">
            <?php echo number_format($totalReturn); ?>
          </div>
          <div class="orders-summary-label">Return</div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <form method="GET" action="order.php">
            <div class="orders-filter-row">
              <div>
                <span class="orders-search-label">Search</span>
                <input
                  type="text"
                  name="search"
                  class="form-control d-inline-block orders-search-input"
                  placeholder="Order Number / Date / Name"
                  value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                >
              </div>

              <div class="d-flex align-items-center gap-2">
                <span class="orders-search-label me-2">Export</span>
                <select class="form-control orders-export-select" disabled>
                  <option value="20">20</option>
                </select>
              </div>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table orders-table">
              <thead>
                <tr>
                  <th>Order #</th>
                  <th>Date</th>
                  <th>Customer</th>
                  <th>Value</th>
                  <th>Method</th>
                  <th>Payment</th>
                  <th>Status</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($orders)): ?>
                  <tr>
                    <td colspan="8" class="text-center py-4">
                      No cart items found.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($orders as $row): ?>
                    <?php
                      $orderNumber = $row['order_number'];
                      $customerName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
                      if ($customerName === '') {
                        $customerName = 'Guest';
                      }
                      $customer = $customerName;
                      if (!empty($row['email'])) {
                        $customer .= ' (' . $row['email'] . ')';
                      }

                      $value = isset($row['total']) ? (float) $row['total'] : 0;
                      $createdAt = isset($row['created_at']) ? $row['created_at'] : '';
                      $paymentMethod = !empty($row['payment_method']) ? strtoupper($row['payment_method']) : '-';
                      $paymentStatus = !empty($row['payment_status']) ? ucfirst($row['payment_status']) : '-';
                    ?>
                    <tr>
                      <td><?php echo htmlspecialchars($orderNumber, ENT_QUOTES, 'UTF-8'); ?></td>
                      <td>
                        <?php echo $createdAt ? date('M d, Y H:i', strtotime($createdAt)) : '-'; ?>
                      </td>
                      <td><?php echo htmlspecialchars($customer, ENT_QUOTES, 'UTF-8'); ?></td>
                      <td>LKR <?php echo number_format($value, 2); ?></td>
                      <td><?php echo htmlspecialchars($paymentMethod, ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars($paymentStatus, ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><span class="orders-status-pill">Order Placed</span></td>
                      <td class="text-end">
                        <span class="text-secondary text-xs">View</span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <?php echo $adminHeader->printAdminFooter(); ?>
    </div>
  </main>

  <?php echo $adminHeader->printAdminFooterJS(); ?>
</body>
</html>

