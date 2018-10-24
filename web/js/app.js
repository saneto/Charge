function addMessage(message, type, duration) {
    if (type === undefined) {
        type  = 'danger';
    }
    if (duration === undefined) {
        duration = 5000;
    }

    // création de la notification
    var $notification = $('<div/>').hide().html(message);

    $notification.addClass('alert alert-' + type).slideDown(150, function () {
        var $self = $(this);
        setTimeout(function () {
            $self.slideUp(150, function () { $(this).remove() });
        }, duration);
    });

    // ajout de la notification dans la liste
    $('#toasts').append($notification);
    // si on a plus de 3 notifs, on supprime la plus ancienne (la première)
    if($('#toasts').children().length > 3) {
        $('#toasts div:not(".close")').first().addClass('close').slideUp(150, function () { $(this).remove() });
    }
};

function addModalMessage($form, key, message, type) {
    if (type === undefined) {
        type = 'danger';
    }

    var id = "error_" + key;
    var $old = $("#" + id);
    var $message = $('<div/>')
        .attr('id', id)
        .text(message)
        .addClass('alert alert-small alert-' + type);

    if($old.length) {
        $old.remove();
    }

    $form.append($message);
}

var getFormData = function ($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
};

var addSimpleComment = function ($destination, type, label, author, comment_text, comment_date, force_id) {
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
        author: author,
        label: label,
        text: comment_text,
        date: comment_date
    };

    // on créer une nouvelle ligne du tableau
    var $tr = $('<tr/>')
        .attr('id', id)
        .attr('data-id', id)
        .attr('data-comment', JSON.stringify(comment));

    // colonne label + commentaire + date
    var $td_label = $('<td/>').text(comment.label).css('text-align', "center");
    var $td_author = $('<td/>').text(comment.author).css('text-align', "center");
    var $td_text = $('<td/>').text(comment.text || "N/A");
    var $td_date = $('<td/>').text(comment.date || "N/A").css('text-align', "center");

    // ajout des colonnes à la ligne
    $tr.append($td_label, $td_author, $td_text, $td_date);

    if ($('tr', $destination).length === 1) {
        $('tr:first-child', $destination).hide();
    }

    // ajout de la ligne au tableau
    $destination.append($tr);

    return true;
};

(function($) {

/*  var fastFlash = sessionStorage.getItem('fastFlash');
    var $momentsDates = $('span[data-moment]');

    $.datepicker.setDefaults({
        showAnim: "",
        minDate: "-2M",
        maxDate: "+2M",
        dateFormat: "dd/mm/yy",
        showOtherMonths: true,
        selectOtherMonths: true,
        beforeShowDay: $.datepicker.noWeekends
    });

    $.extend($.ui.dialog.prototype.options, {
        modal: true,
        autoOpen: false,
        draggable: false,
        resizable: false
    });
    
    if (fastFlash !== null) {
        fastFlash = JSON.parse(fastFlash);

        addMessage(fastFlash.message, fastFlash.type);
        sessionStorage.removeItem('fastFlash');
    }*/

})(jQuery);
