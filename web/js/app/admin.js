(function($) {
    var $removeSerie = $('#removeSerie');
    var $removeIlot = $('#removeIlot');

    var $selects2 = $('select.js-select2');
    $selects2.select2({
        placeholder: "Sélectionnez un ou plusieurs éléments",
        allowClear: true,
        closeOnSelect: false
    });

    // lien pour supprimer des éléments en utilisant HTTP DELETE
    var $ajaxDeleteHref = $('a[data-delete]').on('click', function (e) {
        e.preventDefault();

        var message = $(this).data('delete');
        message = message + "\n" + "Cette action est irrévessible.";

        var confirmed = confirm(message);
        if (confirmed) {
            $.ajax({
                url: $(this).attr('href'),
                method: 'DELETE',
                error: function ($xhr) {
                    var error = $xhr.responseJSON.error;
                    addMessage(error || "La suppression n'a pas aboutie");
                },
                success: function (data) {
                    sessionStorage.setItem('fastFlash', JSON.stringify({
                        type: 'success',
                        message: data.message
                    }));
                    window.location.replace(data.location);
                }
            });
        }
    });

    $removeSerie.on('click', function (e) {
        e.preventDefault();

        var confirmed = confirm("Supprimer le Gestionnaire ?\nCette action est irréverssible.");
        if (confirmed) {
            $.ajax({
                url: $removeSerie.attr('href'),
                method: 'DELETE',
                error: function ($xhr) {
                    var error = $xhr.responseJSON.error;

                    console.log(error);
                    addMessage(error || "Une erreur est survenue lors de la suppresion");
                },
                success: function ($serie) {
                    var $flash = {
                        type: 'success',
                        message: "Le Gestionnaire " + $serie.id + " a bien été supprimé"
                    };

                    sessionStorage.setItem('fastFlash', JSON.stringify($flash));
                    window.location.replace($removeSerie.data('location'));
                }
            })
        }
    });

    $removeIlot.on('click', function (e) {
        e.preventDefault();

        var confirmed = confirm("Supprimer l'îlot ?\nCette action est irréverssible.");
        if (confirmed) {
            $.ajax({
                url: $removeIlot.attr('href'),
                method: 'DELETE',
                error: function ($xhr) {
                    var error = $xhr.responseJSON.error;

                    console.log(error);
                    addMessage(error || "Une erreur est survenue lors de la suppresion");
                },
                success: function ($ilot) {
                    var $flash = {
                        type: 'success',
                        message: "L'îlot " + $ilot.name + " a bien été supprimé"
                    };

                    sessionStorage.setItem('fastFlash', JSON.stringify($flash));
                    window.location.replace($removeIlot.data('location'));
                }
            })
        }
    });
})(jQuery);