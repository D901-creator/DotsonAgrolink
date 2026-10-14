document.addEventListener("DOMContentLoaded", function () {

    // Password visibility
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirm_password");

    const togglePassword = document.getElementById("togglePassword");
    const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");

    togglePassword.addEventListener("click", function () {
        toggleField(passwordInput, togglePassword);
    });

    toggleConfirmPassword.addEventListener("click", function () {
        toggleField(confirmPasswordInput, toggleConfirmPassword);
    });

    function toggleField(input, button) {

        if (input.type === "password") {
            input.type = "text";
            button.classList.remove("fa-eye-slash");
            button.classList.add("fa-eye");
        } else {
            input.type = "password";
            button.classList.remove("fa-eye");
            button.classList.add("fa-eye-slash");
        }
    }

    // Profile image preview
    const profileInput = document.getElementById("profile_picture");
    const profilePreview = document.getElementById("profilePreview");

    profileInput.addEventListener("change", function () {

        const file = this.files[0];

        if (!file) {
            return;
        }

        const allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/jpg"
        ];

        if (!allowedTypes.includes(file.type)) {
            alert("Please select a JPG or PNG image.");
            this.value = "";
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert("Profile picture must not exceed 5MB.");
            this.value = "";
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            profilePreview.src = event.target.result;
        };

        reader.readAsDataURL(file);
    });
    const phoneInput = document.getElementById("phone");

phoneInput.addEventListener("input", function () {
    this.value = this.value.replace(/[^0-9+]/g, "");
});

    // Password matching
    const form = document.getElementById("registerForm");

    form.addEventListener("submit", function (event) {

        if (passwordInput.value !== confirmPasswordInput.value) {
            event.preventDefault();

            alert("Passwords do not match.");

            confirmPasswordInput.focus();
        }
    });

});