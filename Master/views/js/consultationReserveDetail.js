document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".reserve-consultation-submit").forEach(button => {
        button.addEventListener("click", function (event) {
            const clickedButton = event.target;
            clickedButton.classList.add('disabled');
            return true;
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("#consultation-form").addEventListener("submit", function (event) {
        const subject = document.querySelector("#subject");
        const subjectTooLongMessage = document.querySelector("#subject-too-long-message");
        const subjectEmptyErrorMessage = document.querySelector("#subject-empty-message");
        const clickedButton = document.querySelector(".reserve-consultation-submit");
        let isValid = true;

        if (subject.value.length === 0) {
            event.preventDefault();
            subject.classList.add("error");
            subjectEmptyErrorMessage.style.display = 'block';
            isValid = false;
            clickedButton.classList.remove('disabled');
        } else {
            subject.classList.remove("error");
            subjectEmptyErrorMessage.style.display = 'none';
        }

        if (subject.value.length > 80) {
            event.preventDefault();
            subject.classList.add("error");
            subjectTooLongMessage.style.display = 'block';
            isValid = false;
            clickedButton.classList.remove('disabled');
        } else {
            subject.classList.remove("error");
            subjectTooLongMessage.style.display = 'none';
        }

        return isValid;
    });
});