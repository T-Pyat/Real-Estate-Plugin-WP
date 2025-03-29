jQuery(document).ready(function ($) {
  $(document).on('submit', '.estate-filter-form', function (e) {
    e.preventDefault();

    const $form = $(this);
    const resultsId = $form.data('results');
    const $results = $('#' + resultsId);

    $.ajax({
      url: estate_ajax.url,
      method: 'POST',
      data: {
        action: 're_ajax_filter_estates',
        filters: $form.serialize()
      },
      beforeSend: function () {
        $results.html('<p>' + estate_ajax.i18n.loading + '</p>');
      },
      success: function (response) {
        $results.html(response);
      },
      error: function () {
        $results.html('<p class="text-danger">' + estate_ajax.i18n.error + '</p>');
      }
    });
  });

  $(document).on('click', '.estate-page', function (e) {
    e.preventDefault();

    const page = $(this).data('page');
    const $form = $(this).closest('.container').find('form.estate-filter-form');
    const resultsId = $form.data('results');
    const $results = $('#' + resultsId);

    $.ajax({
      url: estate_ajax.url,
      method: 'POST',
      data: {
        action: 're_ajax_filter_estates',
        filters: $form.serialize(),
        paged: page
      },
      beforeSend: function () {
        $results.html('<p>' + estate_ajax.i18n.loading + '</p>');
      },
      success: function (response) {
        $results.html(response);
      }
    });
  });
});
