<div class="mt-6 bg-white p-4 rounded-lg shadow-md">
    <h2 class="text-lg font-semibold mb-3">نسبة التقدم</h2>
    <canvas id="progressChart"></canvas>
</div>

<script>
    const ctx = document.getElementById('progressChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['جلسة 1', 'جلسة 2', 'جلسة 3'],
            datasets: [{
                label: 'تقدم الحفظ',
                data: [20, 50, 80],
                borderColor: 'blue',
                fill: false
            }]
        }
    });
</script>

