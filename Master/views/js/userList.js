document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".userPromoteButton").forEach(button => {
        button.addEventListener("click", function () {
            const userId = this.getAttribute("data-id");
            updateUserType(userId, "promote");
        });
    });

    document.querySelectorAll(".userDegradeButton").forEach(button => {
        button.addEventListener("click", function () {
            const userId = this.getAttribute("data-id");
            updateUserType(userId, "degrade");
        });
    });

    function updateUserType(userId, action) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../actions/updateUserType.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                location.reload(); // Reload the page to reflect changes
            }
        };
        xhr.send("userId=" + userId + "&action=" + action);
    }
});
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.userSelectButton');

    buttons.forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-id');
            const filter = this.getAttribute('data-filter');

            let targetPage = '';
            if (filter === 'students') {
                targetPage = 'consultationListS.php';
            } else if (filter === 'teachers' || filter === 'admins') {
                targetPage = 'consultationListT.php';
            }

            if (targetPage) {
                window.location.href = `${targetPage}?selectedUserId=${userId}`;
            }
        });
    });
});