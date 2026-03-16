<?php
  session_start();
  require_once("../classes/class.user.php");
  require_once("../classes/class.header.php");
  $adminHeader = new HEADER("dashboard");
  $user = new USER();

  if ( $user->is_loggedin() ) {
    if ( !$user->checkTimeout() ) {
      $user->doLogout($adminHeader->getActivePage());
    }
  } else {
    $user->doLogout();
  }

  // Dashboard statistics (data-only, no side effects)
  $totalResources =
    (int) $user->CountRows("blog_details", array("status" => 1)) +
    (int) $user->CountRows("books_details", array("status" => 1)) +
    (int) $user->CountRows("homework_details", array("status" => 1)) +
    (int) $user->CountRows("pdf_details", array("status" => 1));

  $totalProducts = (int) $user->CountRows("products", array("status" => 1));

  // Total sales based on orders table
  $totalSales = 0;
  try {
    $orderStmt = $user->getConnection()->query("SELECT COALESCE(SUM(total), 0) AS total_sales FROM orders");
    $orderRow = $orderStmt ? $orderStmt->fetch(PDO::FETCH_ASSOC) : null;
    if ($orderRow && isset($orderRow['total_sales'])) {
      $totalSales = (float) $orderRow['total_sales'];
    }
  } catch (Exception $e) {
    $totalSales = 0;
  }

  // Calculate Total Downloads across all tables
$totalDownloads = 0;
try {
    $downloadQuery = "
        SELECT 
            (SELECT COALESCE(SUM(download_count), 0) FROM pdf_details) +
            (SELECT COALESCE(SUM(download_count), 0) FROM books_details) +
            (SELECT COALESCE(SUM(download_count), 0) FROM homework_details) 
        AS grand_total";
    
    $downloadStmt = $user->getConnection()->query($downloadQuery);
    $downloadRow = $downloadStmt->fetch(PDO::FETCH_ASSOC);
    $totalDownloads = (int)$downloadRow['grand_total'];
} catch (Exception $e) {
    $totalDownloads = 0; // Fallback to 0 if table columns don't exist yet
}

// Fetch Total Members (Since you have a 1000 placeholder there too)
$totalMembers = (int)$user->CountRows("tourists", array());
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php echo $adminHeader->printAdminHeader(); ?>
  <meta property='og:title' content='Traveylo | Sri Lanka Tour Packages | Travel Agent in Sri Lanka'/>
  <meta name='description' content='“Ayubowan!” Traveylo.com provides tour packages covering the most beautiful places 
in Sri Lanka, and you can travel in luxury with your own vehicle around 
our beautiful country. So reserve your tour with us.' />
<meta name='keywords' content='Travel Agents In Sri Lanka / Sri Lanka Tourism / Sri Lanka Tourist Destinations / Places To Visit In Sri Lanka With Family / How To Travel In Sri Lanka / Sri Lanka Tours & Travels / Tour Packages In Sri Lanka / Sri Lanka Itinerary / Sri Lanka Travel Guide /Sri Lanka HotelsSri Lanka Tour Operators /Sri Lanka Budgets Tours /Small Group Tour In Sri Lanka / Sri Lanka Holiday Packages /Sri Lanka Tour Packages For Couple / Sri Lanka Tour Packages For Family /Sri Lanka Tour Packages Price / What To Do In Sri Lanka /Popular Destinations In Sri Lanka' />
  <script src="./assets/js/plugins/chartjs.min.js"></script>
  <style>
    .stats-section {
      margin-bottom: 2rem;
      
    }

    .stats-title {
      font-size: 1.4rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #f97316;
      margin-bottom: 1.5rem;
    }

    .stats-card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 1.2rem;
    }

    .stats-card {
      background-color: #ffffff;
      border-radius: 18px;
      border: 1px solid #e5e7eb;
      padding: 32px 16px;
      text-align: center;
      box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
      min-height: 150px;
      width: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      margin-left: 10px;
      margin-right: 100px;
      margin-top: 10px;
    }

    .stats-card-value {
      font-size: 2.1rem;
      font-weight: 700;
      color: #111827;
      margin-bottom: 4px;
    }

    .stats-card-label {
      font-size: 0.8rem;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: #9ca3af;
    }

    @media (max-width: 576px) {
      .stats-card {
        padding: 24px 14px;
        min-height: 130px;
      }

      .stats-card-value {
        font-size: 1.8rem;
      }
    }
  </style>
</head>

<body class="g-sidenav-show   bg-gray-100">
  <div class="min-height-300 bg-primary position-absolute w-100"></div>
  <?php echo $adminHeader->printAdminNav(); ?>
  <main class="main-content position-relative border-radius-lg ">
    <!-- Navbar -->
    <?php echo $adminHeader->printAdminNav2($adminHeader->getActivePageName()); ?>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="stats-section">
        <h2 class="stats-title">Statistics</h2>
        <div class="stats-card-grid">
          <div class="stats-card">
            <div class="stats-card-value">
              <?php echo number_format($totalResources); ?>
            </div>
            <div class="stats-card-label">Total Resources</div>
          </div>

          <div class="stats-card">
          <div class="stats-card-value">
           <?php echo number_format($totalDownloads); ?>
          </div>
           <div class="stats-card-label">Total Downloads</div>
          </div>

          <div class="stats-card">
            <div class="stats-card-value">
              <?php echo number_format($totalProducts); ?>
            </div>
            <div class="stats-card-label">Total Products</div>
          </div>

          <div class="stats-card">
          <div class="stats-card-value">
            <?php echo number_format($totalMembers); ?>
           </div>
           <div class="stats-card-label">Total Members</div>
          </div>
          </div>

          <div class="stats-card">
            <div class="stats-card-value">
              <?php echo number_format($totalSales); ?>
            </div>
            <div class="stats-card-label">Total Sales</div>
          </div>
        </div>
      </div>
      <?php echo $adminHeader->printAdminFooter(); ?>
    </div>
  </main>
  <?php echo $adminHeader->printAdminFooterJS(); ?>
</body>

</html>