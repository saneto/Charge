(function($) {

    var $modalAdd = $("#modal_pln_event-add");
    var $modalEdit = $("#modal_pln_event-edit");
    var $modal_commandeCharges = $("#modal_pln_commande-charge");

    var $formAdd  = $('#form_modal_pln_event-add');
    var $formEdit  = $('#form_modal_pln_event-edit');

    var $eventAddQuantity = $("#eventAddQuantity");
    var $eventAddQuantityAt = $("#eventAddQuantityAt");

    var $eventEditQuantity = $("#eventEditQuantity");
    var $eventEditQuantityAt = $("#eventEditQuantityAt");

    var deleteEvent = function (event) {
        var deleted = false;
        var message = "Êtes-vous sûr de vouloir supprimer les " + event.title + " du " + event.start.format('DD/MM/YYYY') + " ?";
        message = message + "\nCette action est irréversible.";

        if(confirm(message)) {
            $.ajax({
                url: event.url,
                method: 'DELETE',
                error: function () {
                    alert('Une erreur survenue lors de la suppression...');
                },
                success: function () {
                    // supprimer l'évènement qui a été selectionner en gardant si existant le détails de la charge
                    /*$('#planning_depot').fullCalendar('removeEvents', function (e) {
                        return e.id === event.id && e.eventType === 'charge';
                    });*/
                    $('#planning_depot').fullCalendar('refetchEventSources', 'charge');

                    $modalEdit.dialog('close');
                    addMessage("La disponibilité de " + event.title + " du " + event.start.format('DD/MM/YYYY') + " a bien été supprimée", 'success');
                }
            });

            return true;
        }

        return deleted;
    };

    var openDayClick = function ($date) {
        var events = $('#planning_depot').fullCalendar('clientEvents');

        if(events.length >= 1) {
            var today = $date.format("DD/MM/YYYY");

            for(var k in events) {
                var event = events[k];
                if (event.eventType === 'charge') {
                    var start = event.start.format("DD/MM/YYYY");

                    if(start === today) {
                        addMessage("Une disponibilité de " + event.title + " a déjà été saisie pour le " + start + ". Vous ne pouvez pas avoir 2 disponibilités le même jour...", 'danger', 8000);
                        return false;
                    }
                }
            }
        }

        return true;
    };

    $eventAddQuantityAt.datepicker();
    $eventEditQuantityAt.datepicker();

    $modalAdd.dialog({
        modal: true,
        minWidth: 300,
        autoOpen: false,
        draggable: false,
        closeText: "Annuler",
        open: function () {
            var $start = $modalAdd.data('start');
            $eventAddQuantityAt.val($start.format("DD/MM/YYYY"));
        },
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
                    $formAdd.submit();
                }
            }
        ]
    });

    $modalEdit.dialog({
        modal: true,
        minWidth: 300,
        autoOpen: false,
        draggable: false,
        closeText: "Fermer",
        open: function () {
            var $event = $modalEdit.data('event');
            var $start = moment($event.start).format('DD/MM/YYYY');

            $formEdit.attr('action', $event.url);
            $eventEditQuantity.val($event.data.quantity).select();
            $eventEditQuantityAt.val($start);
            $modalEdit.dialog('option', 'title', $event.data.ilot.name + " au " + $start);
        },
        buttons: [
            {
                text: "Supprimer",
                class: "button2 delete",
                click: function () {
                    // $modalEdit.dialog('close');
                    // if(deleteEvent($(this).data('event')) === false) {
                        // $modalEdit.dialog('open');
                    // }
                    deleteEvent($(this).data('event'));
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
                    $formEdit.submit();
                }
            }
        ]
    });

    // gestion du formulaire d'ajout
    $formAdd.on('submit', function (e) {
        e.preventDefault();

        var $ilot_id = $('#eventAddIlot option:selected', $formAdd);
        if($ilot_id.val() <= 0) {
            addModalMessage($formAdd, 'ilot_id', "Vous devez sélectionner un îlot", 'danger');
            return false;
        }

        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            method: 'POST',
            error: function (response) {
                var errors = response.responseJSON.errors;
                var $modal_errors = $('.modal-errors', $modalAdd);

                for (var key in errors) {
                    var error = errors[key];
                    var $input = $formAdd.find("[name=" + key + "]");

                    $input.addClass('error');
                    addModalMessage($modal_errors, key, error.message, 'danger');
                }
            },
            success: function ($event) {
                console.log('event added', $event);

                // $('#planning_depot').fullCalendar('renderEvent', $event);
                $('#planning_depot').fullCalendar('refetchEventSources', 'charge');

                $modalAdd.dialog('close');

                var start = moment($event.start).format("DD/MM/YYYY");
                addMessage("Une disponibilité de " + $event.title + " a bien été ajouté pour le " + start, 'success');
            }
        });
    });

    $formEdit.on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            method: 'POST',
            error: function (response) {
                var errors = response.responseJSON.errors;
                var $modal_errors = $('.modal-errors', $modalEdit);

                for (var key in errors) {
                    var error = errors[key];
                    var $input = $formEdit.find("[name=" + key + "]");

                    $input.addClass('error');
                    addModalMessage($modal_errors, key, error.message, 'danger');
                }
            },
            success: function ($newEvent, textStatus, $xhr) {
                // pas de modification
                if($xhr.status === 204) {
                    addMessage("Il n'y a pas eu de modification...", 'info');
                    console.log('event not modified');
                } else {
                    $('#planning_depot').fullCalendar('removeEvents', $newEvent.id);
                    // $('#planning_depot').fullCalendar('renderEvent', $newEvent);

                    var $oldStart = moment($newEvent.data.previous.start).format('DD/MM/YYYY');
                    var $newStart = moment($newEvent.start).format('DD/MM/YYYY');

                    if($newStart !== $oldStart) {
                        addMessage("Quantité du " + $oldStart + " déplacée au " + $newStart, 'success');
                    }
                    if($newEvent.title !== $newEvent.data.previous.title) {
                        addMessage("Quantité du " + $oldStart + " modifiée de " + $newEvent.data.previous.title + " à " + $newEvent.title, 'success');
                    }

                    $('#planning_depot').fullCalendar('refetchEventSources', 'charge');

                    $modalEdit.dialog('close');
                    console.log('event updated', $newEvent);
                }
            }
        });
    });

    $modal_commandeCharges.dialog({
        width: 'auto',
        autoOpen: false,
        maxHeight: 520,
        open: function () {
            var date = $(this).data('event').start.format('DD/MM/YYYY');
            $(this).dialog('option', 'title', "Détails des prélèvements du " + date);
        },
        buttons: [
            {
                text: "Fermer",
                type: "button",
                class: "button2",
                click: function () {
                    $(this).dialog('close');
                }
            }
        ]
    });

    // bouton pour ouvrir la modal d'ajout
    $('#open_modal_add-event').on('click', function (e) {
        e.preventDefault();

        $modalAdd.data('start', moment());
        $modalAdd.dialog('open');
    });

    $('#planning_depot').fullCalendar({
        defaultView: 'week',
        editable: true,
        header: {
            left: "week month, title"
        },
        views: {
            month: {
                eventLimit: 5
            },
            week: {
                type: 'basic',
                duration: { weeks: 2 },
                rows: 2
            }
        },
        locale: 'fr',
        weekends: false,
        aspectRatio: 1.8,
        eventOrder: "-priority",
        eventSources: $('#planning_depot').data('eventsSources'),
        // le planning se charge
        loading: function (isLoading) {
            if(!isLoading) {
                $('.load_planning').slideUp(150);
            } else {
                $('.load_planning').slideDown(150);
            }
        },
        eventRender: function ($event, $item) {
            if ($event.data !== undefined) {
                var $title = '<b style="color: ' + $event.textColor + ';">' + $event.data.ilot.name + '</b>: ' + $event.title;
                $item.html($title);
            }

            $item.attr('title', $event.tooltip || $event.title);
        },
        dayRender: function (date, cell) {
            cell.css('cursor', "pointer");
            cell.attr('title', "Ajouter une disponibilité");
        },
        // on veut créer une nouvelle dispo
        dayClick: function ($date) {
            // if (openDayClick($date)) {
                $modalAdd.data('start', $date);
                $modalAdd.dialog('open');
            // }
        },
        // on veut modifier une dispo
        eventClick: function ($event, jsEvent) {
            jsEvent.preventDefault();

            if ($event.editable && $event.eventType === "charge") {
                $modalEdit.data('event', $event);
                $modalEdit.dialog('open');
            } else if ($event.eventType === "chargeDetails") {
                $.ajax({
                    url: $event.url,
                    method: 'GET',
                    dataType: 'html',
                    beforeSend: function () {
                        var $layout = $('<div/>')
                            .attr('id', "ajax-layout");

                        $('body').append($layout);
                    },
                    error: function () {
                        $('#ajax-layout').remove();
                        addMessage("Impossible de récupérer le détails des prélèvements");
                    },
                    success: function (response) {
                        $('#response', $modal_commandeCharges).html(response);
                        $('#ajax-layout').remove();

                        $('#editCharge', $modal_commandeCharges).on('click', function (e) {
                            e.preventDefault();
                            var $charge = $(this).data('charge'); // on récupère la disponibilité en json
                            $charge.start = moment($charge.start); // formats des dates pour le JS
                            $charge.end = moment($charge.end);

                            $modal_commandeCharges.dialog('close'); // on ferme la modal des détails
                            $modalEdit.data('event', $charge); // on défini l'evènement pour la modification
                            $modalEdit.dialog('open'); // on affiche l'élèvement à modifier
                        });

                        $modal_commandeCharges.data('event', $event);
                        $modal_commandeCharges.dialog('open');
                    }
                });

                // alert("Détails des prélèvements");
            }
        },
        // on déplace un évènement sur le calendrier
        eventDrop: function ($event, moved_at, revertFunc) {
            if ($event.eventType === 'chargeDetails') { // on ne déplace ce type d'évènement
                revertFunc();
                return false;
            }

            $.ajax({
                url: $event.url,
                method: 'PUT',
                dataType: 'json',
                data: {
                    quantity_at: $event.start.format("DD/MM/YYYY")
                },
                error: function ($xhr) {
                    revertFunc();

                    var errors = $xhr.responseJSON.errors;
                    for (var k in errors) {
                        addMessage(errors[k].message);
                    }
                },
                success: function ($newEvent) {
                    var start = moment($newEvent.start).format("DD/MM/YYYY");
                    var previous = moment($newEvent.data.previous.start).format("DD/MM/YYYY");

                    $('#planning_depot').fullCalendar('refetchEventSources', 'charge');

                    addMessage($newEvent.data.ilot.name + ": la quantité de " + $newEvent.title + " du " + previous + " a bien été déplacée au " + start, 'success');
                    console.log('event updated (dropped)', $newEvent);
                }
            });
        }
    });

})(jQuery);