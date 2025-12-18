/**
 * Dashboard Charts Initialization
 * This file handles all ApexCharts on the admin dashboard
 */

export function initializeDashboardCharts(orderStatusData, salesPerformance, branches) {
    console.log('🚀 Initializing dashboard charts...');
    console.log('📊 Chart data:', { orderStatusData, salesPerformance, branches });
    
    if (typeof window.ApexCharts === 'undefined') {
        console.error('❌ ApexCharts is not loaded!');
        return;
    }

    // ============================================================================
    // ORDERS BY STATUS - DONUT CHART
    // ============================================================================
    const statusChartElement = document.querySelector("#ordersStatusChart");
    if (statusChartElement) {
        console.log('📈 Rendering status chart...');
        statusChartElement.innerHTML = '';
        
        const statusChartOptions = {
            series: [
                orderStatusData.pending || 0,
                orderStatusData.confirmed || 0,
                orderStatusData['picked up'] || 0,
                orderStatusData.cancelled || 0
            ],
            chart: {
                type: 'donut',
                height: 350,
                fontFamily: 'inherit',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                }
            },
            labels: ['Pending', 'Confirmed', 'Picked Up', 'Cancelled'],
            colors: ['#FD7E14', '#0dcaf0', '#198754', '#dc3545'],
            legend: {
                position: 'bottom',
                fontSize: '14px',
                markers: {
                    width: 12,
                    height: 12,
                    radius: 3,
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 5
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: {
                                fontSize: '16px',
                                fontWeight: 600,
                            },
                            value: {
                                fontSize: '28px',
                                fontWeight: 700,
                                color: '#A52A2A',
                                formatter: function(val) {
                                    return val;
                                }
                            },
                            total: {
                                show: true,
                                label: 'Total Orders',
                                fontSize: '14px',
                                fontWeight: 600,
                                color: '#6c757d',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    return opts.w.config.series[opts.seriesIndex];
                },
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                },
                dropShadow: {
                    enabled: false
                }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function(val) {
                        return val + ' orders';
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        const statusChart = new window.ApexCharts(statusChartElement, statusChartOptions);
        statusChart.render();
        console.log('✅ Status chart rendered');
    }

    // ============================================================================
    // SALES PERFORMANCE - LINE CHART
    // ============================================================================
    const salesChartElement = document.querySelector("#salesPerformanceChart");
    if (salesChartElement && salesPerformance && salesPerformance.length > 0) {
        console.log('📈 Rendering sales chart...');
        salesChartElement.innerHTML = '';
        
        const months = salesPerformance.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        });
        
        const salesData = salesPerformance.map(item => parseFloat(item.total_sales || 0));
        const orderCounts = salesPerformance.map(item => parseInt(item.order_count || 0));

        const salesChartOptions = {
            series: [
                {
                    name: 'Revenue',
                    type: 'area',
                    data: salesData
                },
                {
                    name: 'Orders',
                    type: 'line',
                    data: orderCounts
                }
            ],
            chart: {
                height: 400,
                type: 'line',
                fontFamily: 'inherit',
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: false,
                        reset: true
                    }
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                }
            },
            colors: ['#A52A2A', '#0d6efd'],
            stroke: {
                curve: 'smooth',
                width: [3, 3]
            },
            fill: {
                type: ['gradient', 'solid'],
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.3,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                }
            },
            labels: months,
            markers: {
                size: [5, 4],
                strokeWidth: 0,
                hover: {
                    size: 7
                }
            },
            xaxis: {
                title: {
                    text: 'Month',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600,
                    }
                },
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: [
                {
                    title: {
                        text: 'Revenue (₱)',
                        style: {
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#A52A2A'
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return '₱' + val.toLocaleString('en-PH', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        },
                        style: {
                            colors: '#A52A2A'
                        }
                    }
                },
                {
                    opposite: true,
                    title: {
                        text: 'Orders',
                        style: {
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#0d6efd'
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return Math.round(val);
                        },
                        style: {
                            colors: '#0d6efd'
                        }
                    }
                }
            ],
            tooltip: {
                shared: true,
                intersect: false,
                theme: 'light',
                y: [
                    {
                        formatter: function(val) {
                            return '₱' + val.toLocaleString('en-PH', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        formatter: function(val) {
                            return val + ' orders';
                        }
                    }
                ]
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontSize: '14px',
                markers: {
                    width: 12,
                    height: 12,
                    radius: 3,
                }
            },
            grid: {
                borderColor: '#e9ecef',
                strokeDashArray: 3,
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: {
                        height: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        const salesChart = new window.ApexCharts(salesChartElement, salesChartOptions);
        salesChart.render();
        console.log('✅ Sales chart rendered');

        // Branch filter functionality
        const branchFilter = document.getElementById('branchFilter');
        if (branchFilter) {
            branchFilter.addEventListener('change', function() {
                const branchId = this.value;
                
                if (branchId === 'all') {
                    salesChart.updateSeries([
                        {
                            name: 'Revenue',
                            data: salesData
                        },
                        {
                            name: 'Orders',
                            data: orderCounts
                        }
                    ]);
                } else {
                    fetch(window.location.pathname + '?branch_id=' + branchId + '&ajax=1', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const branchSalesData = data.salesPerformance.map(item => parseFloat(item.total_sales || 0));
                        const branchOrderCounts = data.salesPerformance.map(item => parseInt(item.order_count || 0));
                        
                        salesChart.updateSeries([
                            {
                                name: 'Revenue',
                                data: branchSalesData
                            },
                            {
                                name: 'Orders',
                                data: branchOrderCounts
                            }
                        ]);
                    })
                    .catch(error => console.error('❌ Branch filter error:', error));
                }
            });
        }
    }

    console.log('✅ Dashboard charts initialized!');
}

// ============================================================================
// AUTO-REFRESH LIVE ORDER COUNT
// ============================================================================
export function startLiveRefresh() {
    setInterval(function() {
        fetch(window.location.pathname + '?live_count=1', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const countElement = document.querySelector('.count-value');
            if (countElement) {
                const currentCount = parseInt(countElement.textContent);
                const newCount = data.recentOrderCount;
                
                if (currentCount !== newCount) {
                    countElement.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        countElement.textContent = newCount;
                        countElement.style.transform = 'scale(1)';
                    }, 150);
                }
            }
            
            const trendElement = document.querySelector('.trend-indicator');
            if (trendElement && data.orderTrend !== undefined) {
                const trendValue = data.orderTrend;
                trendElement.className = `trend-indicator ${trendValue >= 0 ? 'trend-up' : 'trend-down'}`;
                trendElement.innerHTML = `
                    <i class="fas fa-${trendValue >= 0 ? 'arrow-up' : 'arrow-down'}"></i>
                    ${Math.abs(trendValue).toFixed(1)}%
                    <span class="trend-text">vs previous 24h</span>
                `;
            }
        })
        .catch(error => console.error('❌ Live refresh error:', error));
    }, 30000);
}