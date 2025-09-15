<?php
require '../../config/config.php';

// Get program ID and (optionally) reference_id from GET
$program_id = isset($_GET['program-id']) ? intval($_GET['program-id']) : 0;
$reference_id = isset($_GET['reference_id']) ? $_GET['reference_id'] : '';

if ($program_id <= 0) {
    die('Invalid program ID.');
}

// Fetch program details
$stmt = $pdo->prepare("SELECT * FROM programs WHERE id = ?");
$stmt->execute([$program_id]);
$program = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$program) {
    die('Program not found.');
}

// Fetch participant by reference_id if provided, else get latest for this program
if ($reference_id) {
    $stmt = $pdo->prepare("SELECT * FROM participants WHERE program_id = ? AND reference_id = ? LIMIT 1");
    $stmt->execute([$program_id, $reference_id]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM participants WHERE program_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$program_id]);
}
$participant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$participant) {
    die('Participant not found.');
}

// Fetch sessions for date display
$stmt = $pdo->prepare("SELECT * FROM program_sessions_times WHERE program_id = ? ORDER BY session_start ASC");
$stmt->execute([$program_id]);
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Ticket - <?= htmlspecialchars($program['title']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-red: #ff2e4d;
            --dark-red: #d1002a;
            --light-red: #ff5a72;
            --gradient-red: linear-gradient(135deg, #ff2e4d 0%, #d1002a 100%);
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #495057;
            --black: #212529;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #fef6f7;
            color: var(--black);
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .ticket-container {
            max-width: 600px;
            width: 100%;
            background: var(--white);
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
            transform: scale(1);
            opacity: 1;
            transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .ticket-header {
            background: var(--gradient-red);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .ticket-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .ticket-header::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .ticket-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }
        
        .ticket-subtitle {
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .ticket-body {
            padding: 30px;
            position: relative;
        }
        
        .ticket-program {
            margin-bottom: 30px;
        }
        
        .ticket-program-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--primary-red);
            position: relative;
            display: inline-block;
        }
        
        .ticket-program-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--gradient-red);
            border-radius: 3px;
        }
        
        .ticket-details {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .ticket-detail {
            flex: 1;
            min-width: 150px;
        }
        
        .detail-label {
            font-size: 14px;
            color: var(--dark-gray);
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .detail-value {
            font-weight: 600;
            color: var(--black);
            font-size: 16px;
        }
        
        .ticket-participant {
            margin-top: 35px;
            padding-top: 25px;
            border-top: 2px dashed var(--medium-gray);
        }
        
        .ticket-qr {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }
        
        .ticket-qr::before, .ticket-qr::after {
            content: '';
            position: absolute;
            top: -15px;
            width: 30px;
            height: 30px;
            background: #fef6f7;
            border-radius: 50%;
            z-index: 1;
        }
        
        .ticket-qr::before {
            left: -30px;
        }
        
        .ticket-qr::after {
            right: -30px;
        }
        
        .ticket-qr-placeholder {
            height: 100px;
            margin: 0 auto;
            background: #fff; /* Changed from var(--gradient-red) to white */
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
            
        }
        
        .ticket-qr-placeholder::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                rgba(255, 255, 255, 0.3) 0%,
                rgba(255, 255, 255, 0) 50%
            );
            transform: rotate(30deg);
        }
        
        .ticket-qr-placeholder i {
            font-size: 50px;
        }
        
        .ticket-qr p {
            margin-top: 15px;
            font-weight: 500;
            color: var(--dark-gray);
        }
        
        .ticket-footer {
            text-align: center;
            font-size: 13px;
            color: var(--dark-gray);
            padding: 20px;
            border-top: 2px dashed var(--medium-gray);
            position: relative;
        }
        
        .ticket-footer::before, .ticket-footer::after {
            content: '';
            position: absolute;
            top: -15px;
            width: 30px;
            height: 30px;
            background: #fef6f7;
            border-radius: 50%;
            z-index: 1;
        }
        
        .ticket-footer::before {
            left: -30px;
        }
        
        .ticket-footer::after {
            right: -30px;
        }
        
        .ticket-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        
        .ticket-btn {
            padding: 12px 25px;
            border-radius: 50px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 15px;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        
        .print-btn {
            background: var(--gradient-red);
            color: white;
            box-shadow: 0 5px 15px rgba(255, 46, 77, 0.3);
        }
        
        .print-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 46, 77, 0.4);
        }
        
        .download-btn {
            background: var(--white);
            border: 2px solid var(--primary-red);
            color: var(--primary-red);
        }
        
        .download-btn:hover {
            background: rgba(255, 46, 77, 0.1);
        }
        
        .success-message {
            background: rgba(46, 204, 113, 0.2);
            color: #27ae60;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            display: <?= isset($_GET['success']) ? 'block' : 'none' ?>;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .ticket-container {
                box-shadow: none;
                max-width: 100%;
            }
            .ticket-actions {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .ticket-title {
                font-size: 24px;
            }
            
            .ticket-program-title {
                font-size: 22px;
            }
            
            .ticket-details {
                flex-direction: column;
                gap: 15px;
            }
            
            .ticket-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .ticket-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
            Registration successful! Your ticket is ready.
        </div>
        <?php endif; ?>
        
        <div class="ticket-header">
            <h3 class="ticket-title">Registration Confirmed!</h3>
            <p class="ticket-subtitle">Your exclusive access to <?= htmlspecialchars($program['title']) ?></p>
        </div>
        
        <div class="ticket-body">
            <div class="ticket-program">
                <h4 class="ticket-program-title"><?= htmlspecialchars($program['title']) ?></h4>
                
                <div class="ticket-details">
                    <div class="ticket-detail">
                        <div class="detail-label">Date</div>
                        <div class="detail-value">
                            <?php if (!empty($sessions)): ?>
                                <?= date('F j, Y', strtotime($sessions[0]['session_start'])) ?>
                                <?php if (count($sessions) > 1 && 
                                      date('F j, Y', strtotime($sessions[0]['session_start'])) != date('F j, Y', strtotime($sessions[count($sessions)-1]['session_start']))): ?>
                                    - <?= date('F j, Y', strtotime($sessions[count($sessions)-1]['session_start'])) ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ticket-detail">
                        <div class="detail-label">Venue</div>
                        <div class="detail-value"><?= htmlspecialchars($program['venue']) ?></div>
                    </div>
                    <div class="ticket-detail">
                        <div class="detail-label">Reference ID</div>
                        <div class="detail-value"><?= htmlspecialchars($participant['reference_id']) ?></div>
                    </div>
                </div>
            </div>
            
            <div class="ticket-participant">
                <div class="detail-label">Participant Details</div>
                <div class="detail-value"><?= htmlspecialchars($participant['full_name']) ?></div>
                
                <div class="detail-label">Mobile</div>
                <div class="detail-value"><?= htmlspecialchars($participant['mobile']) ?></div>
                
                <div class="detail-label">Place</div>
                <div class="detail-value"><?= htmlspecialchars($participant['place']) ?></div>
                
                <div class="detail-label">Registration Date</div>
                <div class="detail-value"><?= date('F j, Y g:i A', strtotime($participant['registration_date'])) ?></div>
            </div>
            
            <div class="ticket-qrw">
                <div class="ticket-qr-placeholder">
                    <img src="../assets/images/bk-logo.png" alt="Logo" style="width:50%;">
                </div>
                <p>Present this ticket at the venue for check-in</p>
            </div>
            
            <div class="ticket-actions">
                <button class="ticket-btn print-btn" id="printTicket">
                    <i class="fas fa-print" style="margin-right: 8px;"></i>
                    Print Ticket
                </button>
                <a href="#" class="ticket-btn download-btn" id="downloadTicket">
                    <i class="fas fa-download" style="margin-right: 8px;"></i>
                    Download Ticket
                </a>
            </div>
        </div>
        
        <div class="ticket-footer">
            <p>For any questions, please contact our support team</p>
            <p>Thank you for your registration!</p>
        </div>
    </div>

    <!-- HTML2Canvas for downloading ticket as image -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Print ticket
            document.getElementById('printTicket').addEventListener('click', function() {
                window.print();
            });
            
            // Download ticket as image
            document.getElementById('downloadTicket').addEventListener('click', function(e) {
                e.preventDefault();
                
                const ticket = document.querySelector('.ticket-container');
                const downloadBtn = document.getElementById('downloadTicket');
                
                // Change button text temporarily
                const originalText = downloadBtn.innerHTML;
                downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i> Preparing download...';
                
                html2canvas(ticket, {
                    scale: 2,
                    logging: false,
                    useCORS: true,
                    allowTaint: true
                }).then(canvas => {
                    // Create download link
                    const link = document.createElement('a');
                    link.download = 'ticket-<?= $participant['reference_id'] ?>.png';
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                    
                    // Restore button text
                    downloadBtn.innerHTML = originalText;
                });
            });
        });
    </script>
</body>
</html>