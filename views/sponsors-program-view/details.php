<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Information</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-light">
    <div class="hero-banner">
        <div class="floating-elements">
            <div class="floating-circle"></div>
            <div class="floating-circle"></div>
            <div class="floating-circle"></div>
        </div>
        
        <div class="hero-content">
            <h1 class="hero-title">Cyber.AI.Summit 2025</h1>
            
            <p class="hero-description">
                Sheraton Grand Bengaluru Whitefield Hotel & Convention Center<br>
                Whitefield, Bengaluru, Karnataka, India
            </p>
            
            <p class="hero-date">Thu, Sep 04 2025</p>
            
            <div class="mb-4">
                <h2 class="h4 mb-4" style="font-family: 'Inter', sans-serif; font-weight: 400; opacity: 0.9;">Sponsorship Pitch</h2>
                <button class="cta-button">
                    <i class="fas fa-handshake me-2"></i>
                    Discover Sponsorship Opportunities
                </button>
            </div>
        </div>
        
        <div class="scroll-arrow">
            <i class="fas fa-chevron-down"></i>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll for arrow
        document.querySelector('.scroll-arrow').addEventListener('click', function() {
            window.scrollBy({
                top: window.innerHeight,
                behavior: 'smooth'
            });
        });
        
        // Add some interactive sparkle effect on mouse move
        document.addEventListener('mousemove', function(e) {
            if (Math.random() > 0.98) {
                createSparkle(e.clientX, e.clientY);
            }
        });
        
        function createSparkle(x, y) {
            const sparkle = document.createElement('div');
            sparkle.style.position = 'fixed';
            sparkle.style.left = x + 'px';
            sparkle.style.top = y + 'px';
            sparkle.style.width = '4px';
            sparkle.style.height = '4px';
            sparkle.style.background = 'white';
            sparkle.style.borderRadius = '50%';
            sparkle.style.pointerEvents = 'none';
            sparkle.style.zIndex = '1000';
            sparkle.style.animation = 'sparkle 1s ease-out forwards';
            
            document.body.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
            }, 1000);
        }
        
        // Add sparkle animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes sparkle {
                0% {
                    transform: scale(0);
                    opacity: 1;
                }
                50% {
                    transform: scale(1);
                    opacity: 1;
                }
                100% {
                    transform: scale(0);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-11">
                <div class="card program-card">
                    <!-- Banner Section -->
                    <div class="banner-section">
                        <img src="https://via.placeholder.com/800x200/dc3545/ffffff?text=PROGRAM+BANNER" 
                             alt="Program Banner" class="banner-image">
                        <div class="banner-overlay">
                            <h1 class="program-title">Test 16-07-2025</h1>
                            <span class="badge-custom">Active Program</span>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="content-section">
                        <!-- Program Information Grid -->
                        <div class="info-grid">
                            <div class="info-item">
                                <i class="fas fa-map-marker-alt info-icon"></i>
                                <span class="info-label">Location:</span>
                                <span class="info-value">VENUE ADDRESS NEW</span>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-building info-icon"></i>
                                <span class="info-label">Centre:</span>
                                <span class="info-value">Phoenix</span>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-user-tie info-icon"></i>
                                <span class="info-label">Instructor:</span>
                                <span class="info-value">INSTRUCTOR NAME</span>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-users info-icon"></i>
                                <span class="info-label">Participants:</span>
                                <div class="flex-grow-1">
                                    <span class="info-value">50 / 1000</span>
                                    <div class="participants-progress">
                                        <div class="participants-fill"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Program Sessions -->
                        <div class="sessions-container">
                            <h3 class="section-title">
                                <i class="fas fa-calendar-alt"></i>
                                Program Sessions
                            </h3>
                            
                            <div class="sessions-grid">
                                <div class="session-item">
                                    <div class="session-name">SESSION NAME 1</div>
                                    <div class="session-time">
                                        <i class="fas fa-clock"></i>
                                        July 17, 2025 • 12:33 PM → July 17, 2025 • 12:34 PM
                                    </div>
                                </div>
                                
                                <div class="session-item">
                                    <div class="session-name">SESSION NAME 2</div>
                                    <div class="session-time">
                                        <i class="fas fa-clock"></i>
                                        July 18, 2025 • 12:34 PM → July 19, 2025 • 12:34 PM
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <h3 class="section-title">
                                <i class="fas fa-file-alt"></i>
                                Description
                            </h3>
                            <p class="description-text">
                                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letras.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="container py-5">
  <h2 class="text-center mb-5 text-danger fw-bold">Sponsorship Opportunities</h2>
  <div class="row g-4">

    <!-- Card 1 -->
    <div class="col-md-6 col-lg-3">
      <div class="card sponsor-card p-4 text-center">
        <h5 class="card-title text-danger">Education Support</h5>
        <p class="card-text text-muted">Help a child get quality education.</p>
        <input type="number" class="form-control amount-input mb-3" placeholder="Enter Amount">
        <button class="btn sponsor-btn w-100">Sponsor</button>
      </div>
    </div>

    <!-- Card 2 -->
    <div class="col-md-6 col-lg-3">
      <div class="card sponsor-card p-4 text-center">
        <h5 class="card-title text-danger">Medical Aid</h5>
        <p class="card-text text-muted">Support emergency medical help.</p>
        <input type="number" class="form-control amount-input mb-3" placeholder="Enter Amount">
        <button class="btn sponsor-btn w-100">Sponsor</button>
      </div>
    </div>

    <!-- Card 3 -->
    <div class="col-md-6 col-lg-3">
      <div class="card sponsor-card p-4 text-center">
        <h5 class="card-title text-danger">Food Package</h5>
        <p class="card-text text-muted">Provide food for a family.</p>
        <input type="number" class="form-control amount-input mb-3" placeholder="Enter Amount">
        <button class="btn sponsor-btn w-100">Sponsor</button>
      </div>
    </div>

    <!-- Card 4 -->
    <div class="col-md-6 col-lg-3">
      <div class="card sponsor-card p-4 text-center">
        <h5 class="card-title text-danger">Event Sponsorship</h5>
        <p class="card-text text-muted">Contribute to a public event.</p>
        <input type="number" class="form-control amount-input mb-3" placeholder="Enter Amount">
        <button class="btn sponsor-btn w-100">Sponsor</button>
      </div>
    </div>

  </div>
</section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>