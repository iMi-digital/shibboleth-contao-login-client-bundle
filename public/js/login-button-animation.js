"use strict";
window.addEventListener('DOMContentLoaded', () => {
    let login_buttons = document.querySelectorAll('.sac-login-button-group button[type="submit"]');
    let i;
    for (i = 0; i < login_buttons.length; ++i) {
        let login_button = login_buttons[i];
        if (login_button) {
            login_button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                submitForm(login_button);

                return false;
            });
        }
    }

    // Special treatment for bootstrap modals
    let login_modal = document.querySelector('#loginModal');

    if (login_modal) {
        login_modal.addEventListener('shown.bs.modal', (event) => {
            let login_button = event.target;
            login_button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                submitForm(login_button);

                return false;
            });
        });
    }

    /**
     * Add an animation class to the button
     * @param login_button
     * @returns {boolean}
     */
    function submitForm(login_button) {
        if (login_button.classList.contains('button--loading')) {
            // Prevent multiple form submits
            return false;
        }

        login_button.classList.add('button--loading');
        window.setTimeout(() => {
            let formBe = document.getElementById('sac-oidc-login-be');
            let formFe = document.getElementById('sac-oidc-login-fe');

            if (formBe) {
                formBe.submit();
            }

            if (formFe) {
                formFe.submit();
            }

        }, 1000);
    }

});