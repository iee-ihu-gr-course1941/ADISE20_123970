var clock = {
    tick : null, // the interval id
    speed: 3000  // 3 seconds
};

function startGameTick(action) {
    window.clock.tick = setInterval(function() {
        console.log('clock is ticking');

        if (action) {
            action();
        }
    }, window.clock.speed);
}

function stopGameTick() {
    console.log('stopping clock');

    clearInterval(window.clock.tick);
}
