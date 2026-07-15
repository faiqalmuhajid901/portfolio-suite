import Chart from 'chart.js/auto';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('portfolioAnalyticsCharts', (initialPayload) => ({
        payload: initialPayload ?? {},
        trendChart: null,
        deviceChart: null,
        sourceChart: null,

        init() {
            this.$nextTick(() => this.renderCharts());
        },

        updateCharts(payload) {
            this.payload = payload ?? {};
            this.$nextTick(() => this.renderCharts());
        },

        renderCharts() {
            this.destroyCharts();
            this.renderTrendChart();
            this.renderDeviceChart();
            this.renderSourceChart();
        },

        renderTrendChart() {
            if (!this.$refs.trendChart) {
                return;
            }

            const trend = this.payload.trend ?? {};

            this.trendChart = new Chart(this.$refs.trendChart, {
                type: 'line',
                data: {
                    labels: trend.labels ?? [],
                    datasets: [
                        {
                            label: 'Page views',
                            data: trend.views ?? [],
                            borderColor: '#2f6f61',
                            backgroundColor: 'rgba(47, 111, 97, 0.12)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3,
                        },
                        {
                            label: 'Unique visitors',
                            data: trend.visitors ?? [],
                            borderColor: '#7fac9f',
                            backgroundColor: 'rgba(127, 172, 159, 0.08)',
                            fill: false,
                            tension: 0.35,
                            pointRadius: 3,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                    },
                },
            });
        },

        renderDeviceChart() {
            if (!this.$refs.deviceChart) {
                return;
            }

            const devices = this.payload.devices ?? {};

            this.deviceChart = new Chart(this.$refs.deviceChart, {
                type: 'doughnut',
                data: {
                    labels: devices.labels ?? [],
                    datasets: [{
                        data: devices.values ?? [],
                        backgroundColor: ['#2f6f61', '#7fac9f', '#c5ddd5', '#dcebe6'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                    },
                },
            });
        },

        renderSourceChart() {
            if (!this.$refs.sourceChart) {
                return;
            }

            const sources = this.payload.sources ?? {};

            this.sourceChart = new Chart(this.$refs.sourceChart, {
                type: 'bar',
                data: {
                    labels: sources.labels ?? [],
                    datasets: [{
                        label: 'Visits',
                        data: sources.values ?? [],
                        backgroundColor: '#7fac9f',
                        borderRadius: 8,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                },
            });
        },

        destroyCharts() {
            this.trendChart?.destroy();
            this.deviceChart?.destroy();
            this.sourceChart?.destroy();

            this.trendChart = null;
            this.deviceChart = null;
            this.sourceChart = null;
        },
    }));
});
