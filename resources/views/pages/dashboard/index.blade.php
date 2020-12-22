@extends('layouts.app')
@section('title', $title)
@section('content')
<div class="mx-4">

    <div class="row no-gutters">

        <div class="col card mr-3 mb-3 p-xl-2">
            <div class="card-body d-flex flex-column justify-content-between align-items-center flex-xl-row-reverse w-100">
                <span class="card-icon mb-3 mb-xl-0" id="sales-icon">
                    <i data-feather="database"></i>
                </span>
                <div class="d-flex flex-column align-items-center align-items-xl-start">
                    <span class="card-title h3 font-weight-bold mb-1">₱ 150</span>
                    <span class="card-subtitle text-muted">Sales</span>
                </div>
            </div>
        </div>

        <div class="col card mr-lg-3 mb-3 p-xl-2">
            <div class="card-body d-flex flex-column justify-content-between align-items-center flex-xl-row-reverse w-100">
                <span class="card-icon mb-3 mb-xl-0" id="sales-icon">
                    <i data-feather="book"></i>
                </span>
                <div class="d-flex flex-column align-items-center align-items-xl-start">
                    <span class="card-title h3 font-weight-bold mb-1">₱ 150</span>
                    <span class="card-subtitle text-muted">Expenses</span>
                </div>
            </div>
        </div>

        <div class="w-100 d-block d-lg-none"></div>

        <div class="col card mr-3 mb-3 p-xl-2">
            <div class="card-body d-flex flex-column justify-content-between align-items-center flex-xl-row-reverse w-100">
                <span class="card-icon mb-3 mb-xl-0" id="sales-icon">
                    <i data-feather="package"></i>
                </span>
                <div class="d-flex flex-column align-items-center align-items-xl-start">
                    <span class="card-title h3 font-weight-bold mb-1">150</span>
                    <span class="card-subtitle text-muted">Orders</span>
                </div>
            </div>
        </div>

        <div class="col card mb-3 p-xl-2">
            <div class="card-body d-flex flex-column justify-content-between align-items-center flex-xl-row-reverse w-100">
                <span class="card-icon mb-3 mb-xl-0" id="sales-icon">
                    <i data-feather="user-check"></i>
                </span>
                <div class="d-flex flex-column align-items-center align-items-xl-start">
                    <span class="card-title h3 font-weight-bold mb-1">150</span>
                    <span class="card-subtitle text-muted">Customers</span>
                </div>
            </div>
        </div>        

    </div>

    <div class="row no-gutters d-flex justify-content-between flex-column flex-lg-row">
        <div class="col col-md-12 col-lg-6 card mr-lg-3 mb-3 mb-lg-0">
            <div class="card-body w-100">
                <span class="card-subtitle text-muted">Annual Sales & Expenses</span>
                <div id="report-chart" class="report-chart"></div>
            </div>
        </div>

        <div class="w-100 d-lg-none"></div>
    
        <div class="col card mr-lg-3 mb-3 mb-lg-0">
            <div class="card-body w-100">
                <span class="card-subtitle text-muted">Stocks</span>
            </div>
        </div>

        <div class="w-100 d-lg-none"></div>

        <div class="col card mb-3 mb-lg-0">
            <div class="card-body w-100">
                <span class="card-subtitle text-muted">On-going Orders</span>
            </div>
        </div>
    </div>
</div>
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
            fontSize: '1em',
            fontWeight: '500',
            fontFamily: ['Poppins', 'Montserrat', 'Segoe UI'],
            color: '#3e4044'
        },
        grid: {
            show: false,
            // borderColor: '#ddd',
            // strokeDashArray: 0,
            // position: 'back',
            // xaxis: {
            //     lines: {
            //         show: false
            //     }
            // },   
            // yaxis: {
            //     lines: {
            //         show: true
            //     }
            // },  
        },
        legend: {
            show: false,
            position: 'bottom',
            horizontalAlign: 'center',
            fontWeight: '500',
            fontFamily: ['Poppins', 'Montserrat', 'Segoe UI'],
            color: '#3e4044'
        },
        stroke: {
            width: [3, 3],
            curve: 'smooth'
        },
        fill: {
            opacity: [1, 1],
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
            fontSize: '1em',
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