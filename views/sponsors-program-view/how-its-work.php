<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sponsor Our Events - Partnership Opportunities</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #d32f2f;
      --primary-dark: #b71c1c;
      --primary-light: #ff6659;
      --secondary-color: #f44336;
      --primary-gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
      --secondary-gradient: linear-gradient(135deg, var(--primary-light) 0%, var(--secondary-color) 100%);
      --card-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      --card-shadow-hover: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    body {
      background: #f5f5f5;
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding-top: 80px;
    }

    /* Header Styles */
    .navbar {
      background-color: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      padding: 10px 0;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
    }

    .navbar-brand img {
      height: 50px;
      transition: all 0.3s ease;
    }

    .navbar-brand img:hover {
      transform: scale(1.05);
    }

    .nav-link {
      color: #333 !important;
      font-weight: 500;
      padding: 8px 15px !important;
      margin: 0 5px;
      border-radius: 5px;
      transition: all 0.3s ease;
    }

    .nav-link:hover,
    .nav-link.active {
      color: var(--primary-color) !important;
      background-color: rgba(211, 47, 47, 0.1);
    }

    .dropdown-menu {
      border: none;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      padding: 10px 0;
    }

    .dropdown-item {
      padding: 8px 20px;
      transition: all 0.3s ease;
    }

    .dropdown-item:hover {
      background-color: rgba(211, 47, 47, 0.1);
      color: var(--primary-color);
    }

    .profile-img {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--primary-color);
      transition: all 0.3s ease;
    }

    .profile-img:hover {
      transform: scale(1.1);
    }

    /* Hero Section */
    .hero-section {
      background: var(--primary-gradient);
      color: white;
      padding: 3rem 0;
      position: relative;
      overflow: hidden;
      margin-top: -20px;
    }

    .hero-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    }

    .hero-content {
      position: relative;
      z-index: 2;
    }

    /* Filter Section */
    .filter-section {
      background: white;
      border-radius: 10px;
      box-shadow: var(--card-shadow);
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .filter-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .filter-buttons {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }

    .filter-dropdown {
      position: relative;
    }

    .filter-dropdown-menu {
      position: absolute;
      top: 100%;
      right: 0;
      width: 900px;
      /* wider dropdown */
      background: white;
      border-radius: 10px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      padding: 1rem;
      z-index: 100;
      display: none;
    }


    .filter-dropdown-menu.show {
      display: block;
    }

    .filter-dropdown-item {
      margin-bottom: 0.8rem;
    }

    .filter-dropdown-item label {
      font-size: 0.85rem;
      font-weight: 500;
      color: #4a5568;
      display: block;
      margin-bottom: 0.3rem;
    }

    .filter-dropdown-item input,
    .filter-dropdown-item select {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      font-size: 0.85rem;
    }

    .filter-dropdown-footer {
      display: flex;
      justify-content: space-between;
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid #e2e8f0;
    }

    /* Program Cards */
    .programs-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1.5rem;
    }

    .program-card {
      background: white;
      border-radius: 15px;
      box-shadow: var(--card-shadow);
      transition: all 0.3s ease;
      overflow: hidden;
      border: none;
      position: relative;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .program-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: var(--secondary-gradient);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .program-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--card-shadow-hover);
    }

    .program-card:hover::before {
      transform: scaleX(1);
    }

    .program-image {
      height: 150px;
      overflow: hidden;
      position: relative;
    }

    .program-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .program-card:hover .program-image img {
      transform: scale(1.05);
    }

    .program-badge {
      position: absolute;
      top: 0.8rem;
      right: 0.8rem;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(5px);
      padding: 0.3rem 0.8rem;
      border-radius: 12px;
      font-size: 0.7rem;
      font-weight: 600;
      color: var(--primary-color);
      z-index: 2;
    }

    .card-header-custom {
      background: white;
      border: none;
      padding: 1rem;
      position: relative;
    }

    .program-number {
      background: var(--primary-gradient);
      color: white;
      padding: 0.2rem 0.7rem;
      border-radius: 15px;
      font-size: 0.75rem;
      font-weight: 600;
      display: inline-block;
      margin-bottom: 0.5rem;
    }

    .program-title {
      font-size: 1rem;
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 0.5rem;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .card-body-custom {
      padding: 0 1rem 1rem;
      flex-grow: 1;
    }

    .info-item {
      display: flex;
      align-items: center;
      margin-bottom: 0.6rem;
      padding: 0.2rem 0;
    }

    .info-icon {
      width: 28px;
      height: 28px;
      background: var(--primary-gradient);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 0.6rem;
      color: white;
      font-size: 0.7rem;
      flex-shrink: 0;
    }

    .info-content {
      flex: 1;
      min-width: 0;
    }

    .info-label {
      font-size: 0.7rem;
      color: #718096;
      font-weight: 500;
      margin-bottom: 0.1rem;
    }

    .info-value {
      font-size: 0.8rem;
      color: #2d3748;
      font-weight: 600;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .participants-bar {
      background: #e2e8f0;
      height: 6px;
      border-radius: 3px;
      overflow: hidden;
      margin-top: 0.3rem;
    }

    .participants-fill {
      background: var(--secondary-gradient);
      height: 100%;
      border-radius: 3px;
      transition: width 0.8s ease;
      width: 50%;
    }

    .btn-marketing {
      background: var(--secondary-gradient);
      border: none;
      color: white;
      padding: 0.5rem 1.2rem;
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.8rem;
      transition: all 0.3s ease;
      width: 100%;
    }

    .btn-marketing:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(244, 67, 54, 0.3);
    }

    .section-title {
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 1rem;
    }

    .section-subtitle {
      color: #718096;
      font-size: 1rem;
      margin-bottom: 2rem;
    }

    .pagination-container {
      display: flex;
      justify-content: center;
      margin-top: 2rem;
    }

    .page-item.active .page-link {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .page-link {
      color: var(--primary-color);
    }

    @media (max-width: 768px) {
      .hero-section {
        padding: 2rem 0;
      }

      .section-title {
        font-size: 1.8rem;
      }

      .programs-container {
        grid-template-columns: 1fr;
      }

      .filter-dropdown-menu {
        width: 400px;
      }
    }

    .fade-in {
      animation: fadeIn 0.6s ease-in;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body cz-shortcut-listen="true">


  <!-- Header -->
  
<?php include 'Includes/nav.php'; ?>

<!-- Header -->


<style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            font-family: 'Inter', sans-serif;
        }

        .steps-section {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(60, 72, 88, 0.12);
            padding: 2.5rem 2rem;
        }

        .steps-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .steps-header h2 {
            font-weight: 700;
            color: #dc2626;
            margin-bottom: 0.5rem;
        }

        .steps-header p {
            color: #64748b;
            font-size: 1.1rem;
        }

        .step-card {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            background: linear-gradient(90deg, #fef2f2 0%, #f1f5f9 100%);
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(220, 38, 38, 0.07);
            padding: 1.5rem 1.25rem;
            margin-bottom: 1.5rem;
            transition: box-shadow 0.2s;
        }

        .step-card:hover {
            box-shadow: 0 8px 24px rgba(220, 38, 38, 0.13);
        }

        .step-icon {
            flex-shrink: 0;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 4px 16px rgba(220, 38, 38, 0.12);
        }

        .step-content h5 {
            font-weight: 600;
            color: #b91c1c;
            margin-bottom: 0.5rem;
        }

        .step-content p {
            color: #374151;
            font-size: 1.05rem;
        }

        @media (max-width: 600px) {
            .steps-section {
                padding: 1.2rem 0.5rem;
            }

            .step-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .step-icon {
                margin-bottom: 0.8rem;
            }
        }
    </style><div class="steps-section">
        <div class="steps-header">
            <h2><i class="fas fa-route me-2"></i>How Sponsorship Works</h2>
            <p>Follow these simple steps to become a sponsor and support our programs.</p>
        </div>
        <!-- Step 1 -->
        <div class="step-card">
            <div class="step-icon">
                <i class="fas fa-search"></i>
            </div>
            <div class="step-content">
                <h5>1. Explore Programs</h5>
                <p>Browse our list of upcoming programs and select the one you wish to support. Each program has detailed information about its goals and needs.</p>
            </div>
        </div>
        <!-- Step 2 -->
        <div class="step-card">
            <div class="step-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <div class="step-content">
                <h5>2. Choose Sponsorship Area</h5>
                <p>Select a specific area to sponsor, such as Radio, TV, Newspaper, Billboard, Facebook, Instagram, or Pamphlet. Each area shows the remaining amount needed.</p>
            </div>
        </div>
        <!-- Step 3 -->
        <div class="step-card">
            <div class="step-icon">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <div class="step-content">
                <h5>3. Enter Your Contribution</h5>
                <p>Decide the amount you wish to contribute. You can sponsor any amount up to the remaining requirement for your chosen area.</p>
            </div>
        </div>
        <!-- Step 4 -->
        <div class="step-card">
            <div class="step-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="step-content">
                <h5>4. Confirm & Submit</h5>
                <p>Review your sponsorship details and confirm your contribution. You’ll receive payment instructions and a sponsorship certificate via email.</p>
            </div>
        </div>
        <!-- Step 5 -->
        <div class="step-card">
            <div class="step-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="step-content">
                <h5>5. Track Your Impact</h5>
                <p>Log in to your profile to view your sponsorship history and see the impact you’ve made on our programs.</p>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>