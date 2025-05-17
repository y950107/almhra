@extends('layouts.guest')

@section('title', 'تقديم الطلب للمترشحين')

@section('content')

    <div class="py-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-6">تقديم الطلب للمترشحين</h1>

        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-600 rounded">
                {{session()->get('success')}}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        title: 'تم إرسال الطلب!',
                        text: 'تم تقديم طلبك بنجاح.',
                        icon: 'success',
                        confirmButtonText: 'حسنًا'
                    });
                });
            </script>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-600 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                        <li>يرجى اعادة تحميل الملفات !</li>
                </ul>
            </div>
        @endif

        <!-- Steps Indicator -->
        <div class="mb-8">
            <ol class="flex items-center justify-center w-full">
                <li class="flex items-center  after:content-[''] after:w-16 after:h-1 after:border-b after:border-blue-200 after:border-4 after:inline-block">
                    <span
                        class="flex items-center justify-center w-8 h-8 bg-blue-400 rounded-full lg:h-12 lg:w-12 shrink-0">
                        <span class="text-white">1</span>
                    </span>
                </li>
                <li class="flex items-center after:content-[''] after:w-16 after:h-1 after:border-b after:border-blue-200 after:border-4 after:inline-block">
                    <span
                        class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full lg:h-12 lg:w-12 shrink-0">
                        <span class="text-gray-500">2</span>
                    </span>
                </li>

                <li class="flex items-center">
                    <span
                        class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full lg:h-12 lg:w-12 shrink-0">
                        <span class="text-gray-500">3</span>
                    </span>
                </li>
            </ol>
        </div>

        <form action="{{ route('candidate.store') }}" method="POST" enctype="multipart/form-data"
              class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl space-y-4 m-auto" id="multiStepForm">
            @csrf

            <!-- Step 1 - Personal Info -->
            @include('Candidate.personal-info-step')

            <!-- Step 2 - Program type -->
            @include('Candidate.program-type-step')


            <!-- Step 3 - Quranic Info -->
            @include('Candidate.quranic-info-step')


        </form>
    </div>



    <!-- Keep all existing scripts exactly as they were -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script>
        $(document).ready(function () {


            $('#qualification').select2({
                placeholder: "اختر المؤهل العلمي",
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: Infinity // Hides the search box
            });

            $('#program_type').select2({
                placeholder: "اختر البرنامج",
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: Infinity
            }).on('change', function () {
                const selected = $(this).val();

                if (selected === 'maqraa') {
                    $('#maqraa_fields').removeClass('hidden');
                    $('#maqraa_file').removeClass('hidden');
                } else {
                    $('#maqraa_fields').addClass('hidden');
                    $('#maqraa_file').addClass('hidden');
                }
            }).trigger('change');



            $('#quran_level').select2({
                placeholder: "اختر المستوى",
                allowClear: true,
                width: '100%', // Important
                minimumResultsForSearch: Infinity // Hides the search box
            });

            $('#has_ijaza').select2({
                placeholder: "اختر",
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: Infinity // Hides the search box
            });



            $('#self_evaluation').select2({
                placeholder: "اختر التقييم الذاتي",
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: Infinity // Hides the search box
            });


                // Toggle password visibility for the first password field
                $('#toggle-password').on('click', function () {
                    var passwordField = $('#password');
                    var eyeIcon = $('#eye-icon');
                    var eyeIconCrossed = $('#eye-icon-crossed');

                    if (passwordField.attr('type') === 'password') {
                        passwordField.attr('type', 'text');
                        eyeIcon.addClass('hidden');
                        eyeIconCrossed.removeClass('hidden');
                    } else {
                        passwordField.attr('type', 'password');
                        eyeIcon.removeClass('hidden');
                        eyeIconCrossed.addClass('hidden');
                    }
                });

                // Toggle password visibility for the password confirmation field
                $('#toggle-password-confirm').on('click', function () {
                    var passwordConfirmField = $('#password_confirmation');
                    var eyeIconConfirm = $('#eye-icon-confirm');
                    var eyeIconCrossedConfirm = $('#eye-icon-crossed-confirm');

                    if (passwordConfirmField.attr('type') === 'password') {
                        passwordConfirmField.attr('type', 'text');
                        eyeIconConfirm.addClass('hidden');
                        eyeIconCrossedConfirm.removeClass('hidden');
                    } else {
                        passwordConfirmField.attr('type', 'password');
                        eyeIconConfirm.removeClass('hidden');
                        eyeIconCrossedConfirm.addClass('hidden');
                    }

            });


        });
    </script>


    <script>
        document.getElementById('multiStepForm').addEventListener('submit', function (e) {
            e.preventDefault();
            document.querySelectorAll('.step').forEach(stepEl => {
                stepEl.querySelectorAll('input, select, textarea').forEach(input => {
                    input.disabled = false; // Re-enable all inputs before submit
                });
            });
            this.submit();
        });
    </script>

    <script>
        function updateFileName(inputId, outputId) {
            var fileInput = document.getElementById(inputId);
            var fileName = fileInput.files.length > 0 ? fileInput.files[0].name : 'لم يتم اختيار ملف';
            document.getElementById(outputId).textContent = fileName;
        }

        function showStep(stepToShow) {
            document.querySelectorAll('.step').forEach((stepEl, index) => {
                const isVisible = stepEl.classList.contains(`step-${stepToShow}`);
                stepEl.classList.toggle('hidden', !isVisible);

                // Disable all inputs in hidden steps
                stepEl.querySelectorAll('input, select, textarea').forEach(input => {
                    input.disabled = !isVisible;
                });
            });
        }

        function updateStepIndicator(step) {
            const indicators = document.querySelectorAll('ol li');
            indicators.forEach((li, index) => {
                const circle = li.querySelector('span:first-child');
                const number = circle.querySelector('span');

                if (index < step) {
                    circle.classList.add('bg-blue-400');
                    circle.classList.remove('bg-gray-100');
                    number.classList.add('text-white');
                    number.classList.remove('text-gray-500');
                } else {
                    circle.classList.remove('bg-blue-400');
                    circle.classList.add('bg-gray-100');
                    number.classList.remove('text-white');
                    number.classList.add('text-gray-500');
                }
            });
        }


        function nextStep(next) {
            const currentStep = next - 1;
            const stepElement = document.querySelector(`.step-${currentStep}`);

            const inputs = stepElement.querySelectorAll('input, select, textarea');
            let valid = true;

            inputs.forEach(input => {
                if (input.hasAttribute('required') && !input.value) {
                    input.classList.add('border-red-500');
                    valid = false;
                } else {
                    input.classList.remove('border-red-500');
                }

                // Special validation for checkboxes (e.g., qualification, recitations, etc.)
                if (input.type === 'checkbox') {
                    const name = input.name;
                    const group = stepElement.querySelectorAll(`input[name="${name}"]`);
                    const isChecked = Array.from(group).some(checkbox => checkbox.checked);

                    if (!isChecked && input.hasAttribute('required')) {
                        group.forEach(cb => cb.classList.add('ring-2', 'ring-red-500'));
                        valid = false;
                    } else {
                        group.forEach(cb => cb.classList.remove('ring-2', 'ring-red-500'));
                    }
                }

                if (input.hasAttribute('required') &&
                    ((input.tagName === 'SELECT' && input.value === '') ||
                        (input.tagName !== 'SELECT' && !input.value))) {

                    input.classList.add('border-red-500');

                    // If Select2, also apply error class to its container
                    if ($(input).hasClass('select2-hidden-accessible')) {
                        $(input).next('.select2-container').find('.select2-selection').addClass('border-red-500');
                    }

                    valid = false;
                } else {
                    input.classList.remove('border-red-500');
                    if ($(input).hasClass('select2-hidden-accessible')) {
                        $(input).next('.select2-container').find('.select2-selection').removeClass('border-red-500');
                    }
                }

            });



           if (!valid) {
                Swal.fire({
                    title: 'يرجى إكمال البيانات',
                    text: 'يجب تعبئة جميع الحقول المطلوبة قبل الانتقال.',
                    icon: 'warning',
                    confirmButtonText: 'حسنًا'
                });
                return;
            }

            showStep(next);
            updateStepIndicator(next);
        }


        function prevStep(prev) {
            showStep(prev);
            updateStepIndicator(prev);
        }

        // Initially show the first step and disable others
        document.addEventListener('DOMContentLoaded', () => {
            showStep(1);
        });
    </script>

@endsection




