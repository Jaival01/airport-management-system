<?php
require_once '../config.php';
requireLogin();

$booking_id = 0;
if (isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);
} elseif (isset($_GET['booking_id'])) {
    $booking_id = intval($_GET['booking_id']);
}

$booking = $conn->query(
    "SELECT b.*, f.*, u.full_name as user_name, u.email as user_email
    FROM bookings b
    JOIN flights f ON b.flight_id = f.id
    JOIN users u ON b.user_id = u.id
    WHERE b.id = " . intval($booking_id) . " AND b.user_id = " . intval($_SESSION['user_id'])
)->fetch_assoc();

if (!$booking) {
    die('Booking not found');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket - <?php echo $booking['booking_reference']; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; color: black; }
            .ticket-container { box-shadow: none; border: 2px solid #000; }
        }
        .ticket-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            color: #000;
            padding: 40px;
            border-radius: 10px;
        }
        .ticket-header {
            border-bottom: 3px solid #ff2b2b;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .ticket-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-block {
            padding: 15px;
            background: #f5f5f5;
            border-left: 4px solid #ff2b2b;
        }
        .info-label {
            font-size: 0.875rem;
            color: #666;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 1.125rem;
            font-weight: 600;
            color: #000;
        }
    </style>
</head>
<body style="background: #f5f5f5;">
    <div class="no-print" style="text-align: center; padding: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    <div class="ticket-container">
        <div class="ticket-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="color: #ff2b2b; margin-bottom: 10px;">E-TICKET</h1>
                    <p style="color: #666;">Airport Management System</p>
                </div>
                <div style="text-align: right;">
                    <p style="font-size: 0.875rem; color: #666;">Booking Reference</p>
                    <p style="font-size: 1.5rem; font-weight: 700; color: #ff2b2b;"><?php echo $booking['booking_reference']; ?></p>
                </div>
            </div>
        </div>

        <div class="ticket-info">
            <div class="info-block">
                <div class="info-label">Passenger Name</div>
                <div class="info-value"><?php echo strtoupper($booking['passenger_name']); ?></div>
            </div>
            <div class="info-block">
                <div class="info-label">Passport Number</div>
                <div class="info-value"><?php echo $booking['passport_number']; ?></div>
            </div>
        </div>

        <div style="background: #ff2b2b; color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 5px;">FROM</p>
                    <h2 style="font-size: 2rem; margin-bottom: 10px;"><?php echo explode(' ', $booking['origin'])[0]; ?></h2>
                    <p><?php echo date('d M Y', strtotime($booking['departure_date'])); ?></p>
                    <p style="font-size: 1.5rem; font-weight: 700;"><?php echo date('H:i', strtotime($booking['departure_time'])); ?></p>
                </div>
                <div style="font-size: 3rem;">→</div>
                <div style="text-align: right;">
                    <p style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 5px;">TO</p>
                    <h2 style="font-size: 2rem; margin-bottom: 10px;"><?php echo explode(' ', $booking['destination'])[0]; ?></h2>
                    <p><?php echo date('d M Y', strtotime($booking['arrival_date'])); ?></p>
                    <p style="font-size: 1.5rem; font-weight: 700;"><?php echo date('H:i', strtotime($booking['arrival_time'])); ?></p>
                </div>
            </div>
        </div>

        <div class="ticket-info">
            <div class="info-block">
                <div class="info-label">Flight Number</div>
                <div class="info-value"><?php echo $booking['flight_code']; ?></div>
            </div>
            <div class="info-block">
                <div class="info-label">Airline</div>
                <div class="info-value"><?php echo $booking['airline']; ?></div>
            </div>
            <div class="info-block">
                <div class="info-label">Seat Number</div>
                <div class="info-value"><?php echo $booking['seat_number']; ?></div>
            </div>
            <div class="info-block">
                <div class="info-label">Gate</div>
                <div class="info-value"><?php echo $booking['gate']; ?></div>
            </div>
            <div class="info-block">
                <div class="info-label">Baggage Allowance</div>
                <div class="info-value"><?php echo $booking['bags_count']; ?> Bag(s)</div>
            </div>
            <div class="info-block">
                <div class="info-label">Booking Status</div>
                <div class="info-value" style="color: #22c55e;"><?php echo $booking['status']; ?></div>
            </div>
        </div>

        <div style="margin-top: 30px; padding: 20px; background: #fff9e6; border: 2px dashed #fbbf24; border-radius: 10px;">
            <h3 style="color: #f59e0b; margin-bottom: 10px;">Important Information</h3>
            <ul style="margin-left: 20px; line-height: 2;">
                <li>Please arrive at the airport at least 2 hours before departure</li>
                <li>Carry a valid photo ID and this ticket</li>
                <li>Check-in closes 45 minutes before departure</li>
                <li>Boarding gate closes 15 minutes before departure</li>
            </ul>
        </div>

        <div style="margin-top: 30px; text-align: center; padding-top: 20px; border-top: 2px dashed #ccc;">
            <p style="color: #666; font-size: 0.875rem;">
                Issued on: <?php echo date('d M Y, H:i'); ?> | Price: Rs.<?php echo number_format($booking['price'], 2); ?>
            </p>
            <p style="color: #999; font-size: 0.75rem; margin-top: 10px;">
                This is a computer-generated ticket and does not require a signature.
            </p>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
</body>
</html>
