(function ($) {
    // liste des utilisateurs
    $('#search_users').select2({
        width: 500,
        placeholder: "Recherchez / Sélectionnez un ou plusieurs utilisateurs dans la liste",
        templateSelection: function (user) {
            return user.text + " (" + user.element.dataset.role + ")";
        }
    });

    // formulaire de modifications des droits
    $('#form_updateRole').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            method: 'PUT',
            dataType: 'json',
            data: $(this).serialize(),
            error: function ($xhr) {
                var error = $xhr.responseJSON.error;

                addMessage(error, 'danger', 6000);
                console.error('form_updateRole', error);
            },
            success: function (data, textStatus, $xhr) {
                if ($xhr.status === 204) {
                    addMessage("Tous les droits étaient à jour, il n'y a pas eu de changements", 'info');
                } else if($xhr.status === 200) {
                    var errors = data.errors;
                    $.each(errors, function (k, errorsByType) {
                        for (var i = 0; i < errorsByType.length; i++) {
                            addMessage(errorsByType[i], k);
                        }
                    });
                }
            }
        })
    });
})(jQuery);