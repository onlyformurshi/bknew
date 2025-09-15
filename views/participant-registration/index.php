<?php
require '../../config/config.php';

// Get program ID from URL
$program_id = isset($_GET['program-id']) ? intval($_GET['program-id']) : 0;

if ($program_id <= 0) {
    die("Invalid program ID");
}

// Fetch program details
$program = [];
$sessions = [];

try {
    // Fetch program info
    $stmt = $pdo->prepare("SELECT * FROM programs WHERE id = ?");
    $stmt->execute([$program_id]);
    $program = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$program) {
        die("Program not found");
    }
    
    // Fetch session times
    $stmt = $pdo->prepare("SELECT * FROM program_sessions_times WHERE program_id = ? ORDER BY session_start");
    $stmt->execute([$program_id]);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Format dates for display
function formatDate($dateString) {
    return date('F j, Y', strtotime($dateString));
}

function formatTime($dateString) {
    return date('g:i A', strtotime($dateString));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($program['title']) ?> Registration</title>
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
            --glass-white: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.3);
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
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
            overflow-x: hidden;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: var(--white);
            box-shadow: var(--shadow);
            padding: 20px 0;
            margin-bottom: 40px;
            position: relative;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            color: var(--primary-red);
            font-weight: 800;
            font-size: 28px;
            text-decoration: none;
            display: flex;
            align-items: center;
            letter-spacing: -0.5px;
        }
        
        .logo i {
            margin-right: 12px;
            font-size: 32px;
        }
        
        .auth-buttons .btn {
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            margin-left: 15px;
            transition: var(--transition);
            text-decoration: none;
        }
        
        .btn-outline {
            border: 2px solid var(--primary-red);
            color: var(--primary-red);
            background: transparent;
        }
        
        .btn-outline:hover {
            background: var(--primary-red);
            color: white;
        }
        
        .btn-solid {
            background: var(--gradient-red);
            color: white;
            border: none;
        }
        
        .btn-solid:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 46, 77, 0.3);
        }
        
        .registration-container {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
            margin-bottom: 60px;
        }
        
        .program-details {
            flex: 1;
            min-width: 300px;
            background: var(--glass-white);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 40px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            transition: var(--transition);
        }
        
        .program-details:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .program-details::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 8px;
            height: 100%;
            background: var(--gradient-red);
        }
        
        .program-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--primary-red);
            line-height: 1.3;
        }
        
        .program-image-container {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 25px;
            height: 250px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .program-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .program-image-container:hover .program-image {
            transform: scale(1.05);
        }
        
        .program-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            font-size: 15px;
            color: var(--dark-gray);
            background: rgba(255, 255, 255, 0.7);
            padding: 8px 15px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .meta-item i {
            margin-right: 8px;
            color: var(--primary-red);
            font-size: 16px;
        }
        
        .program-description {
            margin-bottom: 25px;
            color: var(--dark-gray);
            font-size: 16px;
            line-height: 1.7;
        }
        
        .session-times {
            margin-top: 30px;
        }
        
        .session-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--primary-red);
            position: relative;
            display: inline-block;
        }
        
        .session-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--gradient-red);
            border-radius: 3px;
        }
        
        .session-list {
            list-style: none;
        }
        
        .session-item {
            padding: 18px 20px;
            background-color: var(--white);
            border-left: 4px solid var(--primary-red);
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .session-item:hover {
            transform: translateX(5px);
        }
        
        .session-name {
            font-weight: 600;
            color: var(--black);
        }
        
        .session-time {
            font-weight: 500;
            color: var(--dark-gray);
            font-size: 14px;
        }
        
        .registration-form-container {
            flex: 1;
            min-width: 300px;
        }
        
        .registration-form {
            background: var(--glass-white);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 40px;
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            transition: var(--transition);
        }
        
        .registration-form:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .form-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--primary-red);
            text-align: center;
            position: relative;
        }
        
        .form-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient-red);
            border-radius: 4px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--dark-gray);
        }
        
        .form-control {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--medium-gray);
            border-radius: 12px;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            transition: var(--transition);
            background: rgba(255, 255, 255, 0.8);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 4px rgba(255, 46, 77, 0.2);
            background: white;
        }
        
        .form-icon {
            position: absolute;
            right: 20px;
            top: 50px;
            color: var(--primary-red);
            font-size: 18px;
        }
        
        .terms-container {
            margin: 30px 0;
        }
        
        .terms-checkbox {
            display: flex;
            align-items: flex-start;
        }
        
        .terms-checkbox input {
            margin-right: 12px;
            margin-top: 5px;
            accent-color: var(--primary-red);
            width: 18px;
            height: 18px;
        }
        
        .terms-text {
            font-size: 14px;
            color: var(--dark-gray);
            line-height: 1.6;
        }
        
        .terms-text a {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .terms-text a:hover {
            text-decoration: underline;
            color: var(--dark-red);
        }
        
        .submit-btn {
            width: 100%;
            padding: 18px;
            background: var(--gradient-red);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'Montserrat', sans-serif;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 5px 15px rgba(255, 46, 77, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .submit-btn:hover {
            background: var(--dark-red);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 46, 77, 0.4);
        }
        
        .submit-btn:active {
            transform: translateY(0);
        }
        
        /* Ticket Styles */
        .ticket-container {
            display: none;
            margin: 60px auto;
            max-width: 600px;
            background: var(--glass-white);
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            transform: scale(0.9);
            opacity: 0;
            transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            z-index: 10;
        }
        
        .ticket-container.visible {
            transform: scale(1);
            opacity: 1;
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
            width: 180px;
            height: 180px;
            margin: 0 auto;
            background: var(--gradient-red);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(255, 46, 77, 0.2);
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
            transition: var(--transition);
            font-size: 15px;
            border: none;
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
        
        .new-registration-btn {
            background: transparent;
            border: 2px solid var(--primary-red);
            color: var(--primary-red);
        }
        
        .new-registration-btn:hover {
            background: rgba(255, 46, 77, 0.1);
        }
        
        /* Confetti Effect */
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background-color: var(--primary-red);
            opacity: 0;
            z-index: 999;
            animation: confetti 3s ease-in-out;
        }
        
        @keyframes confetti {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
        
        /* Floating Circles Background */
        .floating-circles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        
        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 46, 77, 0.05);
            animation: float 15s infinite linear;
        }
        
        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
            }
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .registration-container {
                flex-direction: column;
            }
            
            .program-details, .registration-form {
                width: 100%;
            }
            
            .ticket-container {
                max-width: 90%;
            }
        }
        
        @media (max-width: 768px) {
            .program-title, .form-title {
                font-size: 26px;
            }
            
            .ticket-title {
                font-size: 24px;
            }
            
            .ticket-program-title {
                font-size: 22px;
            }
            
            .session-item {
                flex-direction: column;
            }
        }
        
        @media print {
            body * {
                visibility: hidden;
            }
            .ticket-container, .ticket-container * {
                visibility: visible;
            }
            .ticket-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
                background: white;
                border: none;
            }
            .ticket-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Circles Background -->
    <div class="floating-circles">
        <div class="circle" style="width: 300px; height: 300px; top: 10%; left: 5%; animation-duration: 20s;"></div>
        <div class="circle" style="width: 200px; height: 200px; top: 60%; left: 80%; animation-duration: 25s;"></div>
        <div class="circle" style="width: 150px; height: 150px; top: 30%; left: 50%; animation-duration: 15s;"></div>
        <div class="circle" style="width: 250px; height: 250px; top: 70%; left: 20%; animation-duration: 30s;"></div>
    </div>
    
    <header>
        <div class="container header-content">
            <a href="#" class="logo">
                <i class="fas fa-calendar-star"></i>
                <span><img width="250px" src="../assets/images/bk-logo.png" alt=""></span>
            </a>
        </div>
    </header>
    
    <main class="container">
        <div class="registration-container">
            <div class="program-details">
                <h1 class="program-title" id="program-title"><?= htmlspecialchars($program['title']) ?></h1>
                <div class="program-image-container">
                    <img src="../../uploads/programs/<?= htmlspecialchars($program['program_img']) ?>" alt="Program Image" class="program-image" id="program-image">
                </div>
                
                <div class="program-meta">
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span id="program-venue"><?= htmlspecialchars($program['venue']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-user-tie"></i>
                        <span id="program-instructor"><?= htmlspecialchars($program['instructor_name']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-users"></i>
                        <span id="program-participants"><?= htmlspecialchars($program['current_participants']) ?>/<?= htmlspecialchars($program['max_participants']) ?> participants</span>
                    </div>
                    <?php if (!empty($sessions)): ?>
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span><?= count($sessions) ?> Sessions</span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <p class="program-description" id="program-description">
                    <?= htmlspecialchars($program['description']) ?>
                </p>
                
                <?php if (!empty($sessions)): ?>
                <div class="session-times">
                    <h3 class="session-title">Program Schedule</h3>
                    <ul class="session-list" id="session-list">
                        <?php foreach ($sessions as $session): ?>
                        <li class="session-item">
                            <div class="session-name"><?= htmlspecialchars($session['session_name']) ?></div>
                            <div class="session-time">
                                <?= formatDate($session['session_start']) ?> | 
                                <?= formatTime($session['session_start']) ?> - <?= formatTime($session['session_end']) ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="registration-form-container">
                <form class="registration-form" id="registrationForm" method="post" action="process-registration.php">
                    <input type="hidden" name="program_id" value="<?= $program_id ?>">
                    
                    <h2 class="form-title">Join This Program</h2>
                    
                    <div class="form-group">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" id="fullName" name="fullName" class="form-control" required placeholder="Enter your full name">
                        <i class="fas fa-user form-icon"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="mobile" class="form-label">Mobile Number</label>
                        <input type="tel" id="mobile" name="mobile" class="form-control" required placeholder="Enter your mobile number">
                        <i class="fas fa-phone form-icon"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="place" class="form-label">Place</label>
                        <input type="text" id="place" name="place" class="form-control" required placeholder="Enter your city">
                        <i class="fas fa-map-marker-alt form-icon"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="referralSource" class="form-label">Where did you hear about us?</label>
                        <input type="text" id="referralSource" name="referralSource" class="form-control" required placeholder="e.g. Social Media, Friend, Website">
                        <i class="fas fa-info-circle form-icon"></i>
                    </div>
                    
                    <div class="terms-container">
                        <div class="terms-checkbox">
                            <input type="checkbox" id="termsAgreement" name="termsAgreement" required>
                            <label for="termsAgreement" class="terms-text">
                                I agree to the <a href="#" data-terms-modal>Terms and Conditions</a> and understand that my data will be processed in accordance with the <a href="#" data-privacy-modal>Privacy Policy</a>. I consent to receive communications about this event and future similar events.
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                        Confirm Registration
                    </button>
                </form>
            </div>
        </div>
        
        <div class="ticket-container" id="ticketContainer">
            <div class="ticket-header">
                <h3 class="ticket-title">Registration Confirmed!</h3>
                <p class="ticket-subtitle">Your exclusive access to <?= htmlspecialchars($program['title']) ?></p>
            </div>
            
            <div class="ticket-body">
                <div class="ticket-program">
                    <h4 class="ticket-program-title" id="ticket-program-title"><?= htmlspecialchars($program['title']) ?></h4>
                    
                    <div class="ticket-details">
                        <div class="ticket-detail">
                            <div class="detail-label">Date</div>
                            <div class="detail-value" id="ticket-program-date">
                                <?php if (!empty($sessions)): ?>
                                    <?= formatDate($sessions[0]['session_start']) ?>
                                    <?php if (count($sessions) > 1 && 
                                          formatDate($sessions[0]['session_start']) != formatDate($sessions[count($sessions)-1]['session_start'])): ?>
                                        - <?= formatDate($sessions[count($sessions)-1]['session_start']) ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="ticket-detail">
                            <div class="detail-label">Venue</div>
                            <div class="detail-value" id="ticket-program-venue"><?= htmlspecialchars($program['venue']) ?></div>
                        </div>
                        <div class="ticket-detail">
                            <div class="detail-label">Instructor</div>
                            <div class="detail-value" id="ticket-program-instructor"><?= htmlspecialchars($program['instructor_name']) ?></div>
                        </div>
                        <div class="ticket-detail">
                            <div class="detail-label">Reference ID</div>
                            <div class="detail-value">EVT-<?= date('Y') ?>-<?= strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $program['title']), 0, 4)) ?>-<?= str_pad($program_id, 4, '0', STR_PAD_LEFT) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="ticket-participant">
                    <div class="detail-label">Participant Details</div>
                    <div class="detail-value" id="ticket-participant-name">John Doe</div>
                    
                    <div class="detail-label">Email</div>
                    <div class="detail-value" id="ticket-participant-email">john.doe@example.com</div>
                    
                    <div class="detail-label">Mobile</div>
                    <div class="detail-value" id="ticket-participant-mobile">+1 (555) 123-4567</div>
                    
                    <div class="detail-label">Place</div>
                    <div class="detail-value" id="ticket-participant-place">New York, NY</div>
                </div>
                
                <div class="ticket-qr">
                    <div class="ticket-qr-placeholder">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <p>Scan this QR code at the venue for check-in</p>
                </div>
                
                <div class="ticket-actions">
                    <button class="ticket-btn print-btn" id="printTicket">
                        <i class="fas fa-print" style="margin-right: 8px;"></i>
                        Print Ticket
                    </button>
                    <button class="ticket-btn new-registration-btn" id="newRegistration">
                        <i class="fas fa-user-plus" style="margin-right: 8px;"></i>
                        New Registration
                    </button>
                </div>
            </div>
            
            <div class="ticket-footer">
                <p>For any questions, please contact our support team at support@eventpro.com</p>
                <p>We look forward to seeing you at the event!</p>
            </div>
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Create floating circles dynamically
            const floatingCircles = document.querySelector('.floating-circles');
            for (let i = 0; i < 8; i++) {
                const circle = document.createElement('div');
                circle.className = 'circle';
                const size = Math.floor(Math.random() * 150) + 50;
                const top = Math.random() * 100;
                const left = Math.random() * 100;
                const duration = Math.floor(Math.random() * 20) + 10;
                circle.style.width = `${size}px`;
                circle.style.height = `${size}px`;
                circle.style.top = `${top}%`;
                circle.style.left = `${left}%`;
                circle.style.animationDuration = `${duration}s`;
                floatingCircles.appendChild(circle);
            }
            
            // Form submission
            const registrationForm = document.getElementById('registrationForm');
            const ticketContainer = document.getElementById('ticketContainer');
            
            registrationForm.addEventListener('submit', function(e) {
                // Allow normal form submission to process-registration.php
            });
            
            // Print ticket
            document.getElementById('printTicket').addEventListener('click', function() {
                window.print();
            });
            
            // New registration
            document.getElementById('newRegistration').addEventListener('click', function() {
                ticketContainer.classList.remove('visible');
                setTimeout(() => {
                    ticketContainer.style.display = 'none';
                    registrationForm.reset();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }, 600);
            });
            
            // Create confetti effect
            function createConfetti() {
                const colors = ['#ff2e4d', '#ff5a72', '#d1002a', '#ff8fa3'];
                
                for (let i = 0; i < 100; i++) {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.width = Math.random() * 10 + 5 + 'px';
                    confetti.style.height = Math.random() * 10 + 5 + 'px';
                    confetti.style.animationDuration = Math.random() * 2 + 2 + 's';
                    document.body.appendChild(confetti);
                    
                    setTimeout(() => {
                        confetti.remove();
                    }, 3000);
                }
            }
        });
    </script>
</body>
</html>