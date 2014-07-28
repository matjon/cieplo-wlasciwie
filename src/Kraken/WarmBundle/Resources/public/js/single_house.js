$(document).ready(function () {
    initialize();

    bindEvents();
});

function initialize() {
    updateConstructionType();
    updateWallStuff();
    updateFloorsStuff();
    updateRoofType();
    updateBasementThings();
    analyzeWallSize();
}

function bindEvents() {
    $('#calculation_roof_type').change(function() {
        updateRoofType();
    });

    $('#calculation_has_basement').change(function () {
        updateBasementThings();
    });

    $('#calculation_is_basement_heated').change(function () {
        updateBasementThings();
    });

    $('#calculation_is_ground_floor_heated').change(function () {
        updateBasementThings();
    });

    $('#calculation_number_floors').change(function () {
        updateFloorsStuff();
    });

    $('#calculation_number_heated_floors').change(function () {
        updateFloorsStuff();
    });
    
    $('#calculation_construction_type').change(function() {
        updateConstructionType();
    });

    $('#calculation_walls_0_construction_layer_size').change(function () {
        analyzeWallSize();
    });

    $('#calculation_walls_0_isolation_layer_size').change(function () {
        analyzeWallSize();
    });

    $('#calculation_walls_0_outside_layer_size').change(function () {
        analyzeWallSize();
    });

    $('#calculation_walls_0_extra_isolation_layer_size').change(function () {
        analyzeWallSize();
    });
}

function analyzeWallSize()
{
    var wallSize = makeInteger($('#calculation_walls_0_construction_layer_size').val())
        + makeInteger($('#calculation_walls_0_isolation_layer_size').val())
        + makeInteger($('#calculation_walls_0_outside_layer_size').val())
        + makeInteger($('#calculation_walls_0_extra_isolation_layer_size').val());

    $('#wall_may_be_too_thin').toggle(wallSize < 20 && $('#calculation_construction_type').val() == 'traditional');
    $('#wall_may_be_too_thin span').text(wallSize);
    $('#wall_may_have_isolation').toggle(wallSize > 30 && $('#calculation_walls_0_isolation_layer_size').val() == 0);
}

function makeInteger(text) {
    var val = parseInt(text);

    return isNaN(val) ? 0 : val;
}

function updateFloorsStuff() {
    var heatedFloorsCount = $('#calculation_number_heated_floors').val();
    if (heatedFloorsCount == 0) {
        return;
    }
    $('#whats_unheated').toggle($('#calculation_number_floors').val()-heatedFloorsCount == 1);
}

function updateWallStuff() {
    $('#calculation_walls_0_has_another_layer').change(function() {
        $('#wall_outside_layer').toggle($(this).is(':checked'));
    });
    $('#calculation_walls_0_has_isolation_inside').change(function() {
        $('#wall_isolation_layer').toggle($(this).is(':checked'));
    });
    $('#calculation_walls_0_has_isolation_outside').change(function() {
        $('#wall_extra_isolation_layer').toggle($(this).is(':checked'));
    });
}

function updateRoofType() {
    var newVal = $('#calculation_roof_type').val();

    $('#calculation_is_attic_heated').parents('.control-group').toggle(newVal != 'flat');
    $('#roof_isolation_layer').toggle(newVal != 'flat');
    if (newVal == 'flat') {
        $('#calculation_highest_ceiling_isolation_layer').parent().prev().text('Izolacja dachu');
    } else {
        $('#calculation_highest_ceiling_isolation_layer').parent().prev().text('Izolacja najwyższego stropu');
    }
}

function updateConstructionType() {
    var newVal = $('#calculation_construction_type').val();

    var isCanadian = newVal == 'canadian';
    
    $('#wall_isolation_layer').toggle(isCanadian);
    $('#calculation_walls_0_has_isolation_inside').parent().parent().toggle(!isCanadian);
    $('#calculation_walls_0_has_another_layer').parent().parent().toggle(!isCanadian);
    $('#calculation_walls_0_has_isolation_outside').parent().parent().toggle(!isCanadian);
    $('#calculation_walls_0_construction_layer_material').parent().parent().toggle(!isCanadian);
    $('#calculation_walls_0_construction_layer_size').parent().parent().parent().toggle(!isCanadian);

    if (newVal == 'traditional') {
        $('#calculation_walls_0_construction_layer_material').parent().prev().text('Główny materiał ściany');
        $('#calculation_walls_0_construction_layer_size').parent().parent().prev().text('Grubość ściany');
        
        $('#calculation_walls_0_isolation_layer_material').parent().prev().text('Materiał');
        $('#calculation_walls_0_isolation_layer_size').parent().parent().prev().text('Grubość');
    } else {
        var additionalMaterial = 'Drewno liściaste';
        $('#calculation_walls_0_construction_layer_material option:contains(' + additionalMaterial + ')').prop({selected: true});
        $('#calculation_walls_0_construction_layer_size').val(6);
        
      
        $('#calculation_walls_0_construction_layer_material').parent().prev().text('Materiał');
        $('#calculation_walls_0_construction_layer_size').parent().parent().prev().text('Grubość');
        
        $('#calculation_walls_0_isolation_layer_material').parent().prev().text('Materiał izolacyjny ścian');
        $('#calculation_walls_0_isolation_layer_size').parent().parent().prev().text('Grubość izolacji');
    }
}

function updateBasementThings() {
    var hasBasement = $('#calculation_has_basement').is(':checked');
    var basementIsHeated = $('#calculation_is_basement_heated').is(':checked');
    var groundFloorIsHeated = $('#calculation_is_ground_floor_heated').is(':checked');

    $('#calculation_is_basement_heated').parents('.control-group').toggle(hasBasement);
    $('#basement_floor_isolation_layer').toggle(hasBasement);
    $('#lowest_ceiling_isolation_layer').toggle(!groundFloorIsHeated);
}