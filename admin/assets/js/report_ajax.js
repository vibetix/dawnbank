$(document).ready(function () {
    let reportChart = null;

    // Fetch and display report when "Generate Report" is clicked
    $("#generate").on("click", function () {
        let reportType = $("#report-type").val();
        generateReport(reportType);
    });

    // Export buttons
    $("#export-pdf").on("click", exportToPDF);
    $("#export-csv").on("click", exportToCSV);

    function generateReport(reportType) {
        $.ajax({
            url: "../includes/fetch_report.php",
            type: "POST",
            data: { reportType: reportType },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    updateTable(response.data);
                    updateChart(response.data);
                } else {
                    console.error("Failed to fetch report:", response.message);
                }
            },
            error: function () {
                console.error("Error fetching report data.");
            }
        });
    }

    function updateTable(data) {
        let tableBody = $("#report-table tbody");
        tableBody.empty(); 

        data.forEach(row => {
            let tableRow = `
                <tr>
                    <td>${row.transaction_id}</td>
                    <td>${row.full_name}</td>
                    <td>${row.amount}</td>
                    <td>${row.transaction_type}</td>
                    <td>${row.transaction_date}</td>
                </tr>
            `;
            tableBody.append(tableRow);
        });
    }

    function updateChart(data) {
        const ctx = document.getElementById("reportChart").getContext("2d");

        const labels = data.map(item => item.transaction_date);
        const amounts = data.map(item => parseFloat(item.amount));

        if (reportChart !== null) {
            reportChart.destroy();
        }

        reportChart = new Chart(ctx, {
            type: "pie",
            data: {
                labels: labels,
                datasets: [{
                    label: "Transaction Amount ($)",
                    data: amounts,
                    backgroundColor: "#246bfd",
                    borderColor: "#1d4ed8",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    function exportToPDF() {
        $.ajax({
            url: "../includes/fetch_report.php",
            type: "POST",
            data: { reportType: $("#report-type").val() },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();

                    doc.text("Admin Report", 20, 10);
                    doc.autoTable({
                        head: [["Transaction ID", "User", "Amount", "Type", "Date"]],
                        body: response.data.map(row => [
                            row.transaction_id, row.full_name, row.amount, row.transaction_type, row.transaction_date
                        ]),
                        startY: 20
                    });

                    doc.save("Admin_Report.pdf");
                } else {
                    console.error("Error exporting PDF:", response.message);
                }
            },
            error: function () {
                console.error("Error fetching report for PDF.");
            }
        });
    }

    function exportToCSV() {
        $.ajax({
            url: "../includes/fetch_report.php",
            type: "POST",
            data: { reportType: $("#report-type").val() },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    let csv = "Transaction ID,User,Amount,Type,Date\r\n";
                    response.data.forEach(row => {
                        csv += `"${row.transaction_id}","${row.full_name}","${row.amount}","${row.transaction_type}","${row.transaction_date}"\r\n`;
                    });

                    const blob = new Blob([csv], { type: "text/csv" });
                    const a = document.createElement("a");
                    a.href = URL.createObjectURL(blob);
                    a.download = "Admin_Report.csv";
                    a.click();
                } else {
                    console.error("Error exporting CSV:", response.message);
                }
            },
            error: function () {
                console.error("Error fetching report for CSV.");
            }
        });
    }
});
