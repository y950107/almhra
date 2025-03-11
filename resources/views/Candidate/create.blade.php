@extends('layouts.guest')

@section('title', 'تقديم الطلب للمترشحين')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col">
    <!-- رأس الصفحة (Header) -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900">تقديم الطلب للمترشحين</h1>
        </div>
    </header>

    <!-- المحتوى الرئيسي -->
    <main class="flex-grow max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-6">
            <!-- عرض الأخطاء (Validation Errors) -->
            @if ($errors->any())
                <div class="mb-4">
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('candidate.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

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

                <!-- المؤهل العلمي -->
                <div class="mb-4">
                    <label for="qualification" class="block text-sm font-medium text-gray-700">المؤهل العلمي</label>
                    <input type="text" name="qualification" id="qualification" value="{{ old('qualification') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <!-- مستوى القرآن -->
                <div class="mb-4">
                    <label for="quran_level" class="block text-sm font-medium text-gray-700">مستوى القرآن</label>
                    <input type="text" name="quran_level" id="quran_level" value="{{ old('quran_level') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <!-- هل لديه إجازة -->
                <div class="mb-4">
                    <label for="has_ijaza" class="block text-sm font-medium text-gray-700">هل لديك إجازة؟</label>
                    <select name="has_ijaza" id="has_ijaza" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">اختر</option>
                        <option value="1" {{ old('has_ijaza') == '1' ? 'selected' : '' }}>نعم</option>
                        <option value="0" {{ old('has_ijaza') === '0' ? 'selected' : '' }}>لا</option>
                    </select>
                </div>

                <!-- أنواع الإجازة -->
                <div class="mb-4">
                    <label for="ijaza_types" class="block text-sm font-medium text-gray-700">أنواع الإجازة</label>
                    <input type="text" name="ijaza_types" id="ijaza_types" value="{{ old('ijaza_types') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- القراءات المراد قراءتها -->
                <div class="mb-4">
                    <label for="desired_recitations" class="block text-sm font-medium text-gray-700">القراءات المراد قراءتها</label>
                    <input type="text" name="desired_recitations" id="desired_recitations" value="{{ old('desired_recitations') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- التقييم الذاتي -->
                <div class="mb-4">
                    <label for="self_evaluation" class="block text-sm font-medium text-gray-700">التقييم الذاتي</label>
                    <textarea name="self_evaluation" id="self_evaluation" rows="4"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('self_evaluation') }}</textarea>
                </div>

                <!-- المعلم المراد الدراسة عنده -->
                <div class="mb-4">
                    <label for="teacher_id" class="block text-sm font-medium text-gray-700">المعلم المراد الدراسة عنده</label>
                    <input type="text" name="teacher_id" id="teacher_id" value="{{ old('teacher_id') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <!-- ملف التقييم الشخصي (المؤهل) -->
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
            <p class="text-center">&copy; {{ date('Y') }} اسم مدرستك. جميع الحقوق محفوظة.</p>
        </div>
    </footer>
</div>
@endsection