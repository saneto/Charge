(function($) {
    /**
     * Formulaire d'envoi de la commande.
     * @type {*|HTMLElement}
     */
    var $form_reserve = $('#form_gst-reserve');

    /**
     * Lien pour ajouter un commentaire.
     * @type {*|HTMLElement}
     */
    var $href_commentAdd = $('#href_gst-reserve_comment-add');

    /**
     * Modal pour ajouter un commentaire.
     * @type {*|HTMLElement}
     */
    var $modal_commentAdd = $('#modal_gst-reserve_comment-add');

    /**
     * Formulaire pour ajouter un commentaire.
     * @type {*|HTMLElement}
     */
    var $form_commentAdd = $('#form_modal_gst-reserve_comment-add');

    /**
     * Modal pour modifier un commentaire.
     * @type {*|HTMLElement}
     */
    var $modal_commentEdit = $('#modal_gst-reserve_comment-edit');

    /**
     * Formulaire pour modifier un commentaire.
     * @type {*|HTMLElement}
     */
    var $form_commentEdit = $('#form_modal_gst-reserve_comment-edit');

    /**
     * Modal pour ajouter un traitement.
     * @type {*|HTMLElement}
     */
    var $modal_suppylingAdd = $('#modal_gst-reserve_supplying-add');

    /**
     * Formulaire pour ajouter un traitement.
     * @type {*|HTMLElement}
     */
    var $form_suppylingAdd = $('#form_modal_gst-reserve_supplying-add');

    /**
     * Lien pour modifier un traitement.
     * @type {*|HTMLElement}
     */
    var $href_supplyingAdd = $('#gst-reserve_supplying-add');

    /**
     * Fullcalendar des ilôts.
     * @type {*|HTMLElement}
     */
    var $fullcalendar_supplying = $('#gst-reserve_fullcalendar');

    /**
     * Tableau des commentaires de suivis commandes.
     * @type {*|HTMLElement}
     */
    var $commentsTable = $('#table_gst-reserve_comments');

    /**
     * Datepickers
     * @type {*|HTMLElement}
     */
    var $datepickers = $('input.datepicker');

    var $supplying_events = {
        id: 'supplying',
        events: []
    };

    var $supplyingCount = $('#count_supplying');

    var $modal_success = $('#modal_success');

    /**
     * Ajout d'un commentaire au tableau.
     *
     * @param {string} type
     * @param {string} label
     * @param {null|string} comment_text
     * @param {null|string} comment_date
     * @param {null|int} id
     *
     * @returns {boolean}
     */
    var addComment = function (type, label, comment_text, comment_date, force_id) {
        // le champ de texte est vide
        if (comment_text.length === 0) {
            comment_text = null;
        }
        // la date est vide
        if (comment_date.length !== 10) {
            comment_date = null;
        }

        // on empêche l'ajout si le texte et la date sont vides
        if (comment_text === null && comment_date === null) {
            return false;
        }

        // id unique pour cibler le commentaire plus tard
        var id = "comment_" + (force_id || $.now());
        // objet représentant un commentaire
        var comment = {
            domId: "#" + id,
            type: type,
            label: label,
            text: comment_text,
            date: comment_date
        };

        // on créer une nouvelle ligne du tableau
        var $tr = $('<tr/>')
            .attr('id', id)
            .attr('data-id', id)
            .attr('data-comment', JSON.stringify(comment));

        // lien + colonne du bouton d'édition
        var $a_action = $('<a/>')
            .attr('href', "#" + id)
            .addClass('icon-tk-edit-single');

        $a_action.on('click', function (e) {
            e.preventDefault();

            var comment_id = $(this).attr('href');
            var $comment = $(comment_id).data('comment');

            $modal_commentEdit.data('comment', $comment);
            $modal_commentEdit.dialog('open');
        });

        // nouvelle colonne avec le lien pour modifier le commentaire
        var $td_action = $('<td/>').html($a_action);

        // colonne label + commentaire + date
        var $td_label = $('<td/>').text(comment.label).css('text-align', "center");
        var $td_text = $('<td/>').text(comment.text || "N/A");
        var $td_date = $('<td/>').text(comment.date || "N/A").css('text-align', "center");

        // ajout des colonnes à la ligne
        $tr.append($td_action, $td_label, $td_text, $td_date);

        if ($('tr', $commentsTable).length === 1) {
            $('tr:first-child', $commentsTable).hide();
        }

        // ajout de la ligne au tableau
        $commentsTable.append($tr);

        return true;
    };

    var removeComment = function ($comment, prompt) {
        if (prompt === undefined) {
            prompt = true;
        }

        var message = 'Supprimer le suivi: "' + $comment.label + '" ?';
        var confirmed = (prompt) ? confirm(message) : true;

        if (confirmed) {
            $($comment.domId).remove();

            if ($('tr', $commentsTable).length === 1) {
                $('tr:first-child', $commentsTable).show(150);
            }
        }

        return confirmed;
    };

    // Datepicker sur tous les input .datepicker
    $datepickers.datepicker();

    /******************************************
     * Gestion de l'ajout des commentaires.   *
     ******************************************/

        // Modal pour ajouter un suivi.
        $modal_commentAdd.dialog({
            width: 520,
            autoOpen: false,
            open: function () {
                $('input[type=text], textarea', $form_commentAdd).val(null);
            },
            buttons: [
                {
                    text: "Annuler",
                    type: "button",
                    class: "button2",
                    click: function () {
                        $(this).dialog('close');
                    }
                },
                {
                    text: "Ajouter un suivi",
                    type: "submit",
                    class: "button1",
                    click: function () {
                        $form_commentAdd.submit();
                    }
                }
            ]
        });

        $form_commentAdd.on('submit', function (e) {
            e.preventDefault();

            var comments_types = $('#comments_types', $(this));
            var comment_type = comments_types.val();
            var comment_label = $('option:selected', comments_types).text();
            var comment_text = $('#comment_text', $(this)).val();
            var comment_date = $('#comment_date', $(this)).val();

            var added = addComment(comment_type, comment_label, comment_text, comment_date);

            if (added) {
                $modal_commentAdd.dialog('close');
            }
        });

        $href_commentAdd.on('click', function (e) {
            e.preventDefault();
            $modal_commentAdd.dialog('open');
        });

    /***************************************************
     * Gestion de la modifications des commentaires.   *
     ***************************************************/

        $modal_commentEdit.dialog({
            width: 520,
            autoOpen: false,
            open: function () {
                var $comment = $modal_commentEdit.data('comment');

                $modal_commentEdit.dialog('option', 'title', 'Modification du suivi "' + $comment.label + '"');

                $('#edit_comment_type', $form_commentEdit).val($comment.label);
                $('#edit_comment_text', $form_commentEdit).val($comment.text);
                $('#edit_comment_date', $form_commentEdit).val($comment.date);
            },
            buttons: [
                {
                    text: "Supprimer",
                    type: "button",
                    class: "button2 delete",
                    click: function () {
                        var $comment = $(this).data('comment');

                        if (removeComment($comment)) {
                            $(this).dialog('close');
                        }
                    }
                },
                {
                    text: "Annuler",
                    type: "button",
                    class: "button2",
                    click: function () {
                        $(this).dialog('close');
                    }
                },
                {
                    text: "Modifier ce suivi",
                    type: "submit",
                    class: "button1",
                    click: function () {
                        $form_commentEdit.submit();
                    }
                }
            ]
        });

        $form_commentEdit.on('submit', function (e) {
            e.preventDefault();

            var $comment = $modal_commentEdit.data('comment');
            var comment_type = $comment.type;
            var comment_label = $comment.label;
            var comment_text = $('#edit_comment_text', $(this)).val();
            var comment_date = $('#edit_comment_date', $(this)).val();

            var added = addComment(comment_type, comment_label, comment_text, comment_date);

            if (added) {
                $($comment.domId).remove();
                $modal_commentEdit.dialog('close');
            }
        });

    /*********************************************
     * Gestion d'ajout d'un traitement commande. *
     *********************************************/

        $modal_suppylingAdd.dialog({
            width: 'auto',
            autoOpen: false,
            buttons: [
                {
                    text: "Annuler",
                    type: "button",
                    class: "button2",
                    click: function () {
                        $(this).dialog('close');
                    }
                },
                {
                    text: "Prélever les pièces",
                    type: "submit",
                    class: "button1",
                    click: function (e) {
                        $form_suppylingAdd.submit();
                    }
                }
            ],
            open: function () {
                var $event = $(this).data('event');
                var $start = $(this).data('start');

                if ($event !== undefined) {
                    // on a cliqué sur une dispo du calendrier
                    $(this).dialog('option', 'title', 'Prélèvement sur "' + $event.data.ilot.name + '" le ' + $start.format('ddd DD/MM'));

                    $('#ilots').prop('disabled', true);
                    $('option[value=' + $event.data.ilot.id + ']', '#ilots').prop('selected', true);

                    /*if ($event.data.quantity > 1) {
                        $('#add_quantity', $(this)).attr('max', $event.data.quantity + 10);
                    }*/
                } else {
                    // on a cliqué sur un jour du calendrier
                    $(this).dialog('option', 'title', 'Prélèvement libre le ' + $start.format('ddd DD/YY'));

                    $('#ilots').prop('disabled', false);
                    $('#choose_ilot', '#ilots').prop('selected', true);
                    $('#add_quantity', $(this)).attr('max', 999);
                }
            }
        });

        $form_suppylingAdd.on('submit', function (e) {
            e.preventDefault();

            var $ilot = $('option:selected', '#ilots').data('ilot');

            if ($ilot === undefined) {
                return false;
            }

            var $start = $modal_suppylingAdd.data('start');
            var quantity = parseInt($('#add_quantity', $(this)).val());

            if (isNaN(quantity)) {
                return false;
            } else if (quantity < 1) {
                addMessage("La quantité prélevée doit être de 1 pièce minimum", 'warning');
                return false;
            }

            var id = $.now();
            var depot_id = $('option:selected', '#depots').val();
            var depot_name = $('option:selected', '#depots').text();
            var title = $ilot.name + ": " + quantity + " pièces";
            var $supplyingEvent = {
                id: id,
                title: title,
                tooltip: "Modifier le prélèvement",
                start: $start,
                allDay: true,
                data: {
                    quantity: quantity,
                    depot_id: depot_id,
                    ilot: $ilot
                },
                color: "#00A0F5",
                textColor: "#FFFFFF",
                className: 'event-supplying',
                eventType: 'supplying'
            };

            $supplying_events.events.push($supplyingEvent);

            $fullcalendar_supplying.fullCalendar('removeEventSource', 'supplying');
            $fullcalendar_supplying.fullCalendar('addEventSource', $supplying_events);
            $fullcalendar_supplying.fullCalendar('refetchEventSources', 'supplying');


            let count = parseInt($("#count_supplying_"+$ilot.id).attr('data-count'));
            count = count + quantity;
            $("#count_supplying_"+$ilot.id)
                .text(count)
                .attr('data-count', count);
            $("#count_supplying_bloc"+$ilot.id).css( "display", "" );
            var depart = $('span', '#dateDepartCommande').data('depart');

            if (depart === "") {
                $('span', '#dateDepartCommande').data('depart', $start);
                $('span', '#dateDepartCommande').text($start.format('DD/MM/YYYY'));
            } else if ($start > depart) {
                $('span', '#dateDepartCommande').data('depart', $start);
                $('span', '#dateDepartCommande').text($start.format('DD/MM/YYYY'));
            }


            addComment('approvisionnement', "Approvisionnement", quantity + " pièces depuis le dépôt " + depot_name, $start.format('DD/MM/YYYY'), $supplyingEvent.id);

            $modal_suppylingAdd.dialog('close');
        });

        $href_supplyingAdd.on('click', function (e) {
            e.preventDefault();
            $modal_suppylingAdd.dialog('open');
        });

        $fullcalendar_supplying.fullCalendar({
            defaultView: 'basicWeek',
            locale: 'fr',
            weekends: false,
            editable: true,
            startEditable: false,
            durationEditable: false,
            aspectRatio: 1.8,
            contentHeight: 150,
            eventOrder: "-priority",
            eventSources: [
           $supplying_events,
                 {
                     id: 'charge',
                     url: $fullcalendar_supplying.data('events')
                 },

             ],
            customButtons: {
                add_event: {
                    text: 'Rafraichir ',
                    click: function() {
                        $fullcalendar_supplying.fullCalendar('refetchEventSources', 'charge');
                    }
                }
            }, header: {
                left: 'title',
                center: '',
                right: 'add_event'
            },
            eventRender: function ($event, $item) {
                if ($event.start === undefined) {
                    return false;
                }
                console.log($event)
                console.log($event.data.quantity)

                  $item.attr('title', $event.tooltip || $event.title);

                if ($event.eventType === 'chargeGestionnaire' || $event.eventType === 'supplying') {
                    var plural = ($event.data.quantity < -1 || $event.data.quantity > 1) ? "s" : "";
                    $item.html('<b>' + $event.data.ilot.name + '</b>: ' + $event.data.quantity + ' pièce' + plural);
                }
                if($event.data.quantity<0)
                {
                    $item.attr('style', 'background-color:red');
                }
                if ($event.eventType === 'supplying') {
                     var $html = $item.html();
                     var $icon = $('<span/>')
                         .append($html)
                         .addClass('icon-tk-download');

                     $item.html($icon);
                 } else if ($event.eventType === 'chargeGestionnaire' && $event.data.quantity < 1) {
                     $item.css('cursor', 'not-allowed');
                 }

                return true;
            },
            eventClick: function ($event, e) {
                e.preventDefault();
                console.log("mdlld 1")
                if ($event.eventType === 'chargeGestionnaire') {
                    $modal_suppylingAdd.data('event', $event);
                    $modal_suppylingAdd.data('start', $event.start);
                    $modal_suppylingAdd.dialog('open');

                    return true;
                } else if ($event.eventType === 'supplying') {
                    var message = 'Supprimer le prélèvement:\n"' + $event.title + '" du ' + $event.start.format('DD/MM/YYYY') + ' ?';

                    if (confirm(message)) {
                        var count = parseInt($supplyingCount.attr('data-count'));
                        var $comment = $('#comment_' + $event.id).data('comment');

                        count = count - $event.data.quantity;
                        $supplyingCount
                            .text(count)
                            .attr('data-count', count);

                        $fullcalendar_supplying.fullCalendar('removeEvents', $event.id);
                        if ($comment !== undefined) {
                            removeComment($comment, false);
                        }

                        for (var i = 0; i < $supplying_events.events.length; i++) {
                            var $eventFc = $supplying_events.events[i];

                            if ($eventFc.id === $event.id) {
                                $supplying_events.events.splice(i, 1);
                            }
                        }
                    }
                }

                return false;
            },
            dayRender: function (date, cell) {
                cell.css('cursor', "pointer");
                cell.attr('title', "Saisir un prélèvement");
            },
            dayClick: function ($date, e) {
                $modal_suppylingAdd.removeData('event');
                $modal_suppylingAdd.data('start', $date);
                $modal_suppylingAdd.dialog('open');
            }
        }) ;

       /* setInterval(function () {
            addMessage("Mise à jour des disponibilités", 'success');
            $fullcalendar_supplying.fullCalendar('refetchEventSources', 'charge');
        }, 20000);*/

    $('input', $form_reserve).on('blur', function () {
        $(this).removeClass('error');
    });

    /*$('#bill_id', $form_reserve).on('keyup', function () {
        if ($(this).val().length > 2) {
            $('#form_submit').attr('disabled', false);
        } else {
            $('#form_submit').attr('disabled', true);
        }
    });*/

    $form_reserve.on('submit', function (e) {
        e.preventDefault();

        var data = {
            comments: {},
            supplies: []
        };
        var $comments = $('tr[data-comment]', $commentsTable);
        var $supplies = $supplying_events;

        data = $.extend(data, getFormData($(this)));

        // compactage des commentaires pour les envoyer ...
        if ($comments.length >= 1) {
            for (var i = 0; i < $comments.length; i++) {
                var $comment = $('#' + $comments[i].id).data('comment');
                var comment_data = {
                    text: $comment.text,
                    date: $comment.date
                };

                if (data.comments[$comment.type] === undefined) {
                    data.comments[$comment.type] = [];
                }

                data.comments[$comment.type].push(comment_data);
            }
        }

        // compactage des prélèvements pour les envoyer ...
        if ($supplies.events.length >= 1) {
            for (var j = 0; j < $supplies.events.length; j++) {
                var $supply = $supplies.events[j];

                if ($supply !== undefined) {
                    var $supply_data = {
                        quantity: $supply.data.quantity,
                        from_depot: $supply.data.depot_id,
                        to_ilot: $supply.data.ilot.id,
                        processing_at: $supply.start.format('YYYY-MM-DD')
                    };

                    data.supplies.push($supply_data);
                }
            }
        } else {
            addMessage("Vous devez saisir au moins un prélèvement dans le planning ci-dessous", 'warning');
            return false;
        }

        $.ajax({
            url: $(this).attr('action'),
            method: 'PUT',
            data: data,
            beforeSend: function () {
                console.log(data);
            },
            error: function ($xhr) {
                if ($xhr.status === 400) {
                    addMessage('Veuillez vérifier les informations saisies avant de valider la commande', 'warning');
                } else if ($xhr.status === 409) {
                    $('#bill_id').addClass('error');
                    addMessage('Le numéro de devis saisie est déjà attribué à une commande');
                }
            },
            success: function (res, textStatus, $xhr) {
                if ($xhr.status === 201) {
                    addMessage("Votre commande a bien été saisie !", 'success');
                    $modal_success.dialog('open');
                }
            }
        });
    });

    $modal_success.dialog({
        width: 'auto',
        autoOpen: false,
        close: function () {
            window.location.replace($(this).data('location'));
        },
        buttons: [
            {
                type: "button",
                text: "Retour aux gestionnaires",
                class: "button1",
                click: function () {
                    $(this).dialog('close');
                }
            }
        ]
    });

    $('#machine_ts').on('keyup', function () {
        if ($(this).val() !== "") {
            $('#depart_ts').prop('disabled', false);
        } else {
            $('#depart_ts').prop('disabled', true);
        }
    })
    $('#form_submit').attr('disabled', false);

    $("#choisirSemaine").on('change', function () {
        $('#gst-reserve_fullcalendar').fullCalendar('gotoDate', $(this).val().split("/").reverse().join("-"));
    })
})(jQuery);
