document.addEventListener("DOMContentLoaded", function () {

    // =================================
    // PASSWORD SHOW / HIDE
    // =================================

    const password = document.getElementById("password");
    const passwordToggle = document.getElementById("passwordToggle");

    passwordToggle.addEventListener("click", function () {

        if (password.type === "password") {

            password.type = "text";

            this.innerHTML =
                '<i class="fa-regular fa-eye"></i>';

            this.setAttribute(
                "aria-label",
                "Hide password"
            );

        } else {

            password.type = "password";

            this.innerHTML =
                '<i class="fa-regular fa-eye-slash"></i>';

            this.setAttribute(
                "aria-label",
                "Show password"
            );
        }
    });


    // =================================
    // CLOSE BUTTON
    // =================================

    const closeButton =
        document.getElementById("closeButton");

    closeButton.addEventListener("click", function () {

        window.location.href = "index.php";

    });


    // =================================
    // GOOGLE BUTTON
    // =================================

    const googleLogin =
        document.getElementById("googleLogin");

    googleLogin.addEventListener("click", function () {

        alert(
            "Google login will be connected when Google OAuth is configured."
        );

    });


    // =================================
    // LOGIN FORM VALIDATION
    // =================================

    const loginForm =
        document.getElementById("loginForm");

    loginForm.addEventListener("submit", function (event) {

        const login =
            document.getElementById("login").value.trim();

        const passwordValue =
            password.value.trim();

        if (login === "") {

            event.preventDefault();

            alert("Please enter your username or email.");

            document.getElementById("login").focus();

            return;
        }


        if (passwordValue === "") {

            event.preventDefault();

            alert("Please enter your password.");

            password.focus();

            return;
        }

    });

});