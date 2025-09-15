<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsor Marketing Portal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
       
    </style>
</head>
<body>
    <div class="container">
        <div class="sponsor-section">
            <!-- Header -->
            <div class="section-header">
                <h1 class="display-5 mb-3">
                    <i class="fas fa-handshake me-3"></i>
                    Sponsor Marketing Campaign
                </h1>
                <p class="lead mb-0">Enter amounts for each phase and view complete campaign details</p>
            </div>

            <!-- Design Phase -->
            <div class="phase-card">
                <div class="phase-header">
                    <div class="phase-icon">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                    <h3 class="phase-title">Design Phase</h3>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Designer Name:</div>
                    <div class="detail-input">
                        <input type="text" class="form-control" id="designerName" placeholder="Enter designer name">
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Design Cost:</div>
                    <div class="detail-input">
                        <input type="number" class="form-control amount-input" id="designCost" placeholder="₹ 0.00" onchange="calculateTotal()">
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Designer Invoice:</div>
                    <div class="detail-input">
                        <input type="text" class="form-control" id="designerInvoice" placeholder="Enter invoice number">
                    </div>
                </div>
            </div>

            <!-- Printing Phase -->
            <div class="phase-card">
                <div class="phase-header">
                    <div class="phase-icon">
                        <i class="fas fa-print"></i>
                    </div>
                    <h3 class="phase-title">Printing Phase</h3>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Printer Name:</div>
                    <div class="detail-input">
                        <input type="text" class="form-control" id="printerName" placeholder="Enter printer name">
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Printing Cost:</div>
                    <div class="detail-input">
                        <input type="number" class="form-control amount-input" id="printingCost" placeholder="₹ 0.00" onchange="calculateTotal()">
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Printing Invoice:</div>
                    <div class="detail-input">
                        <input type="text" class="form-control" id="printingInvoice" placeholder="Enter invoice number">
                    </div>
                </div>
            </div>

            <!-- Distribution Phase -->
            <div class="phase-card">
                <div class="phase-header">
                    <div class="phase-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3 class="phase-title">Distribution Phase</h3>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Distributor Name:</div>
                    <div class="detail-input">
                        <input type="text" class="form-control" id="distributorName" placeholder="Enter distributor name">
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Distribution Cost:</div>
                    <div class="detail-input">
                        <input type="number" class="form-control amount-input" id="distributionCost" placeholder="₹ 0.00" onchange="calculateTotal()">
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Distribution Invoice:</div>
                    <div class="detail-input">
                        <input type="text" class="form-control" id="distributionInvoice" placeholder="Enter invoice number">
                    </div>
                </div>
            </div>

            <!-- Sponsor Phase -->
            <div class="phase-card sponsor-phase">
                <div class="phase-header">
                    <div class="phase-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="phase-title">Your Sponsorship</h3>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Sponsor Amount:</div>
                    <div class="detail-input">
                        <input type="number" class="form-control amount-input" id="sponsorAmount" placeholder="₹ 0.00" onchange="calculateTotal()">
                    </div>
                </div>
            </div>

            <!-- Total Section -->
            <div class="total-section">
                <h3><i class="fas fa-calculator me-2"></i>Campaign Summary</h3>
                <div class="row mt-4">
                    <div class="col-6 col-md-3">
                        <h5>Design</h5>
                        <div class="h4">₹<span id="totalDesign">0</span></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <h5>Printing</h5>
                        <div class="h4">₹<span id="totalPrinting">0</span></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <h5>Distribution</h5>
                        <div class="h4">₹<span id="totalDistribution">0</span></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <h5>Sponsorship</h5>
                        <div class="h4">₹<span id="totalSponsor">0</span></div>
                    </div>
                </div>
                
                <hr class="my-4" style="border-color: rgba(255,255,255,0.3);">
                
                <h2>Total Campaign Value</h2>
                <div class="total-display">₹<span id="grandTotal">0</span></div>
                
                <div class="mt-4">
                    <button class="btn btn-calculate" onclick="calculateTotal()">
                        <i class="fas fa-sync-alt me-2"></i>Calculate Total
                    </button>
                    <button class="btn btn-submit" onclick="submitSponsorshipDetails()">
                        <i class="fas fa-paper-plane me-2"></i>Submit Sponsorship
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function calculateTotal() {
            // Get all amount values
            const designCost = parseFloat(document.getElementById('designCost').value) || 0;
            const printingCost = parseFloat(document.getElementById('printingCost').value) || 0;
            const distributionCost = parseFloat(document.getElementById('distributionCost').value) || 0;
            const sponsorAmount = parseFloat(document.getElementById('sponsorAmount').value) || 0;
            
            // Update individual totals
            document.getElementById('totalDesign').textContent = designCost.toLocaleString();
            document.getElementById('totalPrinting').textContent = printingCost.toLocaleString();
            document.getElementById('totalDistribution').textContent = distributionCost.toLocaleString();
            document.getElementById('totalSponsor').textContent = sponsorAmount.toLocaleString();
            
            // Calculate and display grand total
            const grandTotal = designCost + printingCost + distributionCost + sponsorAmount;
            document.getElementById('grandTotal').textContent = grandTotal.toLocaleString();
            
            // Add animation effect
            document.getElementById('grandTotal').parentElement.style.animation = 'pulse 0.5s ease-in-out';
            setTimeout(() => {
                document.getElementById('grandTotal').parentElement.style.animation = '';
            }, 500);
        }
        
        function submitSponsorshipDetails() {
            // Collect all form data
            const formData = {
                design: {
                    designerName: document.getElementById('designerName').value,
                    designCost: document.getElementById('designCost').value,
                    designerInvoice: document.getElementById('designerInvoice').value
                },
                printing: {
                    printerName: document.getElementById('printerName').value,
                    printingCost: document.getElementById('printingCost').value,
                    printingInvoice: document.getElementById('printingInvoice').value
                },
                distribution: {
                    distributorName: document.getElementById('distributorName').value,
                    distributionCost: document.getElementById('distributionCost').value,
                    distributionInvoice: document.getElementById('distributionInvoice').value
                },
                sponsorship: {
                    sponsorAmount: document.getElementById('sponsorAmount').value
                }
            };
            
            // Calculate total
            calculateTotal();
            const grandTotal = document.getElementById('grandTotal').textContent;
            
            // Show confirmation
            if (confirm(`Confirm Sponsorship Details:\n\nDesign: ₹${formData.design.designCost || 0}\nPrinting: ₹${formData.printing.printingCost || 0}\nDistribution: ₹${formData.distribution.distributionCost || 0}\nSponsorship: ₹${formData.sponsorship.sponsorAmount || 0}\n\nTotal Campaign Value: ₹${grandTotal}\n\nProceed with submission?`)) {
                alert('Thank you! Your sponsorship details have been submitted successfully.');
                console.log('Submitted Data:', formData);
                // Here you would typically send data to server
            }
        }
        
        // Auto-calculate on input
        document.addEventListener('DOMContentLoaded', function() {
            const amountInputs = document.querySelectorAll('.amount-input');
            amountInputs.forEach(input => {
                input.addEventListener('input', calculateTotal);
            });
        });
    </script>
</body>
</html>