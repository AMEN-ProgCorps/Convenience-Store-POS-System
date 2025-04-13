function toggleChoice(target) {
    const choices = document.getElementsByClassName("choice_container");
    const login = document.getElementsByClassName("login_container");
    const register = document.getElementsByClassName("register_container");
    const status = document.getElementsByClassName("status_container");
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
        if(status[0].classList.contains("show_status")){
            Checkstatus();
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
function Checkstatus(){
    const status = document.getElementsByClassName("status_container");
    if(!status[0].classList.contains("show_status")){
        Array.from(status).forEach(item => item.classList.add("show_status"));
    }
    else{
        Array.from(status).forEach(item => item.classList.remove("show_status"));
        Array.from(status).forEach(item => item.classList.add("exit_status"));
        setTimeout(() => {
            Array.from(status).forEach(item => item.classList.remove("exit_status"));
        }, 500); // Match the CSS transition duration
    }
}