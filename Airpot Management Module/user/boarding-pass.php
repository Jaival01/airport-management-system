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
    "SELECT b.*, f.*, u.full_name as user_name
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
    <title>Boarding Pass - <?php echo $booking['booking_reference']; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
        .boarding-pass {
            max-width: 900px;
            margin: 40px auto;
            background-color: #cc0000;
            color: white;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #990000;
        }
        .bp-header {
            padding: 25px 30px;
            background-color: rgba(0, 0, 0, 0.2);
        }
        .bp-body {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            padding: 30px;
        }
        .bp-section {
            padding: 15px;
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .bp-label {
            font-size: 0.75rem;
            opacity: 0.8;
            text-transform: uppercase;
        }
        .bp-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 5px;
        }
        .bp-qr {
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body style="background: #1a1a1a;">
    <div class="no-print" style="text-align: center; padding: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Print Boarding Pass</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    <div class="boarding-pass">
        <div class="bp-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                <h1 style="font-size: 2rem; margin-bottom: 5px;">BOARDING PASS</h1>
                    <p style="opacity: 0.9;">Airport Management System</p>
                </div>
                <div style="text-align: right;">
                    <div class="bp-label">Flight</div>
                    <div style="font-size: 2rem; font-weight: 700;"><?php echo $booking['flight_code']; ?></div>
                </div>
            </div>
        </div>

        <div class="bp-body">
            <div>
                <div class="bp-section" style="margin-bottom: 20px;">
                    <div class="bp-label">Passenger Name</div>
                    <div class="bp-value"><?php echo strtoupper($booking['passenger_name']); ?></div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
                    <div class="bp-section">
                        <div class="bp-label">From</div>
                        <div class="bp-value"><?php echo explode(' ', $booking['origin'])[0]; ?></div>
                        <div style="margin-top: 10px; font-size: 1rem;"><?php echo date('H:i', strtotime($booking['departure_time'])); ?></div>
                    </div>
                    <div class="bp-section">
                        <div class="bp-label">To</div>
                        <div class="bp-value"><?php echo explode(' ', $booking['destination'])[0]; ?></div>
                        <div style="margin-top: 10px; font-size: 1rem;"><?php echo date('H:i', strtotime($booking['arrival_time'])); ?></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                    <div class="bp-section">
                        <div class="bp-label">Date</div>
                        <div style="font-size: 1rem; font-weight: 600; margin-top: 5px;">
                            <?php echo date('d M', strtotime($booking['departure_date'])); ?>
                        </div>
                    </div>
                    <div class="bp-section">
                        <div class="bp-label">Gate</div>
                        <div style="font-size: 1.5rem; font-weight: 700; margin-top: 5px;">
                            <?php echo $booking['gate']; ?>
                        </div>
                    </div>
                    <div class="bp-section">
                        <div class="bp-label">Seat</div>
                        <div style="font-size: 1.5rem; font-weight: 700; margin-top: 5px;">
                            <?php echo $booking['seat_number']; ?>
                        </div>
                    </div>
                    <div class="bp-section">
                        <div class="bp-label">Boarding</div>
                        <div style="font-size: 1rem; font-weight: 600; margin-top: 5px;">
                            <?php echo date('H:i', strtotime($booking['departure_time']) - 1800); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="bp-qr">
                    <div style="width: 180px; height: 180px; margin: 0 auto; background: white; border: 3px solid #990000; border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-direction: column; color: #000;">
                        <p style="font-size: 0.7rem; margin-bottom: 5px; color: #666;">Booking Ref</p>
                        <p style="font-weight: bold; font-size: 1rem; color: #cc0000;"><?php echo $booking['booking_reference']; ?></p>
                    </div>
                    <p style="margin-top: 12px; font-size: 0.875rem; opacity: 0.9;">
                        <?php echo $booking['booking_reference']; ?>
                    </p>
                </div>
                
                <div class="bp-section" style="margin-top: 20px;">
                    <div class="bp-label">Status</div>
                    <div style="font-size: 1.125rem; font-weight: 600; margin-top: 5px; color: #22c55e;">
                        ✓ <?php echo $booking['status']; ?>
                    </div>
                </div>
            </div>
        </div>

        <div style="padding: 20px 40px; background: rgba(0, 0, 0, 0.3); border-top: 2px dashed rgba(255, 255, 255, 0.3);">
            <p style="text-align: center; font-size: 0.875rem; opacity: 0.9;">
                Please arrive at gate <strong><?php echo $booking['gate']; ?></strong> at least 30 minutes before departure | 
                Boarding closes 15 minutes before departure
            </p>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
</body>
</html>
