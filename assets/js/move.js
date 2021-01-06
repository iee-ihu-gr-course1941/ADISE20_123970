var move  = {};

function selectPiece(piece) {
    var piece_id = $(piece).attr('id');
    var position = $(piece).parent().data('pos');
    var my_color = (window.me.username == window.game.player_1) ? 'mavro' : 'aspro';

    $('.pouli.selected').removeClass('selected');
    $('.thesi.possible').removeClass('possible');
    $('.mazema_' + my_color).removeClass('possible');

    if (!$(piece).hasClass(my_color)) {
        return;
    }

    $(piece).addClass('selected');

    // highlight possible moves
    new_pos_1 = getPossibleMove(position, window.board.die_1);
    new_pos_2 = getPossibleMove(position, window.board.die_2);

    if (new_pos_1 > 0) {
        $('.thesi' + new_pos_1).addClass('possible');
    } else if (new_pos_1 == 0) {
        $('.mavema_' + my_color).addClass('possible');
    }

    if (new_pos_2 > 0) {
        $('.thesi' + new_pos_2).addClass('possible');
    } else if (new_pos_2 == 0) {
        $('.mavema_' + my_color).addClass('possible');
    }

    window.move = {
        piece: piece_id,
        to   : null
    };
}

function getPossibleMove(position, offset) {
    var rival_color  = (window.me.username != window.game.player_1) ? 'mavro' : 'aspro';
    var my_direction = (window.me.username != window.game.player_1) ? '+' : '-';
    
    if (my_direction == '+') {
        var pos = parseInt(position) + parseInt(offset);
    } else {
        var pos = parseInt(position) - parseInt(offset);
    }

    // mazema
    if (pos > 24 || pos < 1) {
        return 0;
    }

    // not possible
    if ($('.thesi' + pos + ' > ' + rival_color).length > 1) {
        return -1;
    }

    return pos;
}
