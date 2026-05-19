@extends('layouts.admin') @section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bảng thống kê tổng quan</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Xuất báo cáo</a>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Doanh thu (Tháng này)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['doanh_thu_thang'] ?? 0, 0, ',', '.') }} VNĐ</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-calendar fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Doanh thu (Năm nay)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['doanh_thu_nam'] ?? 0, 0, ',', '.') }} VNĐ</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Đơn hàng chờ xử lý</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['don_hang_cho'] ?? 0 }} Đơn</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-comments fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Biểu đồ doanh thu</h6>
                    <select id="revenueFilter" class="form-control form-control-sm" style="width: 150px;">
                        <option value="thang">Theo tháng (2026)</option>
                        <option value="nam">Theo năm</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tỉ trọng sản phẩm bán ra</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2" style="height: 250px;">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small" id="pieChartLegend">
                        </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. BIỂU ĐỒ ĐƯỜNG (DOANH THU)
    const chartData = @json($chartData);
    const ctxArea = document.getElementById("myAreaChart").getContext('2d');
    
    let myLineChart = new Chart(ctxArea, {
        type: 'line',
        data: {
            labels: chartData.thang.labels,
            datasets: [{
                label: "Doanh thu (VNĐ)",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: chartData.thang.data, // Truyền mảng dữ liệu thực từ Backend
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Bắt sự kiện đổi Dropdown
    document.getElementById('revenueFilter').addEventListener('change', function() {
        const type = this.value; 
        myLineChart.data.labels = chartData[type].labels;
        myLineChart.data.datasets[0].data = chartData[type].data;
        myLineChart.update(); 
    });


    // 2. BIỂU ĐỒ TRÒN (TỈ TRỌNG)
    const pieData = @json($pieData);
    const ctxPie = document.getElementById("myPieChart").getContext('2d');
    
    let myPieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: pieData.labels,
            datasets: [{
                data: pieData.data,
                backgroundColor: pieData.colors,
                hoverBackgroundColor: pieData.colors,
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });

    // Tạo HTML chú thích
    let legendHtml = '';
    for(let i=0; i<pieData.labels.length; i++) {
        legendHtml += `<span class="mr-2">
            <i class="fas fa-circle" style="color: ${pieData.colors[i]}"></i> ${pieData.labels[i]}
        </span>`;
        if ((i + 1) % 3 === 0) legendHtml += '<br>';
    }
    document.getElementById('pieChartLegend').innerHTML = legendHtml;
});
</script>
@endsection