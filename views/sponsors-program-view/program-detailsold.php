<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Sponsorship Portal - Enhanced Design</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-red: #dc2626;
            --dark-red: #b91c1c;
            --light-red: #fef2f2;
            --accent-red: #ef4444;
            --red-50: #fef2f2;
            --red-100: #fee2e2;
            --red-500: #ef4444;
            --red-600: #dc2626;
            --red-700: #b91c1c;
            --red-800: #991b1b;
            --red-900: #7f1d1d;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: var(--gray-900);
            background: linear-gradient(135deg, var(--red-50) 0%, var(--red-100) 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Enhanced Header Section */
        .header {
            background: var(--white);
            border-radius: 24px;
            padding: 0;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 0;
            border: 1px solid var(--red-100);
        }

        .program-image-container {
            position: relative;
            background: linear-gradient(135deg, var(--red-600), var(--red-800));
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .program-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .program-image:hover {
            transform: scale(1.05);
        }

        .image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, rgba(220, 38, 38, 0.2), rgba(185, 28, 28, 0.2));
            z-index: 1;
        }

        .program-header-content {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .program-title {
            color: var(--gray-900);
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            position: relative;
            background: linear-gradient(135deg, var(--red-600), var(--red-800));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .program-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--red-50);
            border-radius: 12px;
            border-left: 4px solid var(--red-600);
            transition: all 0.3s ease;
        }

        .meta-item:hover {
            background: var(--red-100);
            transform: translateY(-2px);
        }

        .meta-icon {
            width: 20px;
            height: 20px;
            color: var(--red-600);
            flex-shrink: 0;
        }

        .meta-content {
            flex: 1;
        }

        .meta-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .meta-value {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-900);
        }

        .program-description {
            padding: 1.5rem;
            background: var(--red-50);
            border-radius: 16px;
            border: 1px solid var(--red-200);
            margin-top: 1rem;
        }

        .description-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--red-700);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Modern Marketing Sections */
        .marketing-section {
            background: var(--white);
            border-radius: 24px;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            border: 1px solid var(--gray-200);
        }

        .section-header {
            background: linear-gradient(135deg, var(--red-600), var(--red-800));
            color: var(--white);
            padding: 1.5rem 2rem;
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            overflow: hidden;
        }

        .section-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: -50px;
            width: 100px;
            height: 100%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), transparent);
            transform: skewX(-20deg);
        }

        .section-content {
            padding: 2rem;
        }

        /* Enhanced Grid Layout */
        .marketing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .marketing-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--gray-200);
            position: relative;
            hover-lift: true;
        }

        .marketing-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: var(--red-300);
        }

        .marketing-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--red-500), var(--red-700));
        }

        .marketing-card-header {
            background: linear-gradient(135deg, var(--gray-50), var(--red-50));
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .marketing-card-title {
            font-weight: 700;
            color: var(--gray-900);
            font-size: 1.125rem;
            flex: 1;
        }

        .marketing-card-cost {
            font-weight: 800;
            color: var(--red-600);
            font-size: 1.5rem;
            background: var(--red-100);
            padding: 0.5rem 1rem;
            border-radius: 12px;
            border: 2px solid var(--red-200);
        }

        .marketing-card-body {
            padding: 1.5rem;
        }

        .marketing-detail {
            margin-bottom: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .marketing-detail:last-of-type {
            margin-bottom: 1.5rem;
        }

        .marketing-detail-label {
            font-weight: 600;
            color: var(--gray-500);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .marketing-detail-value {
            color: var(--gray-900);
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Enhanced Status Badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .available {
            background: linear-gradient(135deg, var(--red-600), var(--red-700));
            color: var(--white);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .sponsored {
            background: linear-gradient(135deg, var(--gray-600), var(--gray-700));
            color: var(--white);
            box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
        }

        /* Enhanced Sponsor Section */
        .sponsor-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-200);
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .sponsor-amount {
            padding: 0.875rem 1rem;
            border: 2px solid var(--red-300);
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            width: 140px;
            text-align: center;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .sponsor-amount:focus {
            outline: none;
            border-color: var(--red-600);
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
            background: var(--red-50);
        }

        .sponsor-btn {
            background: linear-gradient(135deg, var(--red-600), var(--red-700));
            color: var(--white);
            border: none;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .sponsor-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.4);
            background: linear-gradient(135deg, var(--red-700), var(--red-800));
        }

        .sponsor-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .sponsor-btn:disabled {
            background: linear-gradient(135deg, var(--gray-400), var(--gray-500));
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Enhanced Total Section */
        .total-section {
            background: linear-gradient(135deg, var(--red-600), var(--red-800));
            color: var(--white);
            padding: 3rem 2rem;
            text-align: center;
            border-radius: 24px;
            margin: 3rem 0;
            box-shadow: var(--shadow-xl);
            position: relative;
            overflow: hidden;
        }

        .total-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .total-section > * {
            position: relative;
            z-index: 1;
        }

        .total-section h3 {
            font-size: 1.75rem;
            margin-bottom: 1rem;
            font-weight: 700;
            opacity: 0.95;
        }

        .total-amount {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 1rem;
            text-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .total-description {
            opacity: 0.9;
            font-size: 1.125rem;
            font-weight: 500;
        }

        /* Subsection Headers */
        .subsection-header {
            margin: 2.5rem 0 1.5rem;
            color: var(--gray-800);
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid var(--red-200);
        }

        .subsection-header i {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--red-600), var(--red-700));
            color: var(--white);
            border-radius: 50%;
            font-size: 1rem;
        }

        /* Enhanced Payment Details */
        .payment-details {
            background: var(--white);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .payment-card {
            background: linear-gradient(135deg, var(--red-50), var(--red-100));
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--red-600);
            transition: all 0.3s ease;
        }

        .payment-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        /* Enhanced Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background: var(--white);
            margin: 5% auto;
            padding: 0;
            border-radius: 24px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--red-600), var(--red-700));
            color: var(--white);
            padding: 2rem;
            position: relative;
        }

        .modal-header h2 {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .sponsor-summary {
            background: var(--red-50);
            padding: 1.5rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            border: 1px solid var(--red-200);
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--red-200);
        }

        .summary-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-label {
            color: var(--gray-600);
            font-weight: 600;
        }

        .summary-value {
            font-weight: 700;
            color: var(--gray-900);
        }

        .summary-highlight {
            color: var(--red-600);
            font-weight: 800;
            font-size: 1.125rem;
        }

        .modal-footer {
            padding: 1.5rem 2rem 2rem;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            background: var(--gray-50);
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--red-600), var(--red-700));
            color: var(--white);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.4);
        }

        .btn-secondary {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .btn-secondary:hover {
            background: var(--gray-300);
        }

        .close {
            position: absolute;
            top: 1.5rem;
            right: 2rem;
            color: rgba(255,255,255,0.8);
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
            z-index: 10;
        }

        .close:hover {
            color: var(--white);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .header {
                grid-template-columns: 1fr;
            }
            
            .program-image-container {
                height: 200px;
            }
            
            .marketing-grid {
                grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .program-title {
                font-size: 2rem;
            }
            
            .marketing-grid {
                grid-template-columns: 1fr;
            }
            
            .sponsor-section {
                flex-direction: column;
            }
            
            .sponsor-amount {
                width: 100%;
            }
            
            .sponsor-btn {
                width: 100%;
            }

            .total-amount {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 480px) {
            .program-meta {
                grid-template-columns: 1fr;
            }
            
            .modal-footer {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }

            .payment-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Enhanced Program Header -->
        <div class="header">
            <div class="program-image-container">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80" alt="Stress Free Living Workshop" class="program-image">
                <div class="image-overlay"></div>
            </div>
            <div class="program-header-content">
                <h1 class="program-title">Stress Free Living Workshop</h1>
                
                <div class="program-meta">
                    <div class="meta-item">
                        <i class="fas fa-hashtag meta-icon"></i>
                        <div class="meta-content">
                            <div class="meta-label">Program Number</div>
                            <div class="meta-value">0001/25</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt meta-icon"></i>
                        <div class="meta-content">
                            <div class="meta-label">Location</div>
                            <div class="meta-value">Bellville Public Library<br>Western & Eastern Cape, South Africa</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-university meta-icon"></i>
                        <div class="meta-content">
                            <div class="meta-label">Centre</div>
                            <div class="meta-value">Port Elizabeth</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <i class="far fa-calendar-alt meta-icon"></i>
                        <div class="meta-content">
                            <div class="meta-label">Date & Time</div>
                            <div class="meta-value">June 11, 2025 • 11:23 PM - 11:34 PM</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-chalkboard-teacher meta-icon"></i>
                        <div class="meta-content">
                            <div class="meta-label">Instructor</div>
                            <div class="meta-value">John Doe</div>
                        </div>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-users meta-icon"></i>
                        <div class="meta-content">
                            <div class="meta-label">Participants</div>
                            <div class="meta-value">50 / 100 enrolled</div>
                        </div>
                    </div>
                </div>
                
                <div class="program-description">
                    <div class="description-label">
                        <i class="fas fa-align-left"></i>
                        Description
                    </div>
                    <div class="meta-value">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
                </div>
            </div>
        </div>

        <!-- Radio Advertisements Section -->
<div class="marketing-section">
    <div class="section-header">
        <i class="fas fa-broadcast-tower"></i>
        <span>Radio Advertisements</span>
    </div>
    <div class="section-content">
        <div class="marketing-grid">
            <div class="marketing-card" data-item-id="radio-1">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">Radio Mango 91.9 FM</div>
                    <div class="marketing-card-cost">$500.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Contact</div>
                        <div class="marketing-detail-value">Mr. Rajesh Kumar, Sales Manager - 9847XXXXXX</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Remarks</div>
                        <div class="marketing-detail-value">Popular Malayalam FM station with strong listenership in Kochi and across Kerala</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('radio-1', 'Radio Mango 91.9 FM', 500)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>

            <div class="marketing-card" data-item-id="radio-2">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">Club FM 94.3</div>
                    <div class="marketing-card-cost">$750.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Contact</div>
                        <div class="marketing-detail-value">Ms. Priya Sharma, Advertising Executive - 9745XXXXXX</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Remarks</div>
                        <div class="marketing-detail-value">Leading Malayalam FM station with significant youth and urban listenership</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('radio-2', 'Club FM 94.3', 750)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Television Advertisements Section -->
<div class="marketing-section">
    <div class="section-header">
        <i class="fas fa-tv"></i>
        <span>Television Advertisements</span>
    </div>
    <div class="section-content">
        <div class="marketing-grid">
            <div class="marketing-card" data-item-id="tv-1">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">Asianet News</div>
                    <div class="marketing-card-cost">$1,200.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Contact</div>
                        <div class="marketing-detail-value">Mr. S. Gopalakrishnan, Sales Head - 9447XXXXXX</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Remarks</div>
                        <div class="marketing-detail-value">Leading Malayalam news channel with high viewership across Kerala</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('tv-1', 'Asianet News', 1200)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>

            <div class="marketing-card" data-item-id="tv-2">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">Flowers TV</div>
                    <div class="marketing-card-cost">$1,800.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Contact</div>
                        <div class="marketing-detail-value">Ms. Leena Nair, Ad Sales Coordinator - 9995XXXXXX</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Remarks</div>
                        <div class="marketing-detail-value">Popular Malayalam entertainment channel known for reality shows and serials</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('tv-2', 'Flowers TV', 1800)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Newspaper Advertisements Section -->
<div class="marketing-section">
    <div class="section-header">
        <i class="fas fa-newspaper"></i>
        <span>Newspaper Advertisements</span>
    </div>
    <div class="section-content">
        <div class="marketing-grid">
            <div class="marketing-card" data-item-id="newspaper-1">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">Manorama</div>
                    <div class="marketing-card-cost">$800.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Duration</div>
                        <div class="marketing-detail-value">1 week (3 insertions - Tuesday, Thursday, Sunday)</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Ad Size</div>
                        <div class="marketing-detail-value">4 cm (width) × 5 cm (height) - Display Ad</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Contact</div>
                        <div class="marketing-detail-value">0484-4014014</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('newspaper-1', 'Manorama', 800)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>

            <div class="marketing-card" data-item-id="newspaper-2">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">The Hindu</div>
                    <div class="marketing-card-cost">$9,999.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Duration</div>
                        <div class="marketing-detail-value">1 week (2 insertions - Wednesday, Saturday)</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Ad Size</div>
                        <div class="marketing-detail-value">8 cm (width) × 10 cm (height) - Display Ad</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Contact</div>
                        <div class="marketing-detail-value">adsales@thehindu.co.in</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('newspaper-2', 'The Hindu', 9999)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Digital Marketing Section -->
<div class="marketing-section">
    <div class="section-header">
        <i class="fas fa-globe"></i>
        <span>Digital Marketing</span>
    </div>
    <div class="section-content">
        <h3 class="subsection-header">
            <i class="fab fa-facebook"></i>
            Facebook Advertisements
        </h3>
        <div class="marketing-grid">
            <div class="marketing-card" data-item-id="facebook-1">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">Kochi Restaurant - Lunch Offer</div>
                    <div class="marketing-card-cost">$1,500.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Campaign Type</div>
                        <div class="marketing-detail-value">Promotional Campaign</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Target Audience</div>
                        <div class="marketing-detail-value">Local food enthusiasts, age 25-45</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Duration</div>
                        <div class="marketing-detail-value">2 weeks</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('facebook-1', 'Facebook Campaign 1', 1500)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>

            <div class="marketing-card" data-item-id="facebook-2">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">Online Store Promotion</div>
                    <div class="marketing-card-cost">$543.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Campaign Type</div>
                        <div class="marketing-detail-value">Product Awareness</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Target Audience</div>
                        <div class="marketing-detail-value">Online shoppers, age 18-35</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Duration</div>
                        <div class="marketing-detail-value">1 week</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('facebook-2', 'Facebook Campaign 2', 543)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="subsection-header">
            <i class="fab fa-instagram"></i>
            Instagram Advertisements
        </h3>
        <div class="marketing-grid">
            <div class="marketing-card" data-item-id="instagram-1">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">Summer Collection</div>
                    <div class="marketing-card-cost">$234.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Campaign Type</div>
                        <div class="marketing-detail-value">Product Showcase</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Target Audience</div>
                        <div class="marketing-detail-value">Fashion enthusiasts, age 18-30</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Duration</div>
                        <div class="marketing-detail-value">1 week</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('instagram-1', 'Instagram Campaign 1', 234)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>

            <div class="marketing-card" data-item-id="instagram-2">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">Brand Awareness</div>
                    <div class="marketing-card-cost">$234.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Campaign Type</div>
                        <div class="marketing-detail-value">Brand Awareness</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Target Audience</div>
                        <div class="marketing-detail-value">General audience, age 18-45</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Duration</div>
                        <div class="marketing-detail-value">1 week</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('instagram-2', 'Instagram Campaign 2', 234)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Other Marketing Section -->
<div class="marketing-section">
    <div class="section-header">
        <i class="fas fa-star"></i>
        <span>Other Marketing & Services</span>
    </div>
    <div class="section-content">
        <div class="marketing-grid">
            <div class="marketing-card" data-item-id="other-1">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">Literature</div>
                    <div class="marketing-card-cost">$1,800.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Provider</div>
                        <div class="marketing-detail-value">By Ink & Page Publishing</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Details</div>
                        <div class="marketing-detail-value">Educational materials and program literature</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Quantity</div>
                        <div class="marketing-detail-value">500 copies</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('other-1', 'Literature', 1800)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>

            <div class="marketing-card" data-item-id="other-2">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">Promotional Merchandise</div>
                    <div class="marketing-card-cost">$3,500.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Items</div>
                        <div class="marketing-detail-value">Branded pens, notebooks, keychains, tote bags</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Quantity</div>
                        <div class="marketing-detail-value">500 sets</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('other-2', 'Promotional Merchandise', 3500)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>

            <div class="marketing-card" data-item-id="other-3">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">Event Booth Setup</div>
                    <div class="marketing-card-cost">$28,000.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Components</div>
                        <div class="marketing-detail-value">Pop-up display, banners, standees, welcome desk</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Duration</div>
                        <div class="marketing-detail-value">3 days</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Location</div>
                        <div class="marketing-detail-value">Main conference hall</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('other-3', 'Event Booth Setup', 28000)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>

            <div class="marketing-card" data-item-id="other-4">
                <div class="marketing-card-header">
                    <div class="marketing-card-title">Event Transport</div>
                    <div class="marketing-card-cost">$5,000.00</div>
                </div>
                <div class="marketing-card-body">
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Service</div>
                        <div class="marketing-detail-value">Material Handling & Logistics</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Vehicles</div>
                        <div class="marketing-detail-value">2 trucks, 1 van</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Duration</div>
                        <div class="marketing-detail-value">5 days</div>
                    </div>
                    <div class="marketing-detail">
                        <div class="marketing-detail-label">Status</div>
                        <div class="marketing-detail-value">
                            <span class="status-badge available">
                                <i class="fas fa-check-circle"></i>
                                Available
                            </span>
                        </div>
                    </div>
                    <div class="sponsor-section">
                        <input type="number" class="sponsor-amount" placeholder="Amount" min="1">
                        <button class="sponsor-btn" onclick="sponsorItem('other-4', 'Event Transport', 5000)">
                            <i class="fas fa-hand-holding-heart"></i>
                            Sponsor Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Total Cost Section -->
<div class="total-section">
    <h3>Total Program Sponsorship Goal</h3>
    <div class="total-amount">$149,855.00</div>
    <div class="total-description">Help us reach our goal by sponsoring one or more marketing items</div>
</div>

<!-- Payment Details Section -->
<div class="payment-details">
    <div class="section-header" style="margin: -2rem -2rem 1.5rem -2rem; border-radius: 24px 24px 0 0;">
        <i class="fas fa-credit-card"></i>
        <span>Payment Details</span>
    </div>
    <div class="payment-grid">
        <div class="payment-card">
            <div class="marketing-detail">
                <div class="marketing-detail-label"><i class="fas fa-university"></i> Bank</div>
                <div class="marketing-detail-value">SBI Bank</div>
            </div>
        </div>
        <div class="payment-card">
            <div class="marketing-detail">
                <div class="marketing-detail-label"><i class="fas fa-sort-numeric-down"></i> Sort Code</div>
                <div class="marketing-detail-value">SBIN0000867</div>
            </div>
        </div>
        <div class="payment-card">
            <div class="marketing-detail">
                <div class="marketing-detail-label"><i class="fas fa-wallet"></i> Account Number</div>
                <div class="marketing-detail-value">321XXXXXX6789</div>
            </div>
        </div>
        <div class="payment-card">
            <div class="marketing-detail">
                <div class="marketing-detail-label"><i class="fas fa-hashtag"></i> Payment Reference</div>
                <div class="marketing-detail-value">0001/25</div>
            </div>
        </div>
    </div>
</div>

<!-- Sponsorship Modal -->
<div id="sponsorModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="modal-header">
            <h2>Confirm Sponsorship</h2>
            <p>Please review your sponsorship details before confirming</p>
        </div>
        <div class="modal-body">
            <div class="sponsor-summary">
                <div class="summary-item">
                    <span class="summary-label">Item:</span>
                    <span class="summary-value" id="modalItemName">-</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Original Cost:</span>
                    <span class="summary-value" id="modalOriginalCost">-</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Your Sponsorship:</span>
                    <span class="summary-value summary-highlight" id="modalSponsorAmount">-</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Remaining Cost:</span>
                    <span class="summary-value" id="modalRemainingCost">-</span>
                </div>
            </div>
            <p style="margin-top: 20px; color: var(--gray-600); font-size: 0.9rem;">
                <i class="fas fa-info-circle" style="color: var(--red-600);"></i> 
                After confirmation, you'll receive an email with payment instructions and sponsorship certificate.
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            <button class="btn btn-primary" id="confirmSponsor">Confirm Sponsorship</button>
        </div>
    </div>
</div>

<script>
    let currentSponsorshipData = {};

    function sponsorItem(itemId, itemName, originalCost) {
        const card = document.querySelector(`.marketing-card[data-item-id="${itemId}"]`);
        const amountInput = card.querySelector('.sponsor-amount');
        const sponsorAmount = parseFloat(amountInput.value);

        if (!sponsorAmount || sponsorAmount <= 0) {
            alert('Please enter a valid sponsorship amount.');
            return;
        }

        if (sponsorAmount > originalCost) {
            alert(`Sponsorship amount cannot exceed the original cost of $${originalCost.toFixed(2)}`);
            return;
        }

        currentSponsorshipData = {
            itemId: itemId,
            itemName: itemName,
            originalCost: originalCost,
            sponsorAmount: sponsorAmount,
            remainingCost: originalCost - sponsorAmount
        };

        // Update modal content
        document.getElementById('modalItemName').textContent = itemName;
        document.getElementById('modalOriginalCost').textContent = `$${originalCost.toFixed(2)}`;
        document.getElementById('modalSponsorAmount').textContent = `$${sponsorAmount.toFixed(2)}`;
        document.getElementById('modalRemainingCost').textContent = `$${(originalCost - sponsorAmount).toFixed(2)}`;

        showSponsorModal();
    }

    function showSponsorModal() {
        const modal = document.getElementById('sponsorModal');
        modal.style.display = "block";

        // Close modal when clicking on X
        document.querySelector('.close').onclick = function() {
            modal.style.display = "none";
        }

        // Confirm sponsorship button
        document.getElementById('confirmSponsor').onclick = function() {
            completeSponsorship();
        }
    }

    function completeSponsorship() {
        const modal = document.getElementById('sponsorModal');
        
        // Here you would typically send the data to your server
        // For this demo, we'll just update the UI
        
        const card = document.querySelector(`.marketing-card[data-item-id="${currentSponsorshipData.itemId}"]`);
        
        // Update status to sponsored
        const statusBadge = card.querySelector('.status-badge');
        statusBadge.className = 'status-badge sponsored';
        statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Sponsored';
        
        // Disable the input and button
        const amountInput = card.querySelector('.sponsor-amount');
        amountInput.disabled = true;
        
        const sponsorBtn = card.querySelector('.sponsor-btn');
        sponsorBtn.disabled = true;
        sponsorBtn.innerHTML = '<i class="fas fa-check-circle"></i> Sponsored';
        
        // Update the total marketing amount (just for demo)
        const totalMarketing = document.querySelector('.total-amount');
        let currentTotal = parseFloat(totalMarketing.textContent.replace(/[^0-9.-]+/g,""));
        currentTotal -= currentSponsorshipData.sponsorAmount;
        totalMarketing.textContent = `$${currentTotal.toFixed(2)}`;
        
        // Close the modal
        modal.style.display = "none";
        
        // Show confirmation
        alert(`Thank you for sponsoring ${currentSponsorshipData.itemName} with $${currentSponsorshipData.sponsorAmount.toFixed(2)}!`);
    }

    function closeModal() {
        document.getElementById('sponsorModal').style.display = "none";
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('sponsorModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>