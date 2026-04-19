$(document).ready(function () {
    $.ajax({
        url: "../includes/get_stats.php",
        type: "GET",
        dataType: "json",
        success: function (data) {
            $(".stat-value").eq(0).text(data.total_users);
            $(".stat-value").eq(1).text(data.total_transactions);
            $(".stat-value").eq(2).text(data.loan_requests);
            $(".stat-value").eq(3).text(data.revenue);
            $(".stat-value").eq(4).text(data.total_balance);
            $(".stat-value").eq(5).text(data.total_deposits);
            $(".stat-value").eq(6).text(data.total_withdrawals);
            $(".stat-value").eq(7).text(data.total_transfers);
        },
        error: function () {
            console.log("Error fetching statistics.");
        }
    });
});
