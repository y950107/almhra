<!-- Step 2 - Quranic Info -->
<div class="step step-3 hidden">
    <fieldset class="border border-gray-300 p-4 rounded-md">
        <legend class="text-lg font-semibold text-green-600">المعلومات القرآنية</legend>

        <div class="mb-4">
            <label for="quran_level" class="block text-sm font-medium text-gray-700">مستواك في الحفظ*</label>
            <select name="quran_level" id="quran_level" class="select2 w-full">
                <option value="">اختر المستوى</option>
                @foreach ($quranLevels as $key => $value)
                    <option value="{{ $key }}" {{ old('quran_level') == $key ? 'selected' : '' }}>
                        {{ $value }}</option>
                @endforeach
            </select>
        </div>

        <div id="maqraa_fields">
            <!-- هل لديه إجازة -->
            <div class="mb-4">
                <label for="has_ijaza" class="block text-sm font-medium text-gray-700">هل لديك إجازة؟*</label>
                <select name="has_ijaza" id="has_ijaza"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" onchange="toggleIjazaTypes()">
                    <option value="">اختر</option>
                    <option value="1" {{ old('has_ijaza') == '1' ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ old('has_ijaza') === '0' ? 'selected' : '' }}>لا</option>
                </select>
            </div>

            <!-- أنواع الإجازة -->
            <div class="mb-4" id="ijaza_types_container" style="display: none;">
                <label for="ijaza_types" class="block text-sm font-medium text-gray-700">أنواع الإجازة</label>
                <div class="mt-4 flex flex-col gap-2">
                    @foreach ($ijazas as $key => $label)
                        <label class="inline-flex items-center gap-x-1">
                            <input type="checkbox" name="ijaza_types[]" value="{{ $key }}"
                                   {{ in_array($key, old('ijaza_types', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:outline-none focus:ring-0 focus-visible:outline-none focus-visible:ring-0">
                            <span class="ml-2 text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>



            <!-- القراءات المراد قراءتها -->
            <div class="mb-4">
                <label for="desired_recitation" class="block text-sm font-medium text-gray-700">القراءات المراد قراءتها</label>


                <select name="desired_recitation" id="desired_recitation" class="select2 w-full">
                    <option value="">اختر القرائات</option>
                    @foreach ($recitations as $key => $value)
                        <option value="{{ $key }}" {{ old('desired_recitation') == $key ? 'selected' : '' }}>
                            {{ $value }}</option>
                    @endforeach
                </select>

            </div>
        </div>


        <!-- التقييم الذاتي -->
        <!-- التقييم الذاتي -->
        <div class="mb-4">
            <label for="self_evaluation" class="block text-sm font-medium text-gray-700">مستواك في التجويد*</label>
            <select name="self_evaluation" id="self_evaluation" class="select2 w-full">
                <option value="">اختر المستوى</option>
                <option value="60" {{ old('self_evaluation') == '60' ? 'selected' : '' }}>60%</option>
                <option value="70" {{ old('self_evaluation') == '70' ? 'selected' : '' }}>70%</option>
                <option value="80" {{ old('self_evaluation') == '80' ? 'selected' : '' }}>80%</option>
                <option value="90" {{ old('self_evaluation') == '90' ? 'selected' : '' }}>90%</option>
                <option value="100" {{ old('self_evaluation') == '100' ? 'selected' : '' }}>100%</option>
            </select>
        </div>


     {{--   <!-- المعلم المراد الدراسة عنده -->
        <div class="mb-4">
            <label for="teacher_id" class="block text-sm font-medium text-gray-700">المعلم المراد الدراسة
                عنده</label>
            <select name="teacher_id" id="teacher_id"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">اختر المعلم</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}"
                        {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->name }}
                    </option>
                @endforeach
            </select>
        </div>--}}

    </fieldset>

    <fieldset class="border border-purple-300 p-6 rounded-2xl shadow-sm bg-white mt-8">
        <legend class="text-xl font-bold text-purple-700 px-2">📁 قسم الملفات</legend>

        <!-- ملف المؤهل -->
        <div class="mb-5" >
            <label for="qualification_file" class="block text-sm font-medium text-gray-700 mb-1">
                🎓 ملف الاجازة / المؤهلات / الشهادات
            </label>
            <div class="relative">
                <input type="file" name="qualification_file" id="qualification_file" required class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="updateFileName('qualification_file', 'qualification_file_name')">
                <button  id="file_upload_button" type="button" onclick="document.getElementById('qualification_file').click()"
                        class="block w-full text-sm text-gray-700 py-2 px-4 rounded-md border-2 border-gray-300
                           bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-300">
                    اختر الملف
                </button>
                <span id="qualification_file_name" class="text-gray-500 text-sm mt-2"></span> <!-- To display selected file name -->
            </div>
        </div>

        <!-- المقطع الصوتي -->
        <div class="mb-3" id="maqraa_file">
            <label for="audio_recitation" class="block text-sm font-medium text-gray-700 mb-1">
                🔊 الرجاء إرفاق مقطع صوتي من حفظك للوجه رقم 107 من سورة المائدة.
                من {حرمت عليكم الميتة} إلى نهاية الوجه.
            </label>
            <div class="relative">
                <input type="file" name="audio_recitation" id="audio_recitation" class="hidden" accept=".mp3,.wav,.ogg" onchange="updateFileName('audio_recitation', 'audio_recitation_name')">
                <button  id="file_upload_button2" type="button" onclick="document.getElementById('audio_recitation').click()"
                        class="block w-full text-sm text-gray-700 py-2 px-4 rounded-md border-2 border-gray-300
                           bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-300">
                    اختر المقطع
                </button>
                <span id="audio_recitation_name" class="text-gray-500 text-sm mt-2"></span> <!-- To display selected file name -->
            </div>
        </div>

    </fieldset>


    <div class="mt-6 flex justify-between">
        <button type="button" onclick="prevStep(2)"
                class="bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700 transition">
            → السابق
        </button>
        <button type="submit"
                id="submit-form"
                class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 transition">
            تقديم الطلب
        </button>
    </div>
</div>


