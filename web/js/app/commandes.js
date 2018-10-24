(function ($) {
    var $modal_addComment = $('#modal_addComment');
    var $form_addComment = $('#form_addComment');
    var $btn_addComment = $('.btn_addComment');
    var $builderData = $('#builder').data('values');
    console.log($builderData)
    // datepickers
    $('.datepicker').datepicker({
        maxDate: "+0d"
    });

    $('#builder').queryBuilder({
        select_placeholder: "Sélectionnez un filtre",
        optgroups: {
            tkmf: 'Filtrer par informations Commande:',
            client: 'Filtrer par informations Client:',
            dates: 'Filtrer par date:'
        },
        rules: $('#builder').data('rules'),
        filters: [
            {
                id: 'commande_serie_id',
                label: "Type de série",
                type: 'integer',
                input: 'select',
                values: $builderData.series,
                operators:  ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'tkmf'
            },
            {
                id: 'commande_vendor',
                label: "Vendeur",
                type: 'string',
                input: 'select',
                values: $builderData.users,
                operators:  ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'tkmf'
            },
            {
                id: 'Ilot_de_fabrication',
                label: "Ilot de fabrication",
                type: 'string',
                input: 'select',
                values: $builderData.ilots,
                operators:  ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'tkmf'
            },
            {
                id: 'commande_id',
                label: "N° de devis",
                type: 'integer',
                operators:  ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'tkmf'
            },
            {
                id: 'commande_bl_id',
                label: "N° de BL",
                type: 'integer',
                operators:  ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'tkmf'
            },
            {
                id: 'commande_cas_type',
                label: "N° de CAS",
                type: 'string',
                operators:  ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'tkmf'
            },
            {
                id: 'commande_quantity',
                label: "Quantité de pièces",
                type: 'integer',
                operators: ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'tkmf'
            },
            {
                id: 'commande_machine_ts',
                label: "Machine TS",
                type: 'integer',
                operators:  ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'tkmf'
            },
            {
                id: 'commande_client_name',
                label: "Nom du client",
                type: 'string',
                operators:  ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'client'
            },
            {
                id: 'commande_client_reference',
                label: "Référence commande",
                type: 'string',
                operators:  ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'client'
            },
            {
                id: 'processings.processing_at',
                label: "Lancement de la commande",
                type: 'date',
                operators:  ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'dates',
                plugin: 'datepicker',
                plugin_config: {
                    dateFormat: 'mm/dd/yy',
                    todayBtn: 'linked',
                    todayHighlight: true,
                    autoclose: true
                }
            },
            {
                id: 'commande_delivery_at',
                label: "Livraison de la commande",
                type: 'date',
                operators:  ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'client',
                plugin: 'datepicker',
                plugin_config: {
                    dateFormat: 'mm/dd/yy',
                    todayBtn: 'linked',
                    todayHighlight: true,
                    autoclose: true
                }
            },
            {
                id: 'commande_depart_ts',
                label: "Départ Travaux Spéciaux",
                type: 'date',
                operators: ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'dates',
                plugin: 'datepicker',
                plugin_config: {
                    dateFormat: 'mm/dd/yy',
                    todayBtn: 'linked',
                    todayHighlight: true,
                    autoclose: true
                }
            },
            {
                id: 'date_de_lancement',
                label: "Date de lancement de la commande",
                type: 'date',
                operators:  ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'dates',
                plugin: 'datepicker',
                plugin_config: {
                    dateFormat: 'mm/dd/yy',
                    todayBtn: 'linked',
                    todayHighlight: true,
                    autoclose: true
                }
            },
            {
                id: 'date_depart_atelier',
                label: "Date départ atelier",
                type: 'date',
                operators:  ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between', 'is_null'],
                optgroup: 'dates',
                plugin: 'datepicker',
                plugin_config: {
                    dateFormat: 'mm/dd/yy',
                    todayBtn: 'linked',
                    todayHighlight: true,
                    autoclose: true
                }
            }
        ]
    });

    $('#btn-builder').on('click', function (e) {
        e.preventDefault();
        var query = $('#builder').queryBuilder('getRules');

        if ($('#builder').queryBuilder('validate')) {
            query = window.btoa(JSON.stringify(query));
            window.location.search = "?query=" + query;
        }
    });


    $modal_addComment.dialog({
        width: 400,
        autoOpen: false,
        buttons: [
            {
                type: 'button',
                text: "Annuler",
                class: 'button2',
                click: function () {
                    $(this).dialog('close');
                }
            },
            {
                type: 'button',
                text: "Ajouter",
                class: 'button1',
                click: function (e) {
                    e.preventDefault();
                    $form_addComment.submit();
                }
            }
        ],
        open: function () {
            var $commande = $(this).data('commande');

            $(this).dialog('option', 'title', "Ajouter un suivi à la commande n°" + $commande.id);
        }
    });

    $form_addComment.on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            error: function ($xhr) {
                var response = $xhr.responseJSON;
                console.error(response);
            },
            success: function (comment) {
                console.log(comment);
                var $comments = $('#comments_' + $modal_addComment.data('commande').id);

                addMessage("Le suivi a bien été ajouté", 'success', 4000);
                addSimpleComment($comments, comment.type.type, comment.type.label, comment.author.displayname, comment.text, comment.date);

                $modal_addComment.dialog('close');
            }
        })
    });

    $btn_addComment.on('click', function (e) {
        e.preventDefault();

        $form_addComment.attr('action', $(this).data('href'));
        $modal_addComment.data('commande', $(this).data('commande'));
        $modal_addComment.dialog('open');
    });
})(jQuery);