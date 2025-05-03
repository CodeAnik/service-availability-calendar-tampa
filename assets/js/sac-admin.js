// In sac-admin.js
jQuery(document).ready(function($) {
    // Handle availability toggle
    $('#availability').change(function() {
        $('#availability_details').toggle($(this).val() === 'yes');
    }).trigger('change');

    // Date formatting function
    function formatAndDisplayDate(input, display) {
        let selectedDate = input.val();
        if (selectedDate) {
            let dateObj = new Date(selectedDate);
            let options = { month: 'long', day: 'numeric', year: 'numeric' };
            display.text('  ' + dateObj.toLocaleDateString('en-US', options));
        } else {
            display.text('');
        }
    }

    // Initialize date formatting if elements exist
    if ($('#availability_date').length && $('#display_date').length) {
        const dateInput = $('#availability_date');
        const dateDisplay = $('#display_date');
        
        dateInput.on('change', function() {
            formatAndDisplayDate(dateInput, dateDisplay);
        });
        
        // Initial format
        formatAndDisplayDate(dateInput, dateDisplay);
    }
});