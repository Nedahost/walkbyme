jQuery(document).ready(function($) {
    $('select[name$="-filter"]').change(function() {
        var selectedFilters = {};

        $('select[name$="-filter"]').each(function() {
            var filterName = $(this).attr('name');
            var filterValue = $(this).val();
            if (filterValue) {
                selectedFilters[filterName] = filterValue;
            }
        });

        $.ajax({
            url: ajax_filters.ajax_url,
            type: 'GET',
            data: {
                action: 'update_filters',
                filters: selectedFilters
            },
            success: function(response) {
                $('.product-filters').html(response);
            }
        });
    });

    $(document).on('click', '.clear-filter', function() {
        var filterName = $(this).data('filter');
        $('select[name="' + filterName + '"]').val('').change();
    });
});
