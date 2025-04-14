<?php
session_start();
require_once '../config/database.php';


if (!isset($_SESSION['admin_id']) || !isset($_SESSION['is_admin'])) {
    header('Location: ../login.php');
    exit();
}



// Get total books
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM books");
$stmt->execute();
$result = $stmt->get_result();
$bookCount = $result->fetch_assoc()['count'];

// Get total users (excluding admin)
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE is_admin = 0");
$stmt->execute();
$result = $stmt->get_result();
$userCount = $result->fetch_assoc()['count'];

// Get pending books count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM books WHERE status = 'pending'");
$stmt->execute();
$result = $stmt->get_result();
$pendingBooks = $result->fetch_assoc()['count'];

// Get total sales (approved books)
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM books WHERE status = 'approved'");
$stmt->execute();
$result = $stmt->get_result();
$approvedBooks = $result->fetch_assoc()['count'];

// Get recent activity
$stmt = $conn->prepare("
    SELECT b.title, b.status, u.username
    FROM books b 
    JOIN users u ON b.user_id = u.id 
    WHERE b.status IN ('pending', 'approved', 'rejected')
    LIMIT 5
");
$stmt->execute();
$recentActivity = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel - ezBooks</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Doto:wght@100..900&family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Galdeano&family=IBM+Plex+Sans:ital,wght@0,100..700;1,100..700&family=Inconsolata:wdth,wght@82,200..900&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="home.css" />
  </head>
  <body>
    <div class="navbar">
      <h2><span class="ez">ez</span><span class="books">Books</span></h2>
      <div class="nav-links">
        <a href="admin_dashboard.php" class="active">Home</a>
        <a href="admin_managebook.php">Manage Books</a>
        <a href="admin_manageuser.php">Manage Users</a>
        <a href="orders.php">Orders</a>
        <a href="logout.php" class="logout-btn">Logout</a>
      </div>
    </div>

    <div class="main">
      <h1 id="greeting">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?> </h1>
      
      <div class="cards">
        <div class="card">
          <p>Total Books</p>
          <h3><?php echo $bookCount; ?></h3>
        </div>
        <div class="card">
          <p>Total Users</p>
          <h3><?php echo $userCount; ?></h3>
        </div>
        <div class="card">
          <p>Pending Books</p>
          <h3><?php echo $pendingBooks; ?></h3>
        </div>
        <div class="card">
          <p>Approved Books</p>
          <h3><?php echo $approvedBooks; ?></h3>
        </div>
      </div>

      <div class="recent-activity">
        <h2>Recent Activity</h2>
        <table>
          <thead>
            <tr>
              <th>Book Title</th>
              <th>Seller</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if(count($recentActivity) > 0): ?>
              <?php foreach($recentActivity as $activity): ?>
                <tr>
                  <td><?php echo htmlspecialchars($activity['title']); ?></td>
                  <td><?php echo htmlspecialchars($activity['username']); ?></td>
                  <td>
                    <span class="status-badge <?php echo strtolower($activity['status']); ?>">
                      <?php echo ucfirst($activity['status']); ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="no-activity">No recent activity</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </body>
</html>
