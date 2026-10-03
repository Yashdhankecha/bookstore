// Initialize JS functions when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    
    // Setup quantity spinners if they exist
    const qtySpinners = document.querySelectorAll('.qty-spinner');
    if(qtySpinners) {
        qtySpinners.forEach(spinner => {
            const btnMinus = spinner.querySelector('.btn-minus');
            const btnPlus = spinner.querySelector('.btn-plus');
            const input = spinner.querySelector('input');

            if(btnMinus && btnPlus && input) {
                btnMinus.addEventListener('click', () => {
                    let val = parseInt(input.value);
                    if(val > 1) {
                        input.value = val - 1;
                    }
                });

                btnPlus.addEventListener('click', () => {
                    let max = parseInt(input.getAttribute('max')) || 100;
                    let val = parseInt(input.value);
                    if(val < max) {
                        input.value = val + 1;
                    }
                });
            }
        });
    }

    // Example of a custom auto-dismiss alert (if using bootstrap alerts)
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000); // 5 seconds dismiss
    });

});
