<div class="mt-6 bg-white p-4 rounded-lg shadow-md">
    <h2 class="text-lg font-semibold mb-3">الحلقات النشطة</h2>
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border">اسم الحلقة</th>
                <th class="p-2 border">تاريخ البدء</th>
                <th class="p-2 border">الحالة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $session)
            <tr class="border">
                <td class="p-2 border">{{ $session->name }}</td>
                <td class="p-2 border">{{ $session->start_date }}</td>
                <td class="p-2 border text-green-600">مستمرة</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

