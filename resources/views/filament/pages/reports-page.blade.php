<x-filament::page>
    <h2 class="text-2xl font-bold mb-4">تقارير حصص التسميع</h2>

    <table class="w-full border-collapse border border-gray-200">
        <thead>
            <tr class="bg-gray-100">
                <th class="border border-gray-300 px-4 py-2">التاريخ</th>
                <th class="border border-gray-300 px-4 py-2">الطالب</th>
                <th class="border border-gray-300 px-4 py-2">المعلم</th>
                <th class="border border-gray-300 px-4 py-2">الحلقة</th>
                <th class="border border-gray-300 px-4 py-2">النتيجة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($this->getSessions() as $session)
                <tr>
                    <td class="border border-gray-300 px-4 py-2">{{ $session->session_date }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $session->student->full_name }}</td>
                    {{-- <td class="border border-gray-300 px-4 py-2">{{ $session->teacher->name }}</td> --}}
                    <td class="border border-gray-300 px-4 py-2">{{ $session->halaka->name }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $session->memory_score }}/10</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        <a href="{{ route('reports.download-pdf') }}" class="px-4 py-2 bg-blue-500 text-white rounded">تحميل PDF</a>
        <a href="{{ route('reports.download-excel') }}" class="px-4 py-2 bg-green-500 text-white rounded">تحميل Excel</a>
    </div>
</x-filament::page>

