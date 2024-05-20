
window.addEventListener('load', function() { // when page is loaded

    // get all calendar items and add an event listener to each
    document.querySelectorAll('.calendar_button').forEach(item => {
        item.addEventListener('click', (event) => {
            const eventId = event.currentTarget.getAttribute('data-event-id');
            toggleCalDetails(event, eventId);
        });
    });

    // get all summary elements and add an event listener to each
    document.querySelectorAll('.calendar_item_desc_ctrl').forEach(item => {
        item.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const eventId = event.currentTarget.getAttribute('data-event-id');
            // click the corresponding calendar item
            document.getElementById('calendar_button_' + eventId).click();
        });
    });

});


function toggleCalDetails(event, eventId) {
  // this function toggles the visibility of the corresponding calendar details
    const button = event.currentTarget;
    const details = document.getElementById('event_det_' + eventId);

    const isExpanded = button.getAttribute('aria-expanded') === 'true';
    button.setAttribute('aria-expanded', !isExpanded);

    if (isExpanded) {
        details.removeAttribute('open');
    } else {
        details.setAttribute('open', 'open');
    }
}
