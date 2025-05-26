function toggleChoice(target) {
    const choices = document.getElementsByClassName("choice_container");
    const login = document.getElementsByClassName("login_container");
    const register = document.getElementsByClassName("register_container");
    const status = document.getElementsByClassName("status_container");
    const recaptcha = document.getElementsByClassName("recaptcha_container");
    if (target === "login") {
        Array.from(choices).forEach(choice => choice.classList.remove("active_show"));
        Array.from(choices).forEach(item => item.classList.add("deactive_show"));
        setTimeout(() => {
            Array.from(choices).forEach(item => item.classList.remove("deactive_show"));
            Array.from(login).forEach(item => item.classList.add("active_show"));
        }, 500); // Match the CSS transition duration
    } else if (target === "register") {
        Array.from(choices).forEach(choice => choice.classList.remove("active_show"));
        Array.from(choices).forEach(item => item.classList.add("deactive_show"));
        setTimeout(() => {
            Array.from(choices).forEach(item => item.classList.remove("deactive_show"));
            Array.from(register).forEach(item => item.classList.add("active_show"));
        }, 500); // Match the CSS transition duration
    } else {
        if (status[0].classList.contains("show_status")) {
            Checkstatus();
        }
        if (recaptcha[0].classList.contains("show_recaptcha")) {
            BotChecker();
        }
        setTimeout(() => {
            if (Array.from(login).some(item => item.classList.contains("active_show"))) {
                Array.from(login).forEach(item => item.classList.remove("active_show"));
                Array.from(login).forEach(item => item.classList.add("deactive_show"));
            }
            if (Array.from(register).some(item => item.classList.contains("active_show"))) {
                Array.from(register).forEach(item => item.classList.remove("active_show"));
                Array.from(register).forEach(item => item.classList.add("deactive_show"));
            }
            setTimeout(() => {
                Array.from(login).forEach(item => item.classList.remove("deactive_show"));
                Array.from(register).forEach(item => item.classList.remove("deactive_show"));
                Array.from(choices).forEach(choice => choice.classList.add("active_show"));
            }, 500); // Match the CSS transition duration

            // Clear input fields in login and register containers
            Array.from(login).forEach(item => {
                const inputs = item.querySelectorAll("input");
                inputs.forEach(input => input.value = "");
            });
            Array.from(register).forEach(item => {
                const inputs = item.querySelectorAll("input");
                inputs.forEach(input => input.value = "");
            });
        }, 600); // Match the CSS transition duration
    }
}
function Checkstatus() {
    const status = document.getElementsByClassName("status_container");
    if (!status[0].classList.contains("show_status")) {
        Array.from(status).forEach(item => item.classList.add("show_status"));
    } else {
        Array.from(status).forEach(item => item.classList.remove("show_status"));
        Array.from(status).forEach(item => item.classList.add("exit_status"));
        setTimeout(() => {
            Array.from(status).forEach(item => item.classList.remove("exit_status"));
        }, 500); // Match the CSS transition duration
    }
}

// Prevent site reset when showing errors
document.addEventListener('DOMContentLoaded', function () {
    const statusContainer = document.querySelector('.status_container');
    if (statusContainer && statusContainer.classList.contains('show_status')) {
        statusContainer.scrollIntoView({ behavior: 'smooth' });
    }
});

function BotChecker() {
    const recaptcha = document.getElementsByClassName("recaptcha_container");
    if (!recaptcha[0].classList.contains("show_recaptcha")) {
        Array.from(recaptcha).forEach(item => item.classList.add("show_recaptcha"));
    }
    else {
        Array.from(recaptcha).forEach(item => item.classList.remove("show_recaptcha"));
        Array.from(recaptcha).forEach(item => item.classList.add("exit_recaptcha"));
        setTimeout(() => {
            Array.from(recaptcha).forEach(item => item.classList.remove("exit_recaptcha"));
        }, 500); // Match the CSS transition duration
    }
}

function onClick(e) {
    e.preventDefault();
    grecaptcha.enterprise.ready(async () => {
        const token = await grecaptcha.enterprise.execute('6LeD9xcrAAAAABd3Q1NbdwgV0iF4Adpj1NNzFlC1', { action: 'LOGIN' });
    });
}