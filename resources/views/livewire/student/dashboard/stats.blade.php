<div class="grid grid-cols-3 gap-4">
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-lg font-semibold">عدد الحلقات</h2>
        <p class="text-3xl font-bold">{{ $totalSessions }}</p>
    </div>
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-lg font-semibold">عدد الاوجه المحفوظة </h2>
        <p class="text-3xl font-bold">{{ $totalAyatMemorized }}</p>
    </div>
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-lg font-semibold">نسبة التطور</h2>
        <p class="text-3xl font-bold">{{ $progressRate }}%</p>
    </div>
</div>
