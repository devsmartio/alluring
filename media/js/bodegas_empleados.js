app.controller('WrapperCtrl', function($scope, $http, $timeout){    
    $scope.ajaxUrl = "./?ajax=true&mod=MantBodegasEmpleados";    
    $scope.startAgain = function(){
        $scope.modData = new Array();
        $scope.alerts = new Array();
        $scope.selectedItem = '';
        $http.get($scope.ajaxUrl + "&act=getData").success(function(r){
            $scope.modData = r.data;
            $.each($scope.modData, function($id, row){
                $scope.setItemSelected(row.ALLOWED);
                $scope.setItemSelected(row.DENIED);
            });
        });
    };
    $scope.setItemSelected = function(rows){
        $.each(rows, function($id, row){
            row.selected = false;
        });
    };
    $scope.setSelected = function(){
        if($scope.selectedItem!=''){
            $.each($scope.modData, function($id, row){
                if(row.ID == $scope.selectedItem){
                    $scope.lastSelected = row;
                }
            });
        }
    };
    $scope.addPermissions = function(){
        $.each($scope.lastSelected.DENIED, function($id, row){
            if(row.selected){
                $scope.lastSelected.ALLOWED.push(row);
            }
        });
        var $r = $scope.lastSelected.DENIED.filter(function(row){
            return row.selected == false;
        });
        $scope.lastSelected.DENIED = $r;
        $scope.setItemSelected($scope.lastSelected.ALLOWED);
        $scope.setItemSelected($scope.lastSelected.DENIED);
    };
    $scope.removePermissions = function(){
        $.each($scope.lastSelected.ALLOWED, function($id, row){
            if(row.selected){
                $scope.lastSelected.DENIED.push(row);
            }
        });
        var $r = $scope.lastSelected.ALLOWED.filter(function(row){
            return row.selected == false;
        });
        $scope.lastSelected.ALLOWED = $r;
        $scope.setItemSelected($scope.lastSelected.ALLOWED);
        $scope.setItemSelected($scope.lastSelected.DENIED);
    };
    $scope.flagControl = function(row){
        if(row.selected === false){
            row.selected = true;
        } else {
            row.selected = false;
        }        
    };
    $scope.savePermissions = function(){
        $scope.send($scope.ajaxUrl + '&act=savePermissions',{data:$scope.modData}, function(r){
            if(r.result==1){
                location.reload();
            } 
        });                    
    }; 
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
