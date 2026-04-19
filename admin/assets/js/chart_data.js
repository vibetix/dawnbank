$(document).ready(function() {
    // Fetch user growth data
    $.ajax({
        url: '../includes/fetch_charts_data.php',
        type: 'GET',
        dataType: 'json',
        data: { chart: 'user_growth' },
        success: function(response) {
            let ctx = document.getElementById('userGrowthChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: response.labels,
                    datasets: [{
                        label: 'Users',
                        data: response.data,
                        backgroundColor: 'rgba(12, 165, 235, 0.2)',
                        borderColor: '#0ca5eb',
                        borderWidth: 2,
                        fill: true
                    }]
                },
                options: { responsive: true }
            });
        }
    });

    // Fetch revenue breakdown data
    $.ajax({
        url: '../includes/fetch_charts_data.php',
        type: 'GET',
        dataType: 'json',
        data: { chart: 'revenue_breakdown' },
        success: function(response) {
            let ctx = document.getElementById('revenueBreakdownChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: response.labels,
                    datasets: [{
                        data: response.data,
                        backgroundColor: ['#0ca5eb', '#34C7C7', '#60DAB7', '#C1E0F4']
                    }]
                },
                options: { responsive: true }
            });
        }
    });
});