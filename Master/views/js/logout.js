document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("logout").addEventListener("click", function() {
        if (confirm("Opravdu se chcete odhlásit?")) {
            window.location.href = "../actions/logout.php";
        }
    });
});