

@extends('layouts.guest')

@section('title', 'تقديم الطلب للمترشحين')

@section('content')

    <div class="">
        <div class="">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-6">تقديم الطلب للمترشحين</h1>

            @if (session()->has('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-600 rounded">
                    {{session()->get('success')}}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-600 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('candidate.store') }}" method="POST" enctype="multipart/form-data"
                class=" bg-white p-8 rounded-lg shadow-md w-full max-w-2xl space-y-4 m-auto">
                @csrf

                <fieldset class="border border-gray-300 p-4 rounded-md">
                    <legend class="text-lg font-semibold text-blue-600">المعلومات الشخصية</legend>

                    <!-- الاسم الكامل -->
                    <div class="mb-4">
                        <label for="full_name" class="block text-sm font-medium text-gray-700">الاسم الكامل</label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <!-- رقم الهاتف -->
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700">رقم الهاتف</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <!-- البريد الإلكتروني -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <!-- تاريخ الميلاد -->
                    <div class="mb-4">
                        <label for="birthdate" class="block text-sm font-medium text-gray-700">تاريخ الميلاد</label>
                        <input type="date" name="birthdate" id="birthdate" value="{{ old('birthdate') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>


                    <div class="mb-4">
                        <label for="qualification" class="block text-sm font-medium text-gray-700">المؤهل العلمي</label>
                        <select name="qualification[]" id="qualification" multiple
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @foreach ($qualifications as $qualification)
                                <option value="{{ $qualification }}"
                                    {{ in_array($qualification, old('qualification', [])) ? 'selected' : '' }}>
                                    {{ $qualification }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                </fieldset>


                <fieldset class="border border-gray-300 p-4 rounded-md">
                    <legend class="text-lg font-semibold text-green-600">المعلومات القرآنية</legend>

                    <!-- مستوى القرآن -->
                    <div class="mb-4">
                        <label for="quran_level" class="block text-sm font-medium text-gray-700">مستوى القرآن</label>
                        <select name="quran_level" id="quran_level" class="select2 w-full">
                            <option value="">اختر المستوى</option>
                            @foreach ($quranLevels as $key => $value)
                                <option value="{{ $key }}" {{ old('quran_level') == $key ? 'selected' : '' }}>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- هل لديه إجازة -->
                    <div class="mb-4">
                        <label for="has_ijaza" class="block text-sm font-medium text-gray-700">هل لديك إجازة؟</label>
                        <select name="has_ijaza" id="has_ijaza"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">اختر</option>
                            <option value="1" {{ old('has_ijaza') == '1' ? 'selected' : '' }}>نعم</option>
                            <option value="0" {{ old('has_ijaza') === '0' ? 'selected' : '' }}>لا</option>
                        </select>
                    </div>

                    <!-- أنواع الإجازة -->
                    <div class="mb-4">
                        <label for="ijaza_types" class="block text-sm font-medium text-gray-700">أنواع الإجازة</label>
                        <select name="ijaza_types[]" id="ijaza_types"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" multiple>
                            @foreach ($ijazas as $key => $label)
                                <option value="{{ $key }}"
                                    {{ in_array($key, old('ijaza_types', [])) ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                   
                    
                    <!-- القراءات المراد قراءتها -->
                    <div class="mb-4">
                        <label for="desired_recitations" class="block text-sm font-medium text-gray-700">القراءات المراد قراءتها</label>
                        <select name="desired_recitations[]" id="desired_recitations" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            
                            @forelse($recitations as $recitation)
                                <option value="{{ $recitation['reading'] }}" {{ in_array($recitation['reading'], old('desired_recitations', [])) ? 'selected' : '' }}>
                                    {{ $recitation['reading'] }}
                                </option>
                            
                            @empty
                            <option value="" disabled>لايوجد أي قراءات</option>
                            @endforelse
                        </select>
                    </div>

                    <!-- التقييم الذاتي -->
                    <div class="mb-4">
                        <label for="self_evaluation" class="block text-sm font-medium text-gray-700">التقييم الذاتي</label>
                        <select name="self_evaluation" id="self_evaluation" class="select2 w-full">
                            <option value="">اختر التقييم</option>
                            <option value="60">60%</option>
                            <option value="70">70%</option>
                            <option value="80">80%</option>
                            <option value="90">90%</option>
                            <option value="100">100%</option>
                        </select>
                    </div>

                    <!-- المعلم المراد الدراسة عنده -->
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
                    </div>

                </fieldset>
                <!-- المؤهل العلمي -->


                <fieldset class="border border-gray-300 p-4 rounded-md">
                    <legend class="text-lg font-semibold text-purple-600">قسم الملفات</legend>
                    <div class="mb-4">
                        <label for="qualification_file" class="block text-sm font-medium text-gray-700">ملف المؤهل</label>
                        <input type="file" name="qualification_file" id="qualification_file"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <!-- المقطع الصوتي -->
                    <div class="mb-4">
                        <label for="audio_recitation" class="block text-sm font-medium text-gray-700">مقطع صوتي</label>
                        <input type="file" name="audio_recitation" id="audio_recitation"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                </fieldset>




                <!-- زر التقديم -->
                <div class="mt-6">
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">
                        تقديم الطلب
                    </button>
                </div>
            </form>

        </div>
        </main>

        <!-- تذييل الصفحة (Footer) -->
        <footer class="bg-gray-800 text-white py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="text-center">&copy; {{ date('Y') }} مقرأة المهرة
                    بجامع والدة األمير بندر بن عبدالعزيز-بحي الندى. جميع الحقوق محفوظة.</p>
            </div>
        </footer>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#desired_recitations').select2({
                    placeholder: "اختر القراءات",
                    allowClear: true
                });

                $('#qualification').select2({
                    placeholder: "اختر المؤهل العلمي",
                    allowClear: true
                });

                $('#teacher_id').select({
                    placeholder: "اختر المعلم",
                    allowClear: true
                });
                $('#self_evaluation').select({
                    placeholder: "اختر التقييم الذاتي",
                    allowClear: true
                });
            });
        </script>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.getElementById('candidateForm').addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'تم إرسال الطلب!',
                    text: 'تم تقديم طلبك بنجاح.',
                    icon: 'success',
                    confirmButtonText: 'حسنًا'
                }).then(() => {
                    this.submit();
                });
            });
        </script>
        @endpushipt>
    @endpush

@endsection
