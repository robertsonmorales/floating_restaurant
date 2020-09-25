@extends('layouts.app')
@section('title', $title)
@section('content')
<div class="dashboard-container">
    <div class="dashboard-cards">

        <div class="reports-card">
            <div class="reports">                
                <h4>Sales</h4>
                <div class="report-record">
                    <h2>₱ 150</h2>
                    <span class="card-icon" id="sales-icon">
                        <i data-feather="database"></i>
                    </span>
                </div>
            </div>

            <div class="reports">
                <h4>Expenses</h4>
                <div class="report-record">
                    <h2>₱ 100</h2>
                    <span class="card-icon" id="expenses-icon">
                        <i data-feather="book"></i>
                    </span>
                </div>
            </div>

            <div class="reports">
                <h4>Orders</h4>
                <div class="report-record">
                    <h2>100</h2>
                    <span class="card-icon" id="order-icon">
                        <i data-feather="package"></i>
                    </span>
                </div>
            </div>

            <div class="reports">                
                <h4>Today's Customers</h4>
                <div class="report-record">
                    <h2>150</h2>
                    <span class="card-icon" id="customer-icon">
                        <i data-feather="user-check"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="exp-vs-sales">
            <h4>Annual Sales & Expenses</h4>
            <div id="report-chart" class="report-chart"></div>
        </div>

        <div class="report-details">
            <div class="details1">1</div>
            <div class="details2">2</div>
        </div>
    </div>
</div>
<br>
@endsection
@section('scripts')
<script>
$(document).ready(function(){
    var token = $("meta[name='csrf-token']").attr("content");
    var animations = {
        enabled: true,
        easing: 'easeinout',
        speed: 800,
        animateGradually: {
            enabled: true,
            delay: 150
        },
        dynamicAnimation: {
           enabled: true,
           speed: 350
        }
    };

    var options = {
        colors: ["#ff4560", "#0e76bd"],
        series: [
            {
                name: 'Expenses',
                data: [30,40,35,50,49,60,70,91,125,130,20,30],
            },
            {
                name: 'Sales',
                data: [20,30,25,60,39,80,70,121,124,100,50,40],
            },
            
        ],
        chart: {
            type: 'area',
            stacked: false,
            markers: {
                size: 4,
            },
            animations: animations,
            toolbar: {
                show: false,
            },
            fontSize: '12px',
            fontWeight: '500',
            fontFamily: ['Poppins', 'Montserrat'],
            color: '#3e4044'
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'left',
            fontSize: '12px',
            fontWeight: '500',
            fontFamily: 'inherit',
            color: '#3e4044'
        },
        stroke: {
            // dashArray: [5, 0],
            width: [3, 3],
            curve: 'smooth'
        },
        fill: {
            opacity: [0.8, 0.8],
            type: ['gradient', 'gradient']
        },
        xaxis: {
            categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        },
        dataLabels: {
            enabled: false,
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (y) {
                  if (typeof y !== "undefined") {
                    return "₱" + y.toFixed(0);
                  }
                  return y;

                }
            },
            fontSize: '12px',
            fontWeight: '500',
            fontFamily: ['Poppins', 'Montserrat'],
            color: '#3e4044'
        }
    }

    var chart = new ApexCharts(document.querySelector("#report-chart"), options);

    chart.render();
});
</script>
@endsection