$(document).ready(function () {
    initialize();

    bindEvents();
});

function initialize() {
    updateAreaStuff();
    updateWallStuff();
    updateFloorsStuff();
    updateCeilingIsolation();
    updateFloorIsolation();
    
    $('#calculation_walls_0_construction_layer_material').parent().prev().text('Główny materiał ściany');
    $('#calculation_walls_0_construction_layer_size').parent().parent().prev().text('Grubość ściany');
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
    
    $('#calculation_area').change(function() {
        updateAreaStuff();
    });
    
    $('#calculation_number_heated_floors').change(function() {
        updateAreaStuff();
    });
    
    $('#calculation_walls_0_construction_layer_size').change(function() {
        updateAreaStuff();
    });
}

function updateAreaStuff() {
    var numberFloors = $('#calculation_number_heated_floors').val();
    $('#calculation_number_floors').val(numberFloors);
    
    var area = $('#calculation_area').val();
    var wallSize = $('#calculation_walls_0_construction_layer_size').val();
    
    if (area == 0) {
        var length = parseFloat($('#calculation_building_length').val());
        length -= 2*(wallSize/100);
        
        $('#calculation_area').val(Math.ceil(length*length*1.1));
        
        return;
    }
    
    var size = Math.sqrt(area) * 1.03;
    
    if (numberFloors > 1) {
        size = Math.round(size / numberFloors);
    }
    
    size += 2*(wallSize/100);
    size = size.toFixed(2);
    
    $('#calculation_building_length').val(size);
    $('#calculation_building_width').val(size);
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