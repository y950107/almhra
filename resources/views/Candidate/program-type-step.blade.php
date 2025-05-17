<!-- Step 2 - Program Type -->
<div class="step step-2">
    <fieldset class="border border-gray-300 p-4 rounded-md">
        <legend class="text-lg font-semibold text-green-600">اختر نوع البرنامج</legend>

        <div class="mb-4 w-full">
            <select id="program_type" name="program_type" class="select2 w-full" required>
                <option value="">اختر البرنامج</option>
                <option value="maqraa" {{ old('program_type') == 'maqraa' ? 'selected' : '' }}>برنامج المقرأة</option>
                <option value="mutqin" {{ old('program_type') == 'mutqin' ? 'selected' : '' }}>برنامج المتقن</option>
                <option value="mahir"  {{ old('program_type') == 'mahir' ? 'selected' : '' }}>برنامج الماهر</option>
            </select>
        </div>


        <!-- Description boxes -->
        <div id="program_description" class="mt-4 p-4 bg-gray-100 border rounded">
            <div id="desc_maqraa" >
                <h4 class="font-bold text-green-700">برنامج المقرأة</h4>
                <p> يهدف إلى قراءة الطالب على شيخه صفحة كاملة لغرض أخذ إجازة في القرآن الكريم فى أي رواية أو قراءة.</p>
            </div>

            <div id="desc_mutqin">
                <h4 class="font-bold text-green-700">برنامج المتقن</h4>
                <p>يهدف ان يحفظ الطالب المقرر اليومي من الحفظ و المراجعة.</p>
            </div>

            <div id="desc_mahir">
                <h4 class="font-bold text-green-700">برنامج الماهر</h4>
                <p>يهدف لتصحيح
                     قراءة الطالب فى القران الكريم
                    وهو ملزم في عدد أوجه اذا حفظ او تلاوة.</p>
            </div>
        </div>
    </fieldset>

    <div class="mt-6 flex justify-between">
        <button type="button" onclick="prevStep(1)"
                class="bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700 transition">
            → السابق
        </button>
        <button type="button" onclick="nextStep(3)"
                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">
            التالي ←
        </button>
    </div>
</div>
