document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');

    if (!form) return;

    // --- 1. التحقق من المدخلات في المتصفح قبل الإرسال (Client-Side Validation) ---
    form.addEventListener('submit', (e) => {
        let hasError = false;

        // تنظيف رسائل الأخطاء السابقة التي أضيفت بواسطة JavaScript
        document.querySelectorAll('.js-error').forEach(el => el.remove());

        // أ) التحقق من الاسم
        const nameInput = document.getElementById('name');
        if (nameInput && nameInput.value.trim() === '') {
            showJsError(nameInput, 'Please enter your full name.');
            hasError = true;
        }

        // ب) التحقق من البريد الإلكتروني
        const emailInput = document.getElementById('email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailInput && !emailRegex.test(emailInput.value.trim())) {
            showJsError(emailInput, 'Please enter a valid email address.');
            hasError = true;
        }

        // ج) التحقق من رقم الهاتف الأردني
        const mobileInput = document.getElementById('mobile');
        const mobileClean = mobileInput ? mobileInput.value.replace(/[\s-]/g, '') : '';
        const mobileRegex = /^(\+?962|0)?7[789][0-9]{7}$/;
        if (mobileInput && !mobileRegex.test(mobileClean)) {
            showJsError(mobileInput, 'Please enter a valid Jordanian phone number (e.g., 0791234567).');
            hasError = true;
        }

        // د) التحقق من التحديد للمحافظة
        const govSelect = document.getElementById('governorate');
        if (govSelect && govSelect.value === '') {
            showJsError(govSelect, 'Please select your governorate.');
            hasError = true;
        }

        // هـ) التحقق من اختيار المسار (Track)
        const selectedTrack = document.querySelector('input[name="track"]:checked');
        if (!selectedTrack) {
            const trackLabel = document.querySelector('label:nth-of-type(5)'); // عنصر العناوين للمسار
            showJsError(trackLabel || form, 'Please select a track.');
            hasError = true;
        }

        // و) التحقق من اختيار مهارة واحدة على الأقل (Skills)
        const selectedSkills = document.querySelectorAll('input[name="skills[]"]:checked');
        if (selectedSkills.length === 0) {
            const skillsLabel = document.querySelectorAll('label')[9];
            showJsError(skillsLabel || form, 'Please select at least one skill.');
            hasError = true;
        }

        // ز) التحقق من الموافقة على الشروط
        const agreeCheckbox = document.querySelector('input[name="agree"]');
        if (agreeCheckbox && !agreeCheckbox.checked) {
            showJsError(agreeCheckbox.parentElement, 'You must agree to the terms.');
            hasError = true;
        }

        // إذا كان هناك أي خطأ، نمنع إرسال النموذج فوراً
        if (hasError) {
            e.preventDefault();
        }
    });

    // --- 2. دالة مساعدة لإظهار تنبيهات الأخطاء بشكل ديناميكي ---
    function showJsError(element, message) {
        const errorElement = document.createElement('span');
        errorElement.className = 'js-error';
        errorElement.style.color = 'red';
        errorElement.style.fontSize = '13px';
        errorElement.style.display = 'block';
        errorElement.style.marginTop = '4px';
        errorElement.innerText = message;

        element.insertAdjacentElement('afterend', errorElement);
    }

    // --- 3. حفظ خيارات المسار المفضل كجلسة محليّة (LocalStorage) ---
    const trackInputs = document.querySelectorAll('input[name="track"]');
    trackInputs.forEach(radio => {
        radio.addEventListener('change', (e) => {
            localStorage.setItem('preferred_track', e.target.value);
        });
    });

    // استرجاع المسار المفضل إذا لم يكن محدداً مسبقاً
    const savedTrack = localStorage.getItem('preferred_track');
    if (savedTrack && !document.querySelector('input[name="track"]:checked')) {
        const targetRadio = document.querySelector(`input[name="track"][value="${savedTrack}"]`);
        if (targetRadio) {
            targetRadio.checked = true;
        }
    }
});