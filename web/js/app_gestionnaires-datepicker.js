/*(function($) {

    var $modalAddSupplying = $('#modal_gst_supplying-add');
    var $addSupplyingLink = $('#gst_supplying-add');
    var $formAddSupplying = $('#form_gst_supplying-add');
    var $formCommande = $('#form_gst_commande');
    var $totalQuantities = $('#gst_supplying-count');

    var $modalAddComment = $('#modal_gst_comment-add');
    var $modalEditComment = $('#modal_edit-comment');
    var $formAddComment = $('#form_gst_comment-add');
    var $addCommentLink = $('#gst_comment-add_href');

    var $datepickerQuantityAt = $('#gst_processing-datepicker');
    var $selectProcessingIlots = $('#gst_processing-select2');
    var $grid = $('.grid-items');

    var days = $datepickerQuantityAt.data('days');

    var updateTotalQuantities = function (quantity, operator) {
        var old_quantity = parseInt($totalQuantities.data('value'));

        if(operator === '+') {
            $totalQuantities.data('value', old_quantity + quantity);
        } else if(operator === '-') {
            $totalQuantities.data('value', old_quantity - quantity);
        }

        $totalQuantities.text($totalQuantities.data('value'));
    };

    var removeQuantity = function ($li) {
        var message = 'Êtes-vous sûr de vouloir supprimer le prélèvement:\n "' + $li.text() + '" ?';

        if(confirm(message)) {
            var id = $li.data('id');
            var quantity = $li.data('value');

            $('#' + id).remove();
            updateTotalQuantities(quantity, '-');
            $li.remove();
        }
    };

    var getIlots = function (date) {
        $.ajax({
            url: $selectProcessingIlots.data('src'),
            data: (
                (date !== undefined)
                ? { quantity_at: date }
                : null
            ),
            dataType: 'json',
            beforeSend: function () {
                var $option = $('<option/>')
                    .attr('id', "opt_searching")
                    .attr('disabled', true)
                    .text("Recherche en cours...");

                $selectProcessingIlots.val(null);
                $selectProcessingIlots.prepend($option);
                $selectProcessingIlots.select2('open');
            },
            error: function ($xhr, status, err) {
                console.error('select2:ajax', err);
                alert("Une erreur est survenue lors de la récupération des îlots");
            },
            success: function (items) {
                $('optgroup', $selectProcessingIlots).remove();

                for (var depot_id in items) {
                    var item = items[depot_id];
                    var depot = item.depot;
                    var ilots = item.ilots;
                    var $optgroup = $('<optgroup/>')
                        .attr('label', depot.name);

                    for (var k in ilots) {
                        var ilot = ilots[k].ilot || ilots[k];
                        var quantity = ilots[k].quantity;
                        var $option = $('<option/>')
                            .attr('value', ilot.name)
                            .data('color', ilot.color);

                        var text = ilot.name;
                        text = text + ((quantity !== undefined)
                            ? " (" + quantity + " pièces dispo.)" : " (quantité indispo.)");

                        $option.text(text);
                        $optgroup.append($option);
                    }

                    $selectProcessingIlots.append($optgroup);
                }

                $('li:first', '#select2-gst_processing-select2-results').slideUp(150, function () {
                    $selectProcessingIlots.select2('close');
                    $(this).remove();
                    $('#opt_searching').remove();
                    $selectProcessingIlots.select2('open');
                });
            }
        });
    };

    $grid.masonry({
        gutter: 16,
        horizontalOrder: true,
        itemSelector: '.grid-item'
    });

    $selectProcessingIlots.select2()
        .on('select2:opening', function () {
            if ($selectProcessingIlots.data('opened') === 0) {
                $selectProcessingIlots.data('opened', 1);
                getIlots(undefined);
            }
        });

    $addSupplyingLink.on('click', function (e) {
        e.preventDefault();
        $modalAddSupplying.dialog('open');
    });

    $addCommentLink.on('click', function (e) {
        e.preventDefault();
        $modalAddComment.dialog('open');
    });

    $modalAddSupplying.dialog({
        modal: true,
        autoOpen: false,
        draggable: false,
        resizable: false,
        width: 400,
        closeText: "Annuler",
        buttons: [
            {
                text: "Annuler",
                class: "button2",
                click: function () {
                    $(this).dialog('close');
                }
            },
            {
                text: "Ajouter",
                type: "submit",
                class: "button1",
                click: function () {
                    $formAddSupplying.submit();
                }
            }
        ]
    });

    $modalAddComment.dialog({
        modal: true,
        autoOpen: false,
        draggable: false,
        resizable: false,
        width: 500,
        closeText: "Annuler",
        buttons: [
            {
                text: "Annuler",
                class: "button2",
                click: function () {
                    $(this).dialog('close');
                }
            },
            {
                text: "Ajouter",
                type: "submit",
                class: "button1",
                click: function () {
                    $formAddComment.submit();
                }
            }
        ]
    });

    $modalEditComment.dialog({
        modal: true,
        autoOpen: false,
        draggable: false,
        resizable: false,
        closeText: "Annuler",
        open: function () {

        },
        buttons: [
            {
                text: "supprimer",
                class: "button2 delete",
                click: function () {
                    $(this).dialog('close');
                }
            },
            {
                text: "Annuler",
                class: "button2",
                click: function () {
                    $(this).dialog('close');
                }
            },
            {
                text: "Modifier",
                type: "submit",
                class: "button1",
                click: function () {
                    $(this).dialog('close');
                }
            }
        ]
    });

    $formAddSupplying.on('submit', function (e) {
        e.preventDefault();

        var quantity = parseInt($('#add_quantity', $(this)).val());
        var depot    = $('#depots option:selected', $(this));
        var id       = 'input_' + $.now();

        if(isNaN(quantity) || quantity < 1) {
            var $modal_errors = $('.modal-errors', $modalAddSupplying);

            $('#add_quantity').addClass('error');
            addModalMessage($modal_errors, 'quantity', "La quantité doit être d'au moins une pièce");
        } else {
            var $li = $('<li/>')
                .addClass('icon-tk-delete')
                .data('id', id)
                .data('value', quantity)
                .attr('title', "Supprimer ce prélèvement")
                .text(depot.text() + ": " + quantity + " pièces")
                .css('display', 'none');

            $li.on('click', function () {
                removeQuantity($(this));
            });

            var $input = $('<input/>')
                .attr('type', 'hidden')
                .attr('id', id)
                .attr('name', 'quantities[' + depot.val() + '][]')
                .attr('value', quantity);

            $formCommande.append($input);
            $('#gst_supplying-list').append($li.fadeIn(150));

            updateTotalQuantities(quantity, '+');
            $modalAddSupplying.dialog('close');
        }
    });

    $formAddComment.on('submit', function (e) {
        e.preventDefault();

        var id = 'input_' + $.now();
        var selected = $('#comments_types option:selected', $(this));
        var type = selected.text();
        var value = selected.val();
        var text = $('#comment_text', $(this)).val();
        var date = $('#comment_date', $(this)).val();

        if (text === "") {
            text = null;
        }

        var comment = {
            type: type,
            value: value,
            text: text,
            date: date
        };

        $type = $('<td/>').text(type)
            .addClass('text-center');
        $text = $('<td/>').text(text);
        $date = $('<td/>').text(date)
            .addClass('text-center');

        var $removea = $('<a/>')
            .data('id', id)
            .addClass('icon-tk-edit-single');
        var $removetd = $('<td/>').on('click', function (e) {
            var $tr = $(this).parent();
            var comment = $tr.data('comment');

            $modalEditComment.dialog('open');
        }).html($removea);

        var $tr = $('<tr/>')
            .data('id', id)
            .data('comment', comment);

        $('tr:first-child', '#table_comments').slideUp(150);
        $('#table_comments').append(
            $tr.append($removetd)
                .append($type)
                .append($text)
                .append($date)
        );

        $modalAddComment.dialog('close');
    });

    $formCommande.on('submit', function (e) {
        e.preventDefault();
        var $comments = $('#table_comments').find('tr[data-comment]');

        console.log($comments);
    });

    $datepickerQuantityAt.datepicker({
        changeMonth: true,
        altField: '#processing_at',
        beforeShowDay: function (date) {
            var $date = $.datepicker.formatDate('yy-mm-dd', date);
            if($date in days) {
                return [true, days[$date]];
            }
            return [true];
        },
        onSelect: function (date, $datepicker) {

            if (date !== $datepicker.lastVal) {
                $selectProcessingIlots.data('opened', 1);
                getIlots(date);
            } else {
                $selectProcessingIlots.select2('open');
            }
        }
    }).datepicker('setDate', new Date());

    $('#for_gst_supplying-add').on('click', function () {
        $modalAddSupplying.dialog('open');
    });

    $('#for_gst_processing-select2').on('click', function () {
        $selectProcessingIlots.select2('open');
    });

    $('#comment_date').datepicker({
        changeMonth: true
    }).datepicker('setDate', new Date());

})(jQuery);*/