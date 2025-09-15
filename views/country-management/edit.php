<?php 


include '../Includes/header.php'; 
require '../../config/config.php'; // Ensure database connection is established
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Country Management');

// Check if user has permission to add countries
$canadd = canUsercan_edit($pdo, 'Country Management'); // <-- use new
// If user does not have permission, redirect or show an error
if (!$canadd) {
    header("Location: ../../unauthorized.php");
    exit;
}
// Initialize empty values
$country_name = $country_code = $currency = $language = "";
$id = "";

// Check if editing mode
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Fetch country details
    $stmt = $pdo->prepare("SELECT * FROM countries WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $country = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($country) {
        $country_name = $country['country_name'];
        $country_code = $country['country_code'];
        $currency = $country['currency'];
        $language = $country['language'];
    }
}
?>

<div class="app-main__outer">
    <div class="app-main__inner h-100">
        <div class="app-page-title app-page-title-simple">
            <div class="page-title-wrapper d-flex justify-content-between">
                <div class="page-title-heading"></div>
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="Country_management.php">Country Management</a></li>
                        <li class="breadcrumb-item"><a href="add_Country.php">Add New Country</a></li>
                    </ol>
                </nav>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title"><?= $id ? "Edit Country" : "Add Country" ?></h5>
                            <form class="needs-validation" novalidate="true" action="save.php" method="post">
                                <div class="form-row">
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustom01">Country Name</label>
                                        <input type="text" class="form-control" id="validationCustom01"
                                            placeholder="Enter Country Name" required="true" name="country_name" value="<?= htmlspecialchars($country_name) ?>">
                                        <div class="invalid-feedback">Please enter Country name.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustom02">Country Code</label>
                                        <input type="text" class="form-control" id="validationCustom02"
                                            placeholder="Enter Country Code" required="true" name="country_code" value="<?= htmlspecialchars($country_code) ?>">
                                        <div class="invalid-feedback">Please enter Country Code.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustom03">Currency</label>
                                        <input type="text" class="form-control" id="validationCustom03"
                                            placeholder="Enter Currency" required="true" name="currency" value="<?= htmlspecialchars($currency) ?>">
                                        <div class="invalid-feedback">Please enter Currency.</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="validationCustom04">Language</label>
                                        <input type="text" class="form-control" id="validationCustom04"
                                            placeholder="Enter Language" required="true" name="language" value="<?= htmlspecialchars($language) ?>">
                                        <div class="invalid-feedback">Please enter Language.</div>
                                    </div>
                                    
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-outline-dark mx-3" type="button" onclick="history.back();">Back</button>
                                    <button class="btn btn-success" type="submit">Save</button>
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<?php include '../Includes/footer.php'; ?>

<script>
    $(document).ready(function () {
        // Get the message from the URL
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message'); 

        if (message) {
            let title, icon;
            
            if (message.toLowerCase().includes('success')) {
                title = "Success";
                icon = "success";
            } else {
                title = "Error";
                icon = "error";
            }

            swal({
                title: title,
                text: message,
                icon: icon
            }).then(() => {
                const url = new URL(window.location.href);
                url.searchParams.delete('message'); 
                window.history.replaceState({}, document.title, url.toString());
            });
        }
    });
</script>

</body>
</html>
