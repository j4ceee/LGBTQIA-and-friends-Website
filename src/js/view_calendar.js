
window.addEventListener('load', function() { // when page is loaded

    // get all calendar items and add an event listener to each
    // -> will allow the details of the calendar item to be expanded/collapsed
    document.querySelectorAll('.calendar_button').forEach(item => {
        item.addEventListener('click', (event) => {
            const eventId = event.currentTarget.getAttribute('data-event-id');
            toggleCalDetails(event, eventId);
        });
    });

    // get all summary elements and add an event listener to each
    // -> clicking on the summary element will trigger the click event on the corresponding calendar item
    document.querySelectorAll('.calendar_item_desc_ctrl').forEach(item => {
        item.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const eventId = event.currentTarget.getAttribute('data-event-id');
            // click the corresponding calendar item
            document.getElementById('calendar_button_' + eventId).click();
        });
    });

    let iCal = null;

    try {
        iCal = document.getElementById('ical_controls_cont');
        // get the ical controls container
        // iCal is element if found, null if not found
    } catch (e) {
        // do nothing
    }

    if (iCal === null) {
        // if iCal is not found, do not add event listeners

        return;
    }

    // add an event listener to the default calendar copy button
    document.getElementById('default_calendar_copy_button').addEventListener('click', () => {
        let anchorElement = document.getElementById('default_calendar_link');
        // default_calendar_link is the anchor tag that contains the link, copy a href attribute
        let copyText = anchorElement.getAttribute('href');
        navigator.clipboard.writeText(copyText).then(function() {
            // show a success message
            alert('Link copied to clipboard:\n' + copyText);
        });
    });

    // add an event listener to the year selection dropdown
    document.getElementById('year_calendar_select').addEventListener('change', (event) => {
        let dropdownElement = event.currentTarget;

        let anchorElement = document.getElementById('year_calendar_link');

        // update the links to the selected year link
        let yearLink = dropdownElement.value;

        anchorElement.setAttribute('href', yearLink);
    });

    // add an event listener to the copy button for the year calendar
    document.getElementById('year_calendar_copy_button').addEventListener('click', () => {
        let anchorElement = document.getElementById('year_calendar_link');
        // year_calendar_link is the anchor tag that contains the link, copy a href attribute
        let copyText = anchorElement.getAttribute('href');
        navigator.clipboard.writeText(copyText).then(function() {
            // show a success message
            alert('Link copied to clipboard:\n' + copyText);
        });
    });
});

function toggleCalDetails(event, eventId) {
    // this function toggles the visibility of the corresponding calendar details
    const event_button = event.currentTarget;
    const details = document.getElementById('event_det_' + eventId);

    const isExpanded = details.hasAttribute('open');

    if (isExpanded) {
        // if the details are already open, close them
        details.removeAttribute('open');

        // add calendar_item_past class if the event is in the past
        if (event_button.getAttribute('data-past-class') === 'true') {
            event_button.classList.add('calendar_item_past');
            event_button.removeAttribute('data-past-class');
        }
    } else {
        // if the details are closed, open them
        details.setAttribute('open', 'open');

        // remove calendar_item_past class & store it in temp attribute
        if (event_button.classList.contains('calendar_item_past')) {
            event_button.setAttribute('data-past-class', 'true');
            event_button.classList.remove('calendar_item_past');
        }
    }
}
