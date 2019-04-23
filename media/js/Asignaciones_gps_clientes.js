app.controller('WrapperCtrl', function($scope, $http, $timeout){    
    $scope.ajaxUrl = "./?ajax=true&mod=AsignacionGPSClientes";

    $scope.startAgain = function(){
        $scope.selectedCliente = null;
        $scope.alerts = new Array();
        $scope.selectedProfile = '';
        $http.get($scope.ajaxUrl + "&act=getData").success(function(r){
            $scope.clientes = r.clientes;
            $scope.dispositivos = r.dispositivosSinAsignar;
        });
    };

    $scope.savePermissions = function(){
        $scope.send($scope.ajaxUrl + '&act=savePermissions',{data:$scope.modData}, function(r){
            if(r.result==1){
                location.reload();
            } 
        });                    
    };

    $scope.setSelected = function(array){
        $.each(array, function($i, item){
            item.selected = false;
        })
    }

    $scope.addDevices = function(){
        $.each($scope.dispositivos, function($i, item){
            if(item.selected){
                newItem = Object.assign({}, item);
                newItem.selected = false;
                $scope.selectedCliente.dispositivos.push(newItem);
            }
        })
        $r = $scope.dispositivos.filter(function(item){
            return !item.selected
        });
        $scope.dispositivos = $r;
    }

    $scope.guardarAsociacion = function(){
        $http.post($scope.ajaxUrl + "&act=guardarAsignacion",{asignaciones:$scope.clientes}).success(function(actionResponse){
            // console.log(JSON.stringify(actionResponse,null,2));
        });
    }

    $scope.removeDevices = function(){
        $.each($scope.selectedCliente.dispositivos, function($i, item){
            if(item.selected){
                newItem = Object.assign({}, item);
                newItem.selected = false;
                $scope.dispositivos.push(newItem);
            }
        })
        $r = $scope.selectedCliente.dispositivos.filter(function(item){
            return !item.selected
        });
        $scope.selectedCliente.dispositivos = $r;
    }

    $scope.send = function(url, data, $cb){
        $http.post(url, data, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(response) {
            if(response.result == 1){
                $cb(response);                
                $scope.alerts = new Array();
                $scope.alerts.push({
                    type: "alert-success",
                    msg: 'Permisos guardados'
                });
            } else if((response.result == 0)){
                $scope.alerts = new Array();
                $scope.alerts.push({
                    type: "alert-danger",
                    msg: response.msg
                });
            }
            $timeout(function(){
                $scope.alerts = new Array();
            }, 3500);
        });
    };       

    $scope.startAgain();
}); 
