function rollDice(token) {
    console.log('rolling dice');

    $('#rollDice').addClass('d-none');
    $('#dice').removeClass('d-none');
    $('#diceLoading').removeClass('d-none');
    $('#die1').addClass('d-none');
    $('#die2').addClass('d-none');

    // TODO: AJAX
}

function updateDice(die1, die2) {
    $('#diceLoading').addClass('d-none');

    if (die1) {
        setDie(1, die1);
        $('#die1').removeClass('d-none');
    }

    if (die2) {
        setDie(2, die2);
        $('#die2').removeClass('d-none');
    }
}

function setDie(die, value) {
    $('#die' + die).removeClass('fa-dice-one');
    $('#die' + die).removeClass('fa-dice-two');
    $('#die' + die).removeClass('fa-dice-three');
    $('#die' + die).removeClass('fa-dice-four');
    $('#die' + die).removeClass('fa-dice-five');
    $('#die' + die).removeClass('fa-dice-six');

    var num = "";
    switch (value) {
        case 1:
            num = "one";
            break;
        case 2:
            num = "two";
            break;
        case 3:
            num = "three";
            break;
        case 4:
            num = "four";
            break;
        case 5:
            num = "five";
            break;
        case 6:
            num = "six";
            break;
    }

    $('#die' + die).addClass('fa-dice-' + num);
}
