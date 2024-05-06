window.onload = function() {
    let eventButton = document.getElementById('enable_desc');

    eventButton.addEventListener('click', toggleEventDesc);
}

function toggleEventDesc() {
    let eventDescEN = document.getElementById('event_desc_en');
    let eventDescDE = document.getElementById('event_desc_de');

    if (eventDescDE.disabled === true && eventDescEN.disabled === true) {
        eventDescDE.disabled = false;
        eventDescEN.disabled = false;
    }
    else {
        eventDescDE.disabled = true;
        eventDescEN.disabled = true;
    }
}

// this function is called when the value of a title input is changed
// it sets the value of the title in the other language to the corresponding value from the datalist
function setOtherTitle(currentTitle) {
    let currentInput = null;
    let otherInput = null;

    let otherTitle = null;

    // get the value of the current input & the other input element & set the other title
    if (currentTitle === 'en') {
        currentInput = document.getElementById('event_name_en').value;
        otherInput = document.getElementById('event_name_de');
        otherTitle = 'de';
    }
    else if (currentTitle === 'de') {
        currentInput = document.getElementById('event_name_de').value;
        otherInput = document.getElementById('event_name_en');
        otherTitle = 'en';
    }

    if (otherInput !== null && currentInput !== null) {
        // get the option element with the value of the current input (if it exists)
        let currentOption = document.querySelector(`#event_name_${currentTitle}_list option[value="${currentInput}"]`);

        // get the value of the option element (if it exists), else set it to null
        let currentDataValue = currentOption ? currentOption.dataset.value : null;

        if (currentDataValue !== null) {
            // get the option element with the data-value of the current option element
            let otherOption = document.querySelector(`#event_name_${otherTitle}_list option[data-value="${currentDataValue}"]`);

            // get the value of the other option element (if it exists), else set it to null
            let otherOptionValue = otherOption ? otherOption.value : null;

            if (otherOptionValue !== null) {
                // set the value of the other input to the value of the other option element
                otherInput.value = otherOptionValue;
            }
        }
    }
}