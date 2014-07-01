$(document).ready(function () {
    initialize();

    bindEvents();
});

function initialize() {
    updateWallStuff();
    updateFloorsStuff();
    updateCeilingIsolation();
    updateFloorIsolation();
}

function bindEvents() {
    $('#calculation_apartment_whats_over').change(function() {
        updateCeilingIsolation();
    });

    $('#calculation_apartment_whats_under').change(function() {
        updateFloorIsolation();
    });
    $('#calculation_walls_0_has_another_layer').change(function() {
        updateWallStuff();
    });
    $('#calculation_walls_0_has_isolation_inside').change(function() {
        updateWallStuff();
    });
    $('#calculation_walls_0_has_isolation_outside').change(function() {
        updateWallStuff();
    });
}

function updateFloorsStuff() {
    var heatedFloorsCount = $('#calculation_number_heated_floors').val();
    if (heatedFloorsCount == 0) {
        return;
    }
    $('#whats_unheated').toggle($('#calculation_number_floors').val()-heatedFloorsCount == 1);
}

function updateWallStuff() {
    $('#wall_outside_layer').toggle($('#calculation_walls_0_has_another_layer').is(':checked'));
    $('#wall_isolation_layer').toggle($('#calculation_walls_0_has_isolation_inside').is(':checked'));
    $('#wall_extra_isolation_layer').toggle($('#calculation_walls_0_has_isolation_outside').is(':checked'));
}

function updateCeilingIsolation() {
    var newVal = $('#calculation_apartment_whats_over').val();

    $('#ceiling_isolation_layer').toggle(newVal != 'heated_room');
}

function updateFloorIsolation() {
    var newVal = $('#calculation_apartment_whats_under').val();

    $('#floor_isolation_layer').toggle(newVal != 'heated_room');
}