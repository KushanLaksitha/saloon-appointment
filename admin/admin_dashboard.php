<?php
session_start();

// Security: Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../includes/database.php';

// Function to get dashboard statistics
function getDashboardStats($conn) {
    $stats = [];

    // Total customers
    $customer_query = "SELECT COUNT(*) as total_customers FROM customers";
    $customer_result = $conn->query($customer_query);
    $stats['total_customers'] = $customer_result->fetch_assoc()['total_customers'];

    // Total bookings
    $booking_query = "SELECT COUNT(*) as total_bookings FROM bookings";
    $booking_result = $conn->query($booking_query);
    $stats['total_bookings'] = $booking_result->fetch_assoc()['total_bookings'];

    // Total revenue (last 30 days)
    $revenue_query = "SELECT COALESCE(SUM(total_amount), 0) as total_revenue 
                      FROM bookings 
                      WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $revenue_result = $conn->query($revenue_query);
    $stats['monthly_revenue'] = $revenue_result->fetch_assoc()['total_revenue'];

    // Most popular service
    $service_query = "SELECT service_name, COUNT(*) as service_count 
                      FROM bookings 
                      GROUP BY service_name 
                      ORDER BY service_count DESC 
                      LIMIT 1";
    $service_result = $conn->query($service_query);
    $stats['top_service'] = $service_result->fetch_assoc();

    return $stats;
}

// Get dashboard statistics
$dashboard_stats = getDashboardStats($conn);

// Recent bookings
function getRecentBookings($conn) {
    $query = "SELECT b.id, c.name, b.service_name, b.booking_date, b.total_amount 
              FROM bookings b
              JOIN customers c ON b.customer_id = c.id
              ORDER BY b.booking_date DESC 
              LIMIT 10";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$recent_bookings = getRecentBookings($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saloon Kavisha - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6a11cb;
            --secondary-color: #2575fc;
            --transition-speed: 0.3s;
        }

        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
        }

        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding-top: 20px;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            transition: all var(--transition-speed) ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }

        .dashboard-card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform var(--transition-speed) ease;
        }

        .dashboard-card:hover {
            transform: translateY(-10px);
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .card-icon {
            font-size: 3rem;
            color: var(--primary-color);
            opacity: 0.7;
        }

        .recent-bookings-table {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .sidebar {
                position: static;
                height: auto;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="text-center mb-4">
                    <h3>Saloon Kavisha</h3>
                    <p class="text-white-50">Admin Panel</p>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_dashboard.php">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="bookings.php">
                            <i class="fas fa-calendar-check me-2"></i>Bookings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="customers.php">
                            <i class="fas fa-users me-2"></i>Customers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services.php">
                            <i class="fas fa-scissors me-2"></i>Services
                        </a>
                    </li>
                    <?php if($_SESSION['is_super_admin']): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_management.php">
                            <i class="fas fa-user-shield me-2"></i>Admin Management
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2">Dashboard</h1>
                    <div class="text-muted">
                        Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                    </div>
                </div>

                <!-- Dashboard Cards -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-body d-flex align-items-center">
                                <i class="fas fa-users card-icon me-3"></i>
                                <div>
                                    <h5 class="card-title">Total Customers</h5>
                                    <p class="card-text display-6">
                                        <?php echo number_format($dashboard_stats['total_customers']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-body d-flex align-items-center">
                                <i class="fas fa-calendar-check card-icon me-3"></i>
                                <div>
                                    <h5 class="card-title">Total Bookings</h5>
                                    <p class="card-text display-6">
                                        <?php echo number_format($dashboard_stats['total_bookings']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-body d-flex align-items-center">
                                <i class="fas fa-dollar-sign card-icon me-3"></i>
                                <div>
                                    <h5 class="card-title">Monthly Revenue</h5>
                                    <p class="card-text display-6">
                                        ₹<?php echo number_format($dashboard_stats['monthly_revenue'], 2); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-body d-flex align-items-center">
                                <i class="fas fa-star card-icon me-3"></i>
                                <div>
                                    <h5 class="card-title">Top Service</h5>
                                    <p class="card-text">
                                        <?php echo htmlspecialchars($dashboard_stats['top_service']['service_name'] ?? 'N/A'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="card recent-bookings-table">
                    <div class="card-header">
                        <h5 class="card-title">Recent Bookings</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Customer Name</th>
                                        <th>Service</th>
                                        <th>Booking Date</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo $booking['id']; ?></td>
                                        <td><?php echo htmlspecialchars($booking['name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></td>
                                        <td>₹<?php echo number_format($booking['total_amount'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>