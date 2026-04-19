$(document).ready(function () {
    $("#transferForm").submit(function (e) {
        e.preventDefault(); // Prevent page reload
        const load = document.getElementById("load");
        load.style.display = "flex";
        let fromAccount = $("#fromAccount").val();
        let transferType = $("#transferType").val();
        let transferAmount = $("#transferAmount").val();
        let toAccount = $("#toAccount").val();
        let receiverEmail = $("#receiverEmail").val();
        let bankName = $("#bankName").val();
        let externalAccount = $("#externalAccount").val();
        let swiftCode = $("#swiftCode").val();

        // Validate amount
        if (transferAmount <= 0) {
            alert("Enter a valid transfer amount.");
            return;
        }

        // Validate fields based on transfer type
        if (transferType === "internal" && !toAccount) {
            alert("Please select a recipient.");
            return;
        }

        if (transferType === "user" && !receiverEmail) {
            alert("Enter the receiver's email.");
            return;
        }

        if (transferType === "external" && (!bankName || !externalAccount || !swiftCode)) {
            alert("Please provide external bank details.");
            return;
        }

        let formData = new FormData();
        formData.append("fromAccount", fromAccount);
        formData.append("transferType", transferType);
        formData.append("transferAmount", transferAmount);

        if (transferType === "internal") {
            formData.append("toAccount", toAccount);
        } else if (transferType === "user") {
            formData.append("receiverEmail", receiverEmail);
        } else if (transferType === "external") {
            formData.append("bankName", bankName);
            formData.append("externalAccount", externalAccount);
            formData.append("swiftCode", swiftCode);
        }

     $.ajax({
    url: "../includes/transfer.php",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
        console.log("Raw Response:", response);
        // Check if response is already an object
        let data;
        if (typeof response === "string") {
            try {
                data = JSON.parse(response);
            } catch (error) {
                console.error("JSON Parse Error:", error);
                alert("Error processing the response. Check the console for details.");
                return;
            }
        } else {
            data = response; // It's already an object
        }

        console.log("Parsed Response:", data);

        if (data.status === "success") {
            load.style.display = "none";
            alert(data.message);
            $("#transferForm")[0].reset(); // Reset form
            closeTransferModal();
        } else {
            alert(data.message);
        }
    },
    error: function (xhr, status, error) {
        console.error("AJAX Error:", xhr.responseText);
        alert("An error occurred. Please try again.");
    }
});


    });
});
