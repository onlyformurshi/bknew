document.addEventListener("DOMContentLoaded", function () {
    // Add event listener to the search close button
    document.getElementById("search-close-new").addEventListener("click", function () {
        // Clear the search input value
        document.getElementById("searchInput-header").value = "";
        // Trigger searchTable function to reset the table
        searchTable();
    });
});



function searchTable() {
    var input, filter, table, tr, td, i, txtValue;
    var serialNumber = 1; // Initialize serial number
    input = document.getElementById("searchInput-header");
    filter = input.value.toUpperCase();
    table = document.getElementById("tabledata-new");
    tr = table.getElementsByTagName("tr");
    var visibleRowCount = 0; // Counter for visible rows

    // Remove the "No data available" row if it exists
    var lastRow = tr[tr.length - 1];
    if (lastRow && lastRow.getElementsByTagName("td")[0].textContent === "No data available") {
        table.deleteRow(-1);
    }

    // Loop through all table rows
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td");
        var isVisible = false; // Flag to check if row is visible after search

        // Loop through all table data cells in the row
        for (var j = 0; j < td.length; j++) {
            var cell = td[j];
            if (cell) {
                txtValue = cell.textContent || cell.innerText;

                // Check if the current cell's value matches the search filter
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    isVisible = true; // Row is visible
                    break; // Break the loop if match found
                }
            }
        }

        // Update the serial number and display status based on visibility
        if (isVisible) {
            tr[i].style.display = "";
            // Set the corrected serial number
            tr[i].getElementsByTagName("td")[0].innerText = serialNumber;
            serialNumber++; // Increment serial number for next visible row
            visibleRowCount++; // Increment the counter for visible rows
        } else {
            tr[i].style.display = "none"; // Hide row if not matching search filter
        }
    }

    // Check if no rows are visible after search
    if (visibleRowCount === 0) {
        // Create a new row and cell
        var newRow = table.insertRow(-1);
        var newCell = newRow.insertCell(0);

        // Span the cell across all columns
        newCell.colSpan = tr[0].getElementsByTagName("td").length;
        newCell.textContent = "No data available"; // Set the cell text
    }
}




// filter for start date and end


$(document).ready(function () {
    $("input[name='datetimes']").daterangepicker(
        {},
        function (start, end, label) {
            let startDate = start.format("YYYY-MM-DD").toString();
            let endDate = end.format("YYYY-MM-DD").toString();

            document.getElementById("startDate").innerHTML =
                "Start date: " + startDate;
            document.getElementById("endDate").innerHTML = "End date: " + endDate;

        }
    );
});







$(document).ready(function() {
    $('.select2').select2();
});

(function() {
    'use strict';
    window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                var select_drop_validation = form.querySelector('#selected-validation'); // Select within the current form
                if (select_drop_validation) {
                    if (select_drop_validation.value === "0") {
                        // If district value is 0, prevent form submission and show validation feedback
                        event.preventDefault();
                        select_drop_validation.classList.add('is-invalid'); // Apply Bootstrap's invalid class
                        document.getElementById('select-feedback').style.display = 'block'; // Show validation feedback
                    } else {
                        // If district is selected, remove validation feedback
                        select_drop_validation.classList.remove('is-invalid');
                        document.getElementById('select-feedback').style.display = 'none';
                    }
                }
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();







// multiple select drop
(function ($) {
    "use strict";

    $(function () {
        $('.chosen-select').chosen({
            max_selected_options: 5 // Set maximum selection limit to 5
        });
        $('.chosen-select-deselect').chosen({
            allow_single_deselect: true,
            max_selected_options: 5 // Set maximum selection limit to 5
        });
    });
})(jQuery);




// date reange table picker

$(document).ready(function () {
    $("input[name='datetimes']").daterangepicker(
        {
            locale: {
                format: 'MM/DD/YYYY'
            }
        },
        function (start, end, label) {
            let startDate = start.format("YYYY-MM-DD");
            let endDate = end.format("YYYY-MM-DD");
            // Display start and end dates in DD/MM/YYYY format
            let displayStartDate = start.format("DD/MM/YYYY");
            let displayEndDate = end.format("DD/MM/YYYY");
            document.getElementById("startDate").innerHTML = "Start date: " + displayStartDate;
            document.getElementById("endDate").innerHTML = "End date: " + displayEndDate;
            filterTable(startDate, endDate);
        }
    );
});

function filterTable(startDate, endDate) {
    $('#dataTable tbody tr').each(function () {
        let rowDate = $(this).find('td:nth-child(4)').text().trim();
        // Convert row date to YYYY-MM-DD format using moment.js
        let formattedRowDate = moment(rowDate, "DD/MM/YYYY").format("YYYY-MM-DD");

        if (formattedRowDate >= startDate && formattedRowDate <= endDate) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}



// serch drop select
$(document).ready(function () {
    $('.select2').select2();
});




//langouge dropdown

        $(document).ready(function() {
            // Handle language selection
            $('.dropdown-item').on('click', function(e) {
                e.preventDefault();
                
                // Get selected language, flag and text
                const selectedLang = $(this).data('lang');
                const selectedFlag = $(this).data('flag');
                const selectedText = $(this).data('text');
                
                // Update the button's flag and text
                $('#currentFlag').removeClass('flag-us flag-br flag-fr flag-sa').addClass(selectedFlag);
                $('#currentLangText').text(selectedText);
                
                // Change language
                changeLanguage(selectedLang);
            });
            
            // Function to handle language change
            function changeLanguage(langCode) {
                console.log('Language changed to: ' + langCode);
                // Here you would typically:
                // 1. Set a cookie or localStorage value
                // 2. Reload page content in new language
                // 3. Trigger any other necessary actions
                
                // Example of dispatching a custom event
                const event = new CustomEvent('languageChanged', { 
                    detail: { language: langCode }
                });
                document.dispatchEvent(event);
            }
        });
    