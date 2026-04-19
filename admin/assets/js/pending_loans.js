$(document).ready(function(){
    function loadPendingLoans() {
        $.ajax({
            url: "../includes/get_pending_loans.php",
            method: "GET",
            dataType: "json",
            success: function(data){
                let tableBody = "";
                if (data.length > 0) {
                data.forEach((loan, index) => {
                    tableBody += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${loan.full_name}</td>
                            <td>${loan.loan_amount}</td>
                            <td>${loan.loan_purpose}</td>
                            <td class="status-pill status-warning" style="text-transform:capitalize;">${loan.status}</td>
                            <td>
                                 <button class="button-primary approveBtn" data-id="${loan.loan_id}" title="Approve">
                                        <img src="assets/images/icons/approve.png">
                                    </button>
                                    <button class="button-danger declineBtn" data-id="${loan.loan_id}" title="Decline">
                                        <img src="assets/images/icons/cross.png">
                                    </button>
                            </td>
                        </tr>
                    `;
                });

                $("#loanApprovalTableBody").html(tableBody);
            } else {
                    tableBody += `
                        <tr>
                        <td colspan="6">No pending approvals.</td>
                        </tr>
                    `;
                    $("#loanApprovalTableBody").html(tableBody);
                }
        }
    });
}
     // Approve User
    $(document).on("click", ".approveBtn", function () {
        let loanId = $(this).data("id");


        if (confirm("Are you sure you want to approve this loan?")) {
           $.ajax({
            url: '../includes/approve_loan.php',
            type: 'POST',
            data: { 
                loan_id: loanId // <-- make sure you have this variable
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === "success") {
                    alert(response.message);
                    loadPendingLoans();
                } else {
                    alert(response.message);
                }
            }
        });

        }
    });

    // Decline User
    $(document).on("click", ".declineBtn", function () {
        let loanId = $(this).data("id");

        if (confirm("Are you sure you want to decline this loan?")) {
            $.ajax({
                url: "../includes/delete_loan.php",
                type: "POST",
                data: {  
                loan_id: loanId // <-- make sure you have this variable
                 },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        alert("Loan declined and removed.");
                        loadPendingLoans();
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function () {
                    alert("Failed to decline loan.");
                }
            });
        }
    });
    loadPendingLoans();
});
