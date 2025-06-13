<x-filament-widgets::widget>
    <x-filament::section>
        <div>
            <div id="canvas-holder" style="width:100%">
                <canvas id="{{ $chartId }}" height="90"></canvas>
            </div>
        </div>

        <script src="https://unpkg.com/chart.js@2.8.0/dist/Chart.bundle.js"></script>
        <script src="https://unpkg.com/chartjs-gauge@0.3.0/dist/chartjs-gauge.js"></script>

        <script>
            (function () {
                const chartTitle = @json($title);
                const chartValue = @json($value);
                const chartId = @json($chartId);

                const config = {
                    type: 'gauge',
                    data: {
                        datasets: [{
                            data: [25, 50, 75, 100],
                            value: chartValue ?? 0,
                            backgroundColor: ['red', 'orange', 'yellow', 'green'],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: chartTitle ?? 'Chart',
                            fontSize: 24,
                            fontStyle: 'bold'
                        },
                        layout: {
                            padding: { bottom: 30 }
                        },
                        needle: {
                            radiusPercentage: 2,
                            widthPercentage: 3.2,
                            lengthPercentage: 80,
                            color: 'rgba(0, 0, 0, 1)'
                        },
                        valueLabel: {
                            formatter: Math.round
                        },
                        minValue: 0,
                        maxValue: 100
                    }
                };

                document.addEventListener('DOMContentLoaded', () => {
                    const canvas = document.getElementById(chartId);
                    if (!canvas) return console.warn('Canvas not found: ' + chartId);
                    const ctx = canvas.getContext('2d');
                    new Chart(ctx, config);
                });
            })();
        </script>
    </x-filament::section>
</x-filament-widgets::widget>
