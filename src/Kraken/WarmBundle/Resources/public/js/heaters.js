var app = angular.module('warm', []).config(function($interpolateProvider){
        $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
    }
);

var FLOAT_REGEXP = /^\-?\d+((\.|\,)\d+)?$/;
app.directive('smartFloat', function() {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      ctrl.$parsers.unshift(function(viewValue) {
        if (FLOAT_REGEXP.test(viewValue)) {
          ctrl.$setValidity('float', true);
          return parseFloat(viewValue.replace(',', '.'));
        } else {
          ctrl.$setValidity('float', false);
          return undefined;
        }
      });
    }
  };
});

app.controller('WarmCtrl', function($scope) {
    $scope.outdoor_temperature = outdoorTemperature;
    $scope.floor_height = floorHeight;
    $scope.standard_window_area = standardWindowArea;
    $scope.standard_door_area = standardDoorArea;
    $scope.floors = buildingFloors;

    $scope.getFirstHeatedFloor = function()
    {
        for (i = 0; i < $scope.floors.length; i++) {
            if ($scope.floors[i].heated == true) {
                return $scope.floors[i].name;
            }
        }
    }

    $scope.room_type = "standard";
    $scope.room_temperature = 20;
    $scope.room_floor = $scope.getFirstHeatedFloor();
    $scope.room_width = Math.round(buildingWidth / 2);
    $scope.room_length = Math.round(buildingLength / 2);
    $scope.room_external_walls = "long";
    $scope.room_unheated_walls = 0;
    $scope.room_windows = 1;
    $scope.room_doors = 0;

    $scope.power = 0;
    $scope.heater_not_required = false;
    
    $scope.realFloors = function()
    {
        var floors = [];
        
        $scope.floors.forEach(function(floor) {
              if (floor.name != 'other') {
                  floors.push(floor);
              }
        });
        
        return floors;
    }

    $scope.getWindowsArea = function()
    {
        if ($scope.room_external_walls == 0) {
            return 0;
        }
        
        var windowsArea = $scope.room_windows * $scope.standard_window_area;

        if ($scope.room_has_balcony_door) {
            windowsArea += 0.8 * $scope.standard_window_area;
        }

        return Math.max(0, windowsArea);
    }

    $scope.getDoorsArea = function()
    {
        if ($scope.room_external_walls == 0) {
            return 0;
        }
        
        return Math.max(0, $scope.room_doors * $scope.standard_door_area);
    }

    $scope.getExternalWallArea = function()
    {
        var externalWallLength = 0;
        
        if ($scope.room_external_walls == "short") {
            externalWallLength = Math.min($scope.room_width, $scope.room_length);
        } else if ($scope.room_external_walls == "long") {
            externalWallLength = Math.max($scope.room_width, $scope.room_length);
        } else if ($scope.room_external_walls == 2) {
            externalWallLength = $scope.room_width + $scope.room_length;
        } else if ($scope.room_external_walls == 3) {
            externalWallLength = 2 * $scope.room_width + $scope.room_length;
        } else if ($scope.room_external_walls == 4) {
            externalWallLength = 2 * $scope.room_width + 2 * $scope.room_length;
        }
        
        return Math.max(0, $scope.floor_height * externalWallLength - $scope.getWindowsArea() - $scope.getDoorsArea());
    }

    $scope.getUnheatedWallArea = function()
    {
        var unheatedWallLength = 0;

        if ($scope.room_unheated_walls == "short") {
            unheatedWallLength = Math.min($scope.room_width, $scope.room_length);
        } else if ($scope.room_unheated_walls == "long") {
            unheatedWallLength = Math.max($scope.room_width, $scope.room_length);
        } else if ($scope.room_unheated_walls == 2) {
            unheatedWallLength = $scope.room_width + $scope.room_length;
        } else if ($scope.room_unheated_walls == 3) {
            unheatedWallLength = 2 * $scope.room_width + $scope.room_length;
        } else if ($scope.room_unheated_walls == 4) {
            unheatedWallLength = 2 * $scope.room_width + 2 * $scope.room_length;
        }

        return $scope.floor_height * unheatedWallLength;
    }

    $scope.setDefaultIndoorTemperature = function()
    {
        if ($scope.room_type == "bathroom") {
            $scope.room_temperature = 24;
        } else if ($scope.room_type == "workshop") {
            $scope.room_temperature = 16;
        } else if ($scope.room_type == "garage") {
            $scope.room_temperature = 12;
        } else {
            $scope.room_temperature = 20;
        }
    }

    $scope.getBelowFloorName = function()
    {
        for (i = 0; i < $scope.floors.length; i++) {
            if ($scope.floors[i].name == $scope.room_floor) {
                return i > 0 ? $scope.floors[i-1].name : false;
            }
        }

        return false;
    }

    $scope.isBelowFloorHeated = function()
    {
        for (i = 0; i < $scope.floors.length; i++) {
            if ($scope.floors[i].name == $scope.room_floor) {
                return i > 0 ? $scope.floors[i-1].heated : false;
            }
        }

        return false;
    }

    $scope.getAboveFloorName = function()
    {
        for (i = 0; i < $scope.floors.length; i++) {
            if ($scope.floors[i].name == $scope.room_floor) {
                return i < $scope.floors.length-1 ? $scope.floors[i+1].name : false;
            }
        }

        return false;
    }

    $scope.isAboveFloorHeated = function()
    {
        for (i = 0; i < $scope.floors.length; i++) {
            if ($scope.floors[i].name == $scope.room_floor) {
                return i < $scope.floors.length-1 ? $scope.floors[i+1].heated : false;
            }
        }

        return false;
    }

    $scope.getCeilingArea = function()
    {
        // close enough
        return $scope.room_length * $scope.room_width;
    }
    
    $scope.getCeilingHeatLoss = function()
    {
        var aboveFloorName = $scope.getAboveFloorName();
        var isAboveFloorHeated = $scope.isAboveFloorHeated();
        
        if (aboveFloorName == false) {
            return $scope.getCeilingArea() * buildingRoofConductance * ($scope.room_temperature - $scope.outdoor_temperature);
        }
        
        if (aboveFloorName == 'attic' && !isAboveFloorHeated) {
            return 0.5 * $scope.getCeilingArea() * buildingHighestCeilingConductance * ($scope.room_temperature - $scope.outdoor_temperature);
        }

        if (!isAboveFloorHeated && aboveFloorName != false) {
            return 0.5 * $scope.getCeilingArea() * buildingInternalCeilingConductance * ($scope.room_temperature - $scope.outdoor_temperature);
        }
        
        return 0;
    }

    $scope.getFloorHeatLoss = function()
    {
        var belowFloorName = $scope.getBelowFloorName();
        var isBelowFloorHeated = $scope.isBelowFloorHeated();
        
        if (belowFloorName == false) {
            return $scope.getCeilingArea() * buildingGroundFloorConductance * ($scope.room_temperature - $scope.outdoor_temperature);
        }
        
        if ($scope.room_floor == "basement") {
            return $scope.getCeilingArea() * buildingUndergroundConductance * ($scope.room_temperature - $scope.outdoor_temperature);
        }

        if (!isBelowFloorHeated && belowFloorName != false) {
            return 0.5 * $scope.getCeilingArea() * buildingInternalCeilingConductance  * ($scope.room_temperature - $scope.outdoor_temperature);
        }

        return 0;
    }
    
    $scope.getVentilationEnergyLoss = function()
    {
        var roomCubature = $scope.room_width * $scope.room_length * $scope.floor_height;
        
        var fraction = roomCubature/buildingCubature;
        
        if ($scope.room_doors > 0 || $scope.room_windows > 0) {
            fraction *= 1.025;
        } else {
            fraction -= 0.025;
        }
        
        return fraction * buildingVentilationEnergyLossFactor * ($scope.room_temperature - $scope.outdoor_temperature);
    }
    
    $scope.calculatePower = function()
    {
        var temperatureDiff = $scope.room_temperature - $scope.outdoor_temperature;
        var power = 0;
        
        if ($scope.room_floor != 'basement') {
            power = $scope.getExternalWallArea() * buildingExternalWallConductance * temperatureDiff;

            if ($scope.room_has_balcony_door == true) {
                power *= 1.15;
            }
        }
        
        power += $scope.getVentilationEnergyLoss();
        power += $scope.getWindowsArea() * buildingWindowsConductance * temperatureDiff;
        power += $scope.getDoorsArea() * buildingDoorsConductance * temperatureDiff;
        power += $scope.getUnheatedWallArea() * buildingInternalWallConductance * temperatureDiff * 0.5;
        power += $scope.getCeilingHeatLoss();
        power += $scope.getFloorHeatLoss();
 
        power *= 1.05;
        
        console.log("ceiling: " +  $scope.getCeilingHeatLoss());
        console.log("floor: " +  $scope.getFloorHeatLoss());
        console.log("ventilation: " +  $scope.getVentilationEnergyLoss());
        console.log("unheated: " +  ($scope.getUnheatedWallArea() * buildingInternalWallConductance * temperatureDiff * 0.5));
        console.log("outdoor: " +  ($scope.getExternalWallArea() * buildingExternalWallConductance * temperatureDiff));
        
        if (power > 0) {
            $scope.power = 50 * Math.ceil(Math.round(power) / 50);
        }
        
        $scope.heater_not_required = $scope.power > 0 && $scope.power <= 200;

        return $scope.power;
    }
});