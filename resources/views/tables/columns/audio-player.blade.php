@if ($getRecord()->audio_recitation)
    <audio controls style="width: 100px;">
        <source src="{{ asset('storage/' . $getRecord()->audio_recitation) }}" type="audio/mpeg">
        متصفحك لا يدعم تشغيل الصوت.
    </audio>
@else
    <span class="text-gray-500">لا يوجد تسجيل</span>
@endif