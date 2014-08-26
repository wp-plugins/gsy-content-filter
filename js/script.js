(function($) {
    'use strict';

    $(document).ready(function() {
        var gsyContentFilter,
                addFilterBtn,
                removeAllFiltersBtn,
                formTable;

        gsyContentFilter = $('#gsy-content-filter');
        addFilterBtn = $('.add-filter', gsyContentFilter);
        removeAllFiltersBtn = $('.remove-all-filters', gsyContentFilter);
        formTable = $('.form-table', gsyContentFilter);

        // Attach events
        addFilterBtn.on('click', addFilter);
        removeAllFiltersBtn.on('click', removeAllFilters);
        $('.delete-this-filter').on('click', deleteThisFilter);

        checkForFilledElements();

        // If a field Old Word has a content then
        // enable and show all corresponding elements
        function checkForFilledElements() {

            $('.old-word', formTable).each(function() {

                if ($.trim($(this).val()) !== '') {

                    $('.new-word, .filter-type, .case-sensitive',
                            $(this)
                            .removeAttr('disabled')
                            .closest('tr')
                            .show()
                            .nextAll(':lt(3)')
                            .show()
                            )
                            .removeAttr('disabled');
                }

            });

            if ($('tr:visible', formTable).size() > 0) {
                removeAllFiltersBtn.removeAttr('disabled');
            }

            if ($('tr:hidden', formTable).size() === 0) {
                addFilterBtn.attr('disabled', 'disabled');
            }
        }

        // Add new filter
        function addFilter(event) {
            event.preventDefault();

            $('tr', formTable).each(function() {
                // If the cuurent TR row is hidden then show it and
                // exit the loop
                if ($(this).is(":hidden")) {

                    $('.old-word', $(this)).removeAttr('disabled');

                    $('.new-word, .filter-type, .case-sensitive', $(this).nextAll(':lt(3)')).removeAttr('disabled');

                    $(this).show('slow')
                            .nextAll(':lt(3)')
                            .show('slow');

                    if ($('tr', formTable).filter(":hidden").size() === 0) {
                        addFilterBtn.attr('disabled', 'disabled');
                    }

                    return false;
                }

            });

            if (removeAllFiltersBtn.prop('disabled')) {
                removeAllFiltersBtn.removeAttr('disabled');
            }
        }

        // Remove all existed filters
        function removeAllFilters(event) {
            event.preventDefault();

            if (!window.confirm('Are you sure you want to remove all filters?')) {
                return false;
            }

            $('tr', formTable).hide('slow');
            $('tr .old-word, tr .new-word, tr .filter-type, tr .case-sensitive', formTable).attr('disabled', 'disabled');

            removeAllFiltersBtn.attr('disabled', 'disabled');

            if (addFilterBtn.prop('disabled')) {
                addFilterBtn.removeAttr('disabled');
            }

        }

        // Delete only this filter
        function deleteThisFilter(event) {
            event.preventDefault();

            $('.new-word, .filter-type, .case-sensitive', $(this).siblings()
                    .attr('disabled', 'disabled')
                    .closest('tr')
                    .hide('slow')
                    .nextAll(':lt(3)')
                    .hide('slow')).attr('disabled', 'disabled');

            // Whait for 600 ms as the upper code is still getting exexuted 
            // because of the 'slow' option
            setTimeout(function() {
                if ($('tr', formTable).filter(":visible").length === 0) {
                    removeAllFiltersBtn.attr('disabled', 'disabled');
                }
            }, 600);

            if (addFilterBtn.prop('disabled')) {
                addFilterBtn.removeAttr('disabled');
            }

        }

    });

})(jQuery);