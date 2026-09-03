window.NotulenChart = (function () {

    let chart = null;

    /**
     * Ambil data notulen dari API
     */
    async function fetchMonthlyNotulen() {

        const response = await fetch('/api/monthly_notulen', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(
                `HTTP ${response.status}: Gagal mengambil data notulen`
            );
        }

        return await response.json();
    }


    /**
     * Render chart
     */
    function render(response, elementId) {

        const canvas = document.getElementById(elementId);

        if (!canvas) {
            console.warn(
                `Element #${elementId} tidak ditemukan`
            );

            return;
        }

        const labels = response.data.map(
            item => item.month_name
        );

        const currentYear = response.years.current;
        const previousYear = response.years.previous;

        const currentData = response.data.map(
            item => item[currentYear] ?? 0
        );

        const previousData = response.data.map(
            item => item[previousYear] ?? 0
        );


        // Destroy chart sebelumnya
        if (chart) {
            chart.destroy();
        }


        chart = new Chart(canvas, {

            type: 'line',

            data: {
                labels: labels,

                datasets: [
                    {
                        label: currentYear,

                        data: currentData,

                        tension: 0.4,

                        fill: false,

                        pointRadius: 4,

                        pointHoverRadius: 6
                    },

                    {
                        label: previousYear,

                        data: previousData,

                        tension: 0.4,

                        fill: false,

                        pointRadius: 4,

                        pointHoverRadius: 6
                    }
                ]
            },

            options: {
                responsive: true,

                maintainAspectRatio: false,

                interaction: {
                    intersect: false,
                    mode: 'index'
                },

                plugins: {

                    legend: {
                        position: 'top'
                    },

                    tooltip: {
                        callbacks: {
                            label: function (context) {

                                return `${context.dataset.label}: ${context.raw} notulen`;
                            }
                        }
                    }
                },

                scales: {

                    y: {
                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        },

                        title: {
                            display: true,
                            text: 'Jumlah Notulen'
                        }
                    },

                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                }
            }
        });
    }


    /**
     * Public initialization
     */
    async function init(elementId = 'notulenChart') {

        try {

            const response = await fetchMonthlyNotulen();

            render(response, elementId);

        } catch (error) {

            console.error(
                'Monthly Notulen Error:',
                error
            );
        }
    }


    // Public API
    return {
        init: init,
        refresh: init
    };

})();