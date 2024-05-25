
function toggleAuthWindow() {
    let authDialog = document.getElementById('auth_dialog');
    // if dialog is open, close it
    if (authDialog.open) {
        closeAuthWindow();
    } else {
        openAuthWindow();
    }
}

function openAuthWindow() {
    let authDialog = document.getElementById('auth_dialog');
    let authForm = document.getElementById('auth_form');
    let authIcon = document.getElementById('auth_icon');
    let authOverlay = document.getElementById('auth_dial_overlay');

    // show auth window
    authDialog.showModal();

    setTimeout(() => {
        authForm.style.maxHeight = '20rem'; // set max height of auth window
        authForm.style.padding = '1rem'; // set padding of auth window

        // add event listener to auth overlay to close auth window when clicked
        authOverlay.addEventListener('click', closeAuthWindow);

    }, 1);

    // make background colour of auth_icon blue
    authIcon.style.transition = 'background-color 0.2s';
    authIcon.style.backgroundColor = 'var(--alt-blue)';
}

function closeAuthWindow() {
    let authDialog = document.getElementById('auth_dialog');
    let authForm = document.getElementById('auth_form');
    let authIcon = document.getElementById('auth_icon');
    let authOverlay = document.getElementById('auth_dial_overlay');

    // remove event listener from auth overlay
    authOverlay.removeEventListener('click', closeAuthWindow);

    // hide auth window
    authForm.style.maxHeight = ''; // remove max height of auth window
    authForm.style.padding = ''; // remove padding of auth window

    // after .2s, hide auth window
    setTimeout(() => {
        authDialog.close(); // close auth window
    }, 200);

    // remove in-line background colour style from auth_icon
    authIcon.style.backgroundColor = '';
}

function setNotRequired(name) {
    // sets the input field with the given name to not required
    let input = document.getElementById(name);
    input.required = false;
}
