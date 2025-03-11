<div class="mt-6 bg-white p-4 rounded-lg shadow-md">
    <h2 class="text-lg font-semibold mb-3">السجل التاريخي للحلقات</h2>
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border">اسم الحلقة</th>
                <th class="p-2 border">تاريخ البدء</th>
                <th class="p-2 border">تاريخ الانتهاء</th>
            </tr>
        </thead>
        <tbody>
            @foreach($history as $session)
            <tr class="border">
                <td class="p-2 border">{{ $session->halaka->name }}</td>
                <td class="p-2 border">{{ $session->halaka->start_date }}</td>
                <td class="p-2 border">{{ $session->halaka->max_students ?? 'لم ينته بعد' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

