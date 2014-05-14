$(document).ready(function () {
    initialize();

    bindEvents();
});

function initialize() {
    updateFuelType();
}

function bindEvents() {
    $('#calculation_fuel_type').change(function() {
        updateFuelType();
    });
}

function updateFuelType() {
    $('#calculation_stove_power').parents('.control-group').show();
    $('#calculation_fuel_consumption').next().text('t');
    $('label[for="calculation_fuel_consumption"]').text('Zużycie opału ostatniej zimy');
    $('label[for="calculation_fuel_cost"]').text('Koszt zużytego opału');

    var newVal = $('#calculation_fuel_type').val();

    $('#calculation_stove_type').parents('.control-group').toggle(newVal != '' && newVal != 'electricity' && newVal.indexOf("gas") == -1);
    $('#calculation_stove_power').parents('.control-group').toggle(newVal != '');
    $('#calculation_fuel_consumption').parents('.control-group').toggle(newVal != '');
    $('#calculation_fuel_cost').parents('.control-group').toggle(newVal != '');

    if (newVal.indexOf("gas") !== -1) {
        $('label[for="calculation_fuel_consumption"]').text('Zużycie gazu ostatniej zimy');
        $('label[for="calculation_fuel_cost"]').text('Koszt zużytego gazu');
        $('#calculation_fuel_consumption').next().text('m3');
    }

    if (newVal == 'wood') {
        $('#calculation_fuel_consumption').next().text('mp');
    }

    if (newVal == 'electricity') {
        $('#calculation_fuel_consumption').next().text('kWh');
        $('label[for="calculation_fuel_consumption"]').text('Zużycie energii ostatniej zimy');
        $('label[for="calculation_fuel_cost"]').text('Koszt zużytej energii');
        $('#calculation_stove_power').parents('.control-group').hide();
    }
}