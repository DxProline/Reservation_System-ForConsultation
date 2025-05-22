document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".create-consultation-submit").forEach(button => {
        button.addEventListener("click", function (event) {
            const clickedButton = event.target;
            clickedButton.classList.add('disabled');
            return true;
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const error = new URLSearchParams(window.location.search).get('error');
    if (error === 'overlap') {
        const consultationDate = document.querySelector("#consultationDate");
        const errorMessage = document.querySelector("#overlap-error-message");
        consultationDate.classList.add("error");
        errorMessage.style.display = 'block';
    }
});


document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("#consultation-form").addEventListener("submit", function (event) {
        const consultationDate = document.querySelector("#consultationDate");
        const duration = document.querySelector("#duration");
        const dateErrorMessage = document.querySelector("#date-error-message");
        const durationErrorMessage = document.querySelector("#duration-error-message");
        const clickedButton = document.querySelector(".create-consultation-submit");
        const subject = document.querySelector("#subject");
        const subjectErrorMessage = document.querySelector("#subject-error-message");
        let isValid = true;

        const currentDate = new Date();
        const selectedDate = new Date(consultationDate.value);

        if (!consultationDate.value || selectedDate < currentDate) {
            event.preventDefault();
            consultationDate.classList.add("error");
            dateErrorMessage.style.display = 'block';
            isValid = false;
            clickedButton.classList.remove('disabled');
        } else {
            consultationDate.classList.remove("error");
            dateErrorMessage.style.display = 'none';
        }

        if (duration.value < 10 || duration.value > 100) {
            event.preventDefault();
            duration.classList.add("error");
            durationErrorMessage.style.display = 'block';
            isValid = false;
            clickedButton.classList.remove('disabled');
        } else {
            duration.classList.remove("error");
            durationErrorMessage.style.display = 'none';
        }

        if (subject.value.length >  80) {
            event.preventDefault();
            subject.classList.add("error");
            subjectErrorMessage.style.display = 'block';
            isValid = false;
            clickedButton.classList.remove('disabled');
        } else {
            subject.classList.remove("error");
            subjectErrorMessage.style.display = 'none';
        }

        return isValid;
    });
});