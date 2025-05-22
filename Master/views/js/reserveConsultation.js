document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".reservationButton").forEach(button => {
        button.addEventListener("click", function () {
            const consultationId = this.getAttribute("data-id");
            const action = this.getAttribute("data-action");
            const selectedUserId = this.getAttribute("data-selected-user-id");
            this.classList.add('disabled');

            if (action === "reserve") {
                window.location.href = `consultationReserveDetail.php?id=${consultationId}&selectedUserId=${selectedUserId}`;
            } else if (action === "cancel") {
                fetch("../actions/cancelReservation.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ id: consultationId, selectedUserId: selectedUserId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Rezervace byla zrušena!");
                        this.textContent = "Rezervovat";
                        this.classList.remove("disabled");
                        this.style.backgroundColor = "";
                        this.setAttribute("data-action", "reserve");
                    } else {
                        alert("Chyba: " + data.error);
                    }
                })
                .catch(error => {
                    console.error("Chyba při zrušení rezervace:", error);
                });
            }
        });
    });
});