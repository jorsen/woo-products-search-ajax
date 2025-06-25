jQuery(document).ready(function($) {
    // Trigger Ajax request when the user types in the search box
    $('#custom-search-input').on('keyup', function() {
        var searchQuery = $(this).val();

        // If query length is more than 2 characters, start searching
        if (searchQuery.length > 2) {
            // Show the spinner
            $('#loading-spinner').show();

            $.ajax({
                url: wp_vars.ajax_url,   // Ajax URL
                method: 'GET',
                data: {
                    action: 'custom_search_products',  // Action name that will be hooked in PHP
                    s: searchQuery
                },
                success: function(response) {
                    $('#search-results').html(response);  // Update the results container
                    $('#loading-spinner').hide(); // Hide the spinner once the search is done
                },
                error: function() {
                    $('#search-results').html('<p>An error occurred. Please try again.</p>');
                    $('#loading-spinner').hide(); // Hide the spinner on error
                }
            });
        } else {
            $('#search-results').html('');  // Clear results if query is too short
            $('#loading-spinner').hide();  // Hide the spinner if there's no query
        }
    });

    // Show full categories when Expand link is clicked
    $(document).on('click', '#expand-categories', function(e) {
        e.preventDefault();  // Prevent the default action (which is going to the top of the page)

        // Reveal all categories by showing all '.category-item'
        $('#search-results .category-item').show();

        // Hide the "Expand" link after it's clicked
        $('#expand-categories').hide();
    });
});


jQuery(document).ready(function ($) {

    /* === Live search === */
    $('#custom-search-input').on('keyup', function () {
        const s = $(this).val().trim();

        if (s.length <= 2) {
            $('#search-results').empty();
            $('#loading-spinner').hide();
            return;
        }

        $('#loading-spinner').show();

        $.get(wp_vars.ajax_url, { action: 'custom_search_products', s })
            .done(resp => $('#search-results').html(resp))
            .fail(() => $('#search-results').html('<p>An error occurred. Please try again.</p>'))
            .always(() => $('#loading-spinner').hide());
    });

    /* === Progressive “Expand” (5 at a time) === */
    $(document).on('click', '#wps-expand', function (e) {
        e.preventDefault();

        const step   = Number($(this).data('step')) || 5;
        const hidden = $('.wps-cat-hidden:hidden').slice(0, step);

        hidden.slideDown();                     // reveal next chunk
        if ($('.wps-cat-hidden:hidden').length === 0) {
            $(this).remove();                   // nothing left to show
        }
    });

});
