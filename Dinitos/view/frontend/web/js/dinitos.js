require(['jquery', 'Magento_Ui/js/modal/modal', 'select2'], function ($) {

    $(document).ready(() => {
        $('body').on('click', '.dinitos-info-btn', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation()
            $('.dinitos-info').toggleClass('is-active')
            $('.page-header').append($('.dinitos-info'));
        });

        function adjustTypeFilter() {
            if (window.innerWidth > 768) {
                $('#dinitos-type-filter').removeClass('select2-normal select2');

                $('#dinitos-type-filter').select2({
                    minimumResultsForSearch: Infinity,
                    placeholder: $(this).data('placeholder'),
                    closeOnSelect: true,
                    multiple: false,
                    width: 'resolve',
                    dropdownParent: $('#type-filter'),
                    dropdownAutoWidth: true,
                })
            }
        }

        const filterMap = {
            'obtained': ['.redeemed, .expired, .refunded, .deducted'],
            'redeemed': ['.accumulated, .expired, .refunded, .deducted'],
            'expired': ['.redeemed, .accumulated, .refunded, .deducted'],
            'refunded': ['.accumulated, .redeemed, .expired, .deducted'],
            'deducted': ['.accumulated, .redeemed, .expired, .refunded'],
            'position': ['.accumulated, .redeemed, .expired, .refunded, .deducted']
        };

        $('.movement-type-filter').on('change', function (e) {
            e.preventDefault();

            filterMap["position"].forEach(filter => $(filter).removeClass('type-excluded'))

            const selectedFilter = $(this).val();

            if (selectedFilter && filterMap[selectedFilter] && selectedFilter !== 'position') {
                filterMap[selectedFilter].forEach(selector => {
                    $(selector).addClass('type-excluded')
                });
            }
        })

        function earliestDate() {
            let dates = $('.dinitos-movement').map(function () {
                return new Date($(this).data('date'));
            }).get();
            return new Date(Math.min.apply(null, dates))
        }

        function latestDate() {
            let dates = $('.dinitos-movement').map(function () {
                return new Date($(this).data('date'));
            }).get();
            return new Date(Math.max.apply(null, dates))
        }

        $('#date-range-filter-btn').click(function (e) {
            e.stopImmediatePropagation()
            let startDate = $('#startDate').val() ? new Date($('#startDate').val()) : new Date(earliestDate());
            let endDate = $('#endDate').val() ? new Date($('#endDate').val()) : new Date(latestDate());

            // Just in case the user selects the dates in the wrong order
            if (startDate > endDate) {
                let tempDate = startDate;
                startDate = endDate;
                endDate = tempDate;
                $('#startDate').val(startDate.toISOString().split('T')[0]);
                $('#endDate').val(endDate.toISOString().split('T')[0]);
            }

            $('.dinitos-movement').each(function () {
                let rowDate = new Date($(this).data('date'));
                if (rowDate >= startDate && rowDate <= endDate) {
                    $(this).removeClass('date-excluded')
                } else {
                    $(this).addClass('date-excluded')
                }
            });
            $('.date-selector-container').toggleClass('is-hidden')
        });

        $('#reset-date-range-filter-btn').click(function (e) {
            e.stopImmediatePropagation()

            $('.dinitos-movement').each(function () {
                $(this).removeClass('date-excluded')
                $('.date-selector-container').toggleClass('is-hidden')
            });
        })

        let maxDate = null;
        let minDate = null;
        let maxDateString = '';
        let minDateString = '';

        $('.dinitos-movement').each(function () {
            let currentDate = new Date($(this).data('date'));

            if (!maxDate || currentDate > maxDate) {
                maxDate = currentDate;
            }
            if (!minDate || currentDate < minDate) {
                minDate = currentDate;
            }
        });

        if (maxDate instanceof Date && !isNaN(maxDate) && minDate instanceof Date && !isNaN(minDate)) {
            maxDateString = maxDate.toISOString().split('T')[0];
            minDateString = minDate.toISOString().split('T')[0];
        }

        $('#startDate, #endDate').attr({
            'max': maxDateString,
            'min': minDateString
        });

        $('#date-filter-btn').click(function (e) {
            e.stopImmediatePropagation()
            $('.date-selector-container').toggleClass('is-hidden');
        })

        $(document).mouseup(function (e) {
            e.stopImmediatePropagation()
            let container = $(".date-selector-container");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.addClass('is-hidden');
            }
        });

        // Important!!! Make filters visible
        $('#dinitos-history-filters').css('visibility', 'visible')

        // Type filter adjustment for mobile
        adjustTypeFilter();
    })
})