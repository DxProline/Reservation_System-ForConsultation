document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".create-consultation").forEach(button => {
        button.addEventListener("click", function () {
            const selectedUserId = new URLSearchParams(window.location.search).get('selectedUserId');
            window.location.href = `../views/consultationCreateDetail.php?selectedUserId=${selectedUserId}`;
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.reservationButton.cancel');

    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const consultationId = this.getAttribute('data-id');
            const action = this.getAttribute('data-action');
            const selectedUserId = new URLSearchParams(window.location.search).get('selectedUserId');

            if (action === 'cancel') {
                if (confirm('Opravdu chcete zrušit tuto konzultaci?')) {
                    fetch(`../actions/cancelConsultation.php?selectedUserId=${selectedUserId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id: consultationId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Konzultace byla zrušena.');
                            location.reload();
                        } else {
                            alert('Nepodařilo se zrušit konzultaci.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Došlo k chybě při zrušení konzultace.');
                    });
                }
            }
        });
    });
});