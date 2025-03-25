@php
    $filePath = $getState() ? asset('storage/' . $getState()) : null;
@endphp

@if ($filePath)
    <a href="{{ $filePath }}" target="_blank" class="text-blue-600 underline">
        📄 تحميل الملف
    </a>
@else
    <span class="text-gray-500">لا يوجد ملف</span>
@endif