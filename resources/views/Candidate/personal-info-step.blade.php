<!-- Step 1 - Personal Info -->
<div class="step step-1">
    <fieldset class="border border-gray-300 p-4 rounded-md">
        <legend class="text-lg font-semibold text-blue-600">المعلومات الشخصية</legend>
        <!-- الاسم الكامل -->
        <div class="mb-4">
            <label for="full_name" class="block text-sm font-medium text-gray-700">الاسم الكامل*</label>
            <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <!-- تاريخ الميلاد -->
        <div class="mb-4">
            <label for="birthdate" class="block text-sm font-medium text-gray-700">تاريخ الميلاد*</label>
            <input type="date" name="birthdate" id="birthdate" value="{{ old('birthdate') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <!-- رقم الهاتف -->
        <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-gray-700">رقم الهاتف*</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <!-- البريد الإلكتروني -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">البريد الإلكتروني*</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>


        <!-- كلمة السر -->
        <div class="mb-4 relative">
            <label for="password" class="block text-sm font-medium text-gray-700">كلمة السر*</label>
            <div class="flex items-center relative">
                <!-- Eye icon for toggling visibility -->
                <span id="toggle-password" class="absolute left-3 cursor-pointer">
                 <!-- SVG Eye Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" id="eye-icon" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6c-5 0-9 3.5-9 7s4 7 9 7 9-3.5 9-7-4-7-9-7zM12 9c1.5 0 3 1 3 3s-1.5 3-3 3-3-1-3-3 1.5-3 3-3z" />
            </svg>
                    <svg id="eye-icon-crossed" class="h-5 w-5 text-gray-500 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"  fill-rule="evenodd" clip-rule="evenodd"><path d="M8.137 15.147c-.71-.857-1.146-1.947-1.146-3.147 0-2.76 2.241-5 5-5 1.201 0 2.291.435 3.148 1.145l1.897-1.897c-1.441-.738-3.122-1.248-5.035-1.248-6.115 0-10.025 5.355-10.842 6.584.529.834 2.379 3.527 5.113 5.428l1.865-1.865zm6.294-6.294c-.673-.53-1.515-.853-2.44-.853-2.207 0-4 1.792-4 4 0 .923.324 1.765.854 2.439l5.586-5.586zm7.56-6.146l-19.292 19.293-.708-.707 3.548-3.548c-2.298-1.612-4.234-3.885-5.548-6.169 2.418-4.103 6.943-7.576 12.01-7.576 2.065 0 4.021.566 5.782 1.501l3.501-3.501.707.707zm-2.465 3.879l-.734.734c2.236 1.619 3.628 3.604 4.061 4.274-.739 1.303-4.546 7.406-10.852 7.406-1.425 0-2.749-.368-3.951-.938l-.748.748c1.475.742 3.057 1.19 4.699 1.19 5.274 0 9.758-4.006 11.999-8.436-1.087-1.891-2.63-3.637-4.474-4.978zm-3.535 5.414c0-.554-.113-1.082-.317-1.562l.734-.734c.361.69.583 1.464.583 2.296 0 2.759-2.24 5-5 5-.832 0-1.604-.223-2.295-.583l.734-.735c.48.204 1.007.318 1.561.318 2.208 0 4-1.792 4-4z"/></svg>
        </span>
                <input type="password" name="password" id="password" value="{{ old('password') }}"
                       class="mt-1 block w-full pl-10 border-gray-300 rounded-md shadow-sm" required>
            </div>
        </div>

        <!-- تاكيد  كلمة السر -->
        <div class="mb-4 relative">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">تاكيد كلمة السر*</label>
            <div class="flex items-center relative">
                <!-- Eye icon for toggling visibility -->
                <span id="toggle-password-confirm" class="absolute left-3 cursor-pointer">
                 <!-- SVG Eye Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" id="eye-icon-confirm" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6c-5 0-9 3.5-9 7s4 7 9 7 9-3.5 9-7-4-7-9-7zM12 9c1.5 0 3 1 3 3s-1.5 3-3 3-3-1-3-3 1.5-3 3-3z" />
            </svg>
                    <svg id="eye-icon-crossed-confirm" class="h-5 w-5 text-gray-500 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"  fill-rule="evenodd" clip-rule="evenodd"><path d="M8.137 15.147c-.71-.857-1.146-1.947-1.146-3.147 0-2.76 2.241-5 5-5 1.201 0 2.291.435 3.148 1.145l1.897-1.897c-1.441-.738-3.122-1.248-5.035-1.248-6.115 0-10.025 5.355-10.842 6.584.529.834 2.379 3.527 5.113 5.428l1.865-1.865zm6.294-6.294c-.673-.53-1.515-.853-2.44-.853-2.207 0-4 1.792-4 4 0 .923.324 1.765.854 2.439l5.586-5.586zm7.56-6.146l-19.292 19.293-.708-.707 3.548-3.548c-2.298-1.612-4.234-3.885-5.548-6.169 2.418-4.103 6.943-7.576 12.01-7.576 2.065 0 4.021.566 5.782 1.501l3.501-3.501.707.707zm-2.465 3.879l-.734.734c2.236 1.619 3.628 3.604 4.061 4.274-.739 1.303-4.546 7.406-10.852 7.406-1.425 0-2.749-.368-3.951-.938l-.748.748c1.475.742 3.057 1.19 4.699 1.19 5.274 0 9.758-4.006 11.999-8.436-1.087-1.891-2.63-3.637-4.474-4.978zm-3.535 5.414c0-.554-.113-1.082-.317-1.562l.734-.734c.361.69.583 1.464.583 2.296 0 2.759-2.24 5-5 5-.832 0-1.604-.223-2.295-.583l.734-.735c.48.204 1.007.318 1.561.318 2.208 0 4-1.792 4-4z"/></svg>

        </span>
                <input type="password" name="password_confirmation" id="password_confirmation" value="{{ old('password_confirmation') }}"
                       class="mt-1 block w-full pl-10 border-gray-300 rounded-md shadow-sm" required>
            </div>
        </div>


        <div class="mb-4">
            <div class="mb-4">
                <label for="qualification" class="block text-sm font-medium text-gray-700">المؤهل العلمي*</label>
                <select name="qualification" id="qualification" class="select2 w-full" required>
                    <option value="">اختر المؤهل العلمي</option>
                    @foreach ($qualifications as $key => $value)
                        <option value="{{ $key }}" {{ old('qualification') === $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" onclick="nextStep(2)"
                        class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">
                    التالي ←
                </button>
            </div>
        </div>


    </fieldset>
</div>
