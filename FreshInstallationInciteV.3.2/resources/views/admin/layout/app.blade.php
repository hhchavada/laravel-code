<!DOCTYPE html>
<?php
if (Session()->has('admin_locale')) {
    $langCode = Session()->get('admin_locale');
} else {
    $langCode = config('app.fallback_locale');
}

$direction = \Helpers::getLanguageDirection($langCode);
?>
<html lang="{{$langCode}}" class="@if(isset($_COOKIE['theme'])) @if($_COOKIE['theme']=='dark') dark-style @else light-style @endif @else light-style @endif layout-navbar-fixed layout-menu-fixed" dir="{{$direction}}" data-theme="theme-default" data-assets-path="{{asset('/admin-assets/')}}" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{setting('site_admin_name')}}</title>
    <meta name="description" content="" />
    @if(setting('site_favicon')!='')
    <link rel="icon" type="image/x-icon" href="{{url('uploads/setting/'.setting('site_favicon'))}}" />
    @else
    <link rel="icon" type="image/x-icon" href="{{url('uploads/no-favicon.png')}}" />
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <script>
        var base_url = "{{url('')}}";
    </script>
    <script>
        var editorInstance; // Global variable to store the editor instance
    </script>
    <!-- All icons css -->
    <link rel="stylesheet" href="{{ asset('admin-assets/font/font.css')}}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fonts/icons.css')}}" />
    <!-- All icons css -->
    <!-- All theme css -->

    <!-- All icons css -->
    <!-- All core css -->
    @if(isset($_COOKIE['theme']))
    @if($_COOKIE['theme']=='dark')
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/css/rtl/theme-dark.css')}}" id="theme-style" />
    @else
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/css/rtl/theme.css')}}" id="theme-style" />
    @endif
    @else
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/css/rtl/theme.css')}}" id="theme-style" />
    @endif
    <!-- All icons css -->
    <!-- All plugin css -->

    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/toastr/toastr.css')}}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/css/custom.css')}}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/spinkit/spinkit.css')}}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/libs/select2/select2.css')}}" />

    <style>
        img.image-preview-cls {
            width: 150px;
            height: 150px;
        }

        label.btn.btn-primary.me-75.mb-0 {
            width: 150px;
            margin-top: 5px;
        }

        iframe.image-preview-cls {
            width: 150px;
            height: 150px;
        }

        img.image-preview-cls[src*="no-image.png"] {
            object-fit: fill;
        }

        img.image-preview-cls:not([src*="no-image.png"]) {
            object-fit: contain;
        }

        img.image-preview-cls[src*="image_preview.jpg"] {
            border: 1px dotted;
        }

        p.img-label {
            margin-top: 7px;
        }

        p.img-resolution {
            margin-top: -15px;
        }

        .overflow-auto {
            scrollbar-width: thin;
        }
        .uploded-video-url-frame {
            width: 85px;
            height: 85px;
        }
    </style>

    <!-- All plugin css -->
    <script src="{{ asset('admin-assets/vendor/js/helpers.js')}}"></script>
    <script src="{{ asset('admin-assets/js/config.js')}}"></script>
    <script>
        window.translations = {
            locale: '{{ app()->getLocale() }}',
            messages: @json(__('lang'))
        };
    </script>
</head>

<body>
    @if(Request::segment(1)=='admin')
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin/layout/component/menu')
            <div class="layout-page">
                @include('admin/layout/component/header')
                <div class="content-wrapper">
                    @yield('content') @include('admin/layout/component/footer')
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
    @else
    @yield('content')
    @endif
    <script src="{{ asset('admin-assets/vendor/libs/jquery/jquery.js')}}"></script>
    @if(Request::segment(2)!='dashboard' || Request::segment(2)!='')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    @endif
    <script src="{{ asset('admin-assets/vendor/js/bootstrap.js')}}"></script>
    <script></script>
    <script src="{{ asset('admin-assets/vendor/js/menu.js')}}"></script>
    <script src="{{ asset('admin-assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
    <script src="{{ asset('admin-assets/vendor/libs/swiper/swiper.js')}}"></script>
    <script src="{{ asset('admin-assets/vendor/libs/toastr/toastr.js')}}"></script>
    <script src="{{ asset('admin-assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{ asset('admin-assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
    <script src="{{ asset('admin-assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{ asset('admin-assets/vendor/libs/select2/select2.js')}}"></script>
    <script src="{{ asset('admin-assets/vendor/libs/tagify/tagify.js')}}"></script>
    <script src="{{ asset('admin-assets/js/main.js')}}"></script>
    <script src="{{ asset('admin-assets/vendor/libs/chartjs/chartjs.js')}}"></script>
    <script src="{{ asset('admin-assets/js/dashboards-analytics.js')}}"></script>
    <script src="{{ asset('admin-assets/js/dashboards-crm.js')}}"></script>
    <script src="{{ asset('admin-assets/js/theme.js')}}"></script>
    <script src="{{ asset('admin-assets/js/custom.js')}}"></script>
    <script src="{{ asset('admin-assets/js/ui-toasts.js')}}"></script>
    <script src="{{ asset('admin-assets/js/tables-datatables-basic.js')}}"></script>
    <script src="{{ asset('admin-assets/js/validation.js')}}"></script>
    <script src="{{ asset('admin-assets/js/forms-pickers.js')}}"></script>
    <script src="{{ asset('admin-assets/js/forms-extras.js')}}"></script>
    <script src="{{ asset('admin-assets/js/forms-selects.js')}}"></script>
    <script src="{{ asset('admin-assets/js/forms-tagify.js')}}"></script>
    <script src="{{ asset('admin-assets/js/charts-chartjs.js')}}"></script>
    <script src="{{ asset('admin-assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
    <script src="{{ asset('admin-assets/js/extended-ui-sweetalert2.js')}}"></script>
    <?php if (Request::segment(2) != 'add-blog' || Request::segment(1) != 'admin-login') {
        $session_id = md5(uniqid(rand(), true));
        Session::put('session_id', $session_id);
    } ?>

    @if(Session::has('error'))
    <script>
        toastr['error']('', "{{ session('error') }}");
    </script>
    @endif
    @if(Session::has('info'))
    <script>
        toastr['info']('', "{{ session('info') }}");
    </script>
    @endif
    @if(Session::has('warning'))
    <script>
        toastr['warning']('', "{{ session('warning') }}");
    </script>
    @endif
    @if(Session::has('success'))
    <script>
        toastr['success']('', "{{ session('success') }}");
    </script>
    @endif
    <script>
        $(".flatpickr-datetime").flatpickr({
            enableTime: true,
            dateFormat: 'Y-m-d h:i K',
            timezone: "<?php echo setting('timezone'); ?>",
        });
        $(".flatpickr-date").flatpickr({
            enableTime: false,
            dateFormat: 'Y-m-d',
            timezone: "<?php echo setting('timezone'); ?>",
        });
        $(".flatpickr-range").flatpickr({
            mode: 'range',
            enableTime: true,
        });
        $(document).ready(function() {

            $('.category_id').select2({
                placeholder: "{{__('lang.admin_select_category')}}"
            });
            $('.sub_category_id').select2({
                placeholder: "{{__('lang.admin_select_subcategory')}}"
            });
            $('.email').select2({
                placeholder: "{{__('lang.admin_select_email')}}"
            });
            $("#ad_table").sortable({
                items: "tr",
                cursor: 'move',
                opacity: 0.6,
                update: function() {
                    sendOrderofAdsToServer();
                }
            });

            function sendOrderofAdsToServer(ad_id) {
                var order = [];
                var token = $('meta[name="csrf-token"]').attr('content');
                $('tr.row1').each(function(index, element) {
                    order.push({
                        id: $(this).attr('data-id'),
                        position: index + 1
                    });
                });
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: base_url + "/admin/ads-sortable",
                    data: {
                        order: order,
                        _token: token
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            console.log(response);
                        } else {
                            console.log(response);
                        }
                    }
                });
            }
        });
    </script>

    @if(Request::segment(2)=='dashboard')

    <?php
    $graphData = \Helpers::getUserOnTheBasisOfDate();
    $loginFormDoughnut = \Helpers::getSignupOnTheBasisOfLoginForm();
    $deviceTypeDoughnut = \Helpers::getSignupOnTheBasisOfDeviceType();
    $postViewAnalyticsData = \Helpers::getPostViewOnTheBasisOfDate();
    ?>

    <script>
        $(function() {
            const purpleColor = '##c6f21a',
                yellowColor = '#ffe800',
                cyanColor = '#7367f0',
                orangeColor = '#FF8132',
                orangeLightColor = '#FDAC34',
                oceanBlueColor = '#299AFF',
                greyColor = '#4F5D70',
                greyLightColor = '#EDF1F4',
                blueColor = '#2B9AFF',
                blueLightColor = '#84D0FF',
                greenLightColor = '#28dac6';

            let cardColor, headingColor, labelColor, borderColor, legendColor;

            if (isDarkStyle) {
                cardColor = config.colors_dark.cardColor;
                headingColor = config.colors_dark.headingColor;
                labelColor = config.colors_dark.textMuted;
                legendColor = config.colors_dark.bodyColor;
                borderColor = config.colors_dark.borderColor;
            } else {
                cardColor = config.colors.cardColor;
                headingColor = config.colors.headingColor;
                labelColor = config.colors.textMuted;
                legendColor = config.colors.bodyColor;
                borderColor = config.colors.borderColor;
            }
            $(".flatpickr-range").flatpickr({
                mode: 'range',
                enableTime: true,
                defaultDate: [<?php echo json_encode($graphData['chart_start_date']); ?>, <?php echo json_encode($graphData['chart_end_date']); ?>],
            });

            // user signup chart
            const barChart = document.getElementById('barChart1');
            if (barChart) {
                const graphData = <?php echo json_encode($graphData); ?>;
                const dates = graphData['dates'];
                const users = graphData['users'];

                // Extract only days for labels
                const dayLabels = dates.map(date => new Date(date).getDate());
                const monthYearLabel = new Date(dates[0]).toLocaleString('default', {
                    month: 'long',
                    year: 'numeric'
                });

                // Display Month and Year
                document.getElementById('monthYearLabel').innerHTML = monthYearLabel;

                const barChartVar = new Chart(barChart, {
                    type: 'bar',
                    data: {
                        labels: dayLabels, // Only show days in the label
                        datasets: [{
                            data: users,
                            backgroundColor: cyanColor,
                            borderColor: 'transparent',
                            maxBarThickness: 15,
                            borderRadius: {
                                topRight: 15,
                                topLeft: 15
                            }
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 500
                        },
                        plugins: {
                            tooltip: {
                                rtl: isRtl,
                                backgroundColor: cardColor,
                                titleColor: headingColor,
                                bodyColor: legendColor,
                                borderWidth: 1,
                                borderColor: borderColor,
                                callbacks: {
                                    title: function(tooltipItems) {
                                        const index = tooltipItems[0].dataIndex;
                                        return `Date: ${new Date(dates[index]).toISOString().split('T')[0]}`;
                                    },
                                    label: function(tooltipItem) {
                                        return `Users: ${tooltipItem.raw}`;
                                    }
                                }
                            },
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Date', // 📌 Label added for X-axis
                                    color: headingColor,
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    }
                                },
                                grid: {
                                    color: borderColor,
                                    drawBorder: false,
                                    borderColor: borderColor
                                },
                                ticks: {
                                    color: labelColor
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: "{{__('lang.admin_users_count')}}", // 📌 Label added for Y-axis
                                    color: headingColor,
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    }
                                },
                                min: 0,
                                max: Math.max(5, ...users), // Dynamic max value
                                grid: {
                                    color: borderColor,
                                    drawBorder: false,
                                    borderColor: borderColor
                                },
                                ticks: {
                                    stepSize: 1,
                                    color: labelColor
                                }
                            }
                        }
                    }
                });
            }

            //user login form
            const loginFormData = <?php echo json_encode($loginFormDoughnut); ?>;
            const doughnutChart = document.getElementById('doughnutChart');
            const doughnutContainer = doughnutChart.parentElement; // Get the parent container
            
            if (doughnutChart) {
                if (loginFormData.users && loginFormData.users.some(value => value > 0)) {
                    // Render the Doughnut Chart
                    new Chart(doughnutChart, {
                        type: 'doughnut',
                        data: {
                            labels: loginFormData.types,
                            datasets: [{
                                data: loginFormData.users,
                                backgroundColor: ['#3b5998', '#EA4335', '#fdac34', '#dcd9e7'],
                                borderWidth: 0,
                                pointStyle: 'rectRounded'
                            }]
                        },
                        options: {
                            responsive: true,
                            animation: { duration: 500 },
                            cutout: '68%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const index = context.dataIndex;
                                            const label = loginFormData.types[index] || '';
                                            const percentage = loginFormData.users[index] || 0;
                                            const totalUsers = loginFormData.totalCounts[index] || 0;
            
                                            return ` ${label}: ${percentage}% (${totalUsers} users)`;
                                        }
                                    },
                                    rtl: isRtl,
                                    backgroundColor: '#2f3349',
                                    titleColor: '#cfd3ec',
                                    bodyColor: '#b6bee3',
                                    borderWidth: 1,
                                    borderColor: '#434968'
                                }
                            }
                        }
                    });
                } else {
                    // Show a message when no data is available
                    doughnutContainer.innerHTML = `<div style="text-align:center; padding:20px; font-size:16px; color:#666;">No data available for login types.</div>`;
                }
            }


            // user device type
            const deviceData = <?php echo json_encode($deviceTypeDoughnut); ?>;
            const doughnutChartType = document.getElementById('doughnutChartType');
            const doughnutTypeContainer = doughnutChartType.parentElement; // Get the parent container
            
            if (doughnutChartType) {
                if (deviceData.users && deviceData.users.some(value => value > 0)) {
                    // Render the Doughnut Chart
                    new Chart(doughnutChartType, {
                        type: 'doughnut',
                        data: {
                            labels: deviceData.types,
                            datasets: [{
                                data: deviceData.users,
                                backgroundColor: [cyanColor, greenLightColor, config.colors.primary],
                                borderWidth: 0,
                                pointStyle: 'rectRounded'
                            }]
                        },
                        options: {
                            responsive: true,
                            animation: { duration: 500 },
                            cutout: '68%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const index = context.dataIndex;
                                            const label = deviceData.types[index] || '';
                                            const percentage = deviceData.users[index] || 0;
                                            const totalUsers = deviceData.totalCounts[index] || 0;
            
                                            return ` ${label}: ${percentage}% (${totalUsers} users)`;
                                        }
                                    },
                                    rtl: isRtl,
                                    backgroundColor: cardColor,
                                    titleColor: headingColor,
                                    bodyColor: legendColor,
                                    borderWidth: 1,
                                    borderColor: borderColor
                                }
                            }
                        }
                    });
                } else {
                    // Show a message when no data is available
                    doughnutTypeContainer.innerHTML = `<div style="text-align:center; padding:20px; font-size:16px; color:#666;">No data available for device types.</div>`;
                }
            }


            // user all post view
            const postViewChart = document.getElementById('postViewChart');
            if (postViewChart) {
                const graphData = <?php echo json_encode($postViewAnalyticsData); ?>;
                const dates = graphData['dates'];
                const views = graphData['views'];

                // Extract only days for labels
                const dayLabels = dates.map(date => new Date(date).getDate());
                const monthYearLabel = new Date(dates[0]).toLocaleString('default', {
                    month: 'long',
                    year: 'numeric'
                });

                // Display Month and Year
                document.getElementById('viewMonthYearLabel').innerHTML = monthYearLabel;

                const postViewChartVar = new Chart(postViewChart, {
                    type: 'line',
                    data: {
                        labels: dayLabels,
                        datasets: [{
                            data: views,
                            label: "{{__('lang.admin_views')}}",
                            borderColor: config.colors.danger,
                            tension: 0.5,
                            pointStyle: 'circle',
                            backgroundColor: config.colors.danger,
                            fill: false,
                            pointRadius: 1,
                            pointHoverRadius: 5,
                            pointHoverBorderWidth: 5,
                            pointBorderColor: 'transparent',
                            pointHoverBorderColor: cardColor,
                            pointHoverBackgroundColor: config.colors.danger
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'nearest',
                            intersect: false
                        },
                        plugins: {
                            tooltip: {
                                enabled: true,
                                position: 'nearest',
                                rtl: isRtl,
                                backgroundColor: cardColor,
                                titleColor: headingColor,
                                bodyColor: legendColor,
                                borderWidth: 1,
                                borderColor: borderColor,
                                callbacks: {
                                    title: function(tooltipItems) {
                                        const index = tooltipItems[0].dataIndex;
                                        return `Date: ${new Date(dates[index]).toISOString().split('T')[0]}`;
                                    },
                                    label: function(tooltipItem) {
                                        return `Views: ${tooltipItem.raw}`;
                                    }
                                }
                            },
                            legend: {
                                position: 'top',
                                align: 'start',
                                rtl: isRtl,
                                labels: {
                                    usePointStyle: true,
                                    padding: 35,
                                    boxWidth: 6,
                                    boxHeight: 6,
                                    color: legendColor
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Date', // 📌 Label added for X-axis
                                    color: headingColor,
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    }
                                },
                                grid: {
                                    color: borderColor,
                                    drawBorder: false,
                                    borderColor: borderColor
                                },
                                ticks: {
                                    color: labelColor
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: "{{__('lang.admin_view_counts')}}",
                                    color: headingColor,
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    }
                                },
                                min: 0,
                                max: Math.max(5, ...views), // Dynamic max value
                                grid: {
                                    color: borderColor,
                                    drawBorder: false,
                                    borderColor: borderColor
                                },
                                ticks: {
                                    stepSize: 1,
                                    color: labelColor
                                }
                            }
                        }
                    }
                });
            }

        });
    </script>
    @endif


    <!-- Post analytics Charts -->
    @if(Request::segment(2)=='analytics')
    <?php
    $postAnalyticsData = \Helpers::getPostAnalyticsOnTheBasisOfDate();
    $postPollDoughnut = \Helpers::getPostPollAnalyticData();
    ?>
    <script>
        var admin_no_data_available_for_post_poll ="{{__('lang.admin_no_data_available_for_post_poll')}}";
        $(function() {
            const colors = [
                '#c6f21a', '#ffe800', '#7367f0', '#FF8132', '#FDAC34', 
                '#299AFF', '#4F5D70', '#EDF1F4', '#2B9AFF', '#84D0FF', '#28dac6'
            ];

            let cardColor, headingColor, labelColor, borderColor, legendColor;

            if (isDarkStyle) {
                cardColor = config.colors_dark.cardColor;
                headingColor = config.colors_dark.headingColor;
                labelColor = config.colors_dark.textMuted;
                legendColor = config.colors_dark.bodyColor;
                borderColor = config.colors_dark.borderColor;
            } else {
                cardColor = config.colors.cardColor;
                headingColor = config.colors.headingColor;
                labelColor = config.colors.textMuted;
                legendColor = config.colors.bodyColor;
                borderColor = config.colors.borderColor;
            }


            // post analytics
            const postAnalyticData = <?php echo json_encode($postAnalyticsData); ?>;
            console.log(postAnalyticData);
            const dates = postAnalyticData['dates'];
            const analyticType = postAnalyticData['analyticType'];
            const analyticTypeParam = postAnalyticData['analyticTypeParam'];
            const totalCount = postAnalyticData['totalCount'];
            const sidebarCounts = postAnalyticData['sidebarCounts']; // Get dynamic Y-axis labels
            
            document.getElementById('totalCountOfAnalytic').innerHTML = "{{__('lang.admin_total_counts')}} " + totalCount;
            
            const dayLabels = dates.map(date => new Date(date).getDate());
            
            const lineChartEl = document.querySelector('#postAnalyticsViewChart'),
                lineChartConfig = {
                    chart: {
                        height: 400,
                        type: 'line',
                        parentHeightOffset: 0,
                        zoom: { enabled: false },
                        toolbar: { show: false }
                    },
                    series: [{
                        name: analyticTypeParam,
                        data: analyticType
                    }],
                    markers: {
                        strokeWidth: 7,
                        strokeOpacity: 1,
                        strokeColors: [cardColor],
                        colors: [config.colors.primary]
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'straight' },
                    colors: [config.colors.primary],
                    grid: {
                        borderColor: borderColor,
                        xaxis: { lines: { show: true } },
                        padding: { top: -20 }
                    },
                    tooltip: {
                        custom: function({ series, seriesIndex, dataPointIndex }) {
                            const count = series[seriesIndex][dataPointIndex];
                            const date = dates[dataPointIndex];
                            return `<div class="px-3 py-2">
                                      <strong>Date:</strong> ${date} <br>
                                      <strong>Type:</strong> ${analyticTypeParam} <br>
                                      <strong>Count:</strong> ${count}
                                    </div>`;
                        }
                    },
                    xaxis: {
                        categories: dayLabels,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            },
                            formatter: function(value) {
                                return sidebarCounts.includes(value) ? value : ''; // Show only relevant values
                            }
                        },
                        min: 0,
                        max: Math.max(...sidebarCounts), // Ensure maximum Y-axis is aligned
                        tickAmount: sidebarCounts.length - 1 // Number of ticks
                    }
                };
            
            if (typeof lineChartEl !== undefined && lineChartEl !== null) {
                const lineChart = new ApexCharts(lineChartEl, lineChartConfig);
                lineChart.render();
            }


            // post poll
            const postData = <?php echo json_encode($postPollDoughnut); ?>;
            const doughnutChartPostPoll = document.getElementById('doughnutChartPostPoll');
            const postPollContainer = doughnutChartPostPoll.parentElement; // Get the parent container
            
            if (doughnutChartPostPoll) {
                if (postData.users && postData.users.some(value => value > 0)) {
                    // Assign a color to each option dynamically
                    const backgroundColors = postData.types.map((_, index) => colors[index % colors.length]);
            
                    // Render the Doughnut Chart
                    new Chart(doughnutChartPostPoll, {
                        type: 'doughnut',
                        data: {
                            labels: postData.types,
                            datasets: [{
                                data: postData.users,
                                backgroundColor: backgroundColors,  // Dynamically assigned colors
                                borderWidth: 0,
                                pointStyle: 'rectRounded'
                            }]
                        },
                        options: {
                            responsive: true,
                            animation: { duration: 500 },
                            cutout: '68%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const index = context.dataIndex;
                                            const label = postData.types[index] || '';
                                            const percentage = postData.users[index] || 0;
                                            const totalUsers = postData.totalCounts[index] || 0;
            
                                            return ` ${label}: ${percentage}% (${totalUsers} users)`;
                                        }
                                    },
                                    rtl: isRtl,
                                    backgroundColor: cardColor,
                                    titleColor: headingColor,
                                    bodyColor: legendColor,
                                    borderWidth: 1,
                                    borderColor: borderColor
                                }
                            }
                        }
                    });
                } else {
                    // Show a message when no data is available
                    postPollContainer.innerHTML = `<div style="text-align:center; padding:20px; font-size:16px; color:#666;">${admin_no_data_available_for_post_poll}</div>`;
                }
            }

        });
    </script>
    @endif

    <script>
        $(document).ready(function() {
            $('.form-control').filter('select').addClass('form-select');
        })
    </script>

    <script>
        $(document).ready(function() {
            // Check/uncheck all checkboxes when parent checkbox is clicked
            $('#selectAll').click(function() {
                $('.selectCheckbox').prop('checked', $(this).prop('checked'));
            });

            // Delete selected checkboxes
            $('#deleteSelected').click(function() {
                var ids = [];
                $('.selectCheckbox:checked').each(function() {
                    ids.push($(this).val());
                });

                if (ids.length > 0) {
                    // Use SweetAlert for confirmation
                   Swal.fire({
                        title: adminTranslation.admin_are_you_sure,
                        text: adminTranslation.admin_delete_warning,
                        icon: adminTranslation.admin_warning,
                        showCancelButton: true,
                        confirmButtonText: adminTranslation.admin_delete_warning_yes_button,
                        cancelButtonText: adminTranslation.admin_delete_warning_no_button,
                        customClass: {
                          confirmButton: 'btn btn-primary me-3',
                          cancelButton: 'btn btn-label-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#selectedIds').val(ids);
                            // Submit the form to delete selected items
                            $('#deleteForm').submit();
                        }
                    });
                } else {
                    toastr['warning']('', "Please select at least one item to delete.");
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.dropdown-notifications-all').on('click', function () {
                $.ajax({
                    url: "{{ url('admin/notifications/mark-all-read') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.status) {
                            // Optional: Refresh part of the notification dropdown
                            location.reload(); // or use AJAX to reload just the list
                        }
                    },
                    error: function () {
                        alert('Failed to mark notifications as read.');
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.dropdown-notifications-archive').on('click', function() {
                let notificationId = $(this).data('id');

                $.ajax({
                    url: "{{ url('admin/notifications/remove') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: notificationId
                    },
                    success: function(response) {
                        if (response.status) {
                            // Remove the notification from UI
                            $(`[data-id='${notificationId}']`).closest('li').remove();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Failed to remove notification.');
                    }
                });
            });
        });
    </script>
</body>
</html>