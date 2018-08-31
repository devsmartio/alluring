<?php
$userData = unserialize($_SESSION[USER]);
$user = $userData['data'];
$first_name = $user['FIRST_NAME'];
$last_name = $user['LAST_NAME'];
?>
<!DOCTYPE html>
<html lang="en" ng-app="app">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Base / <?php echo $this->mod->myTitle() ?></title>
    <link rel="shortcut icon" href="media/img/geoico.ico"/>
    <!-- Bootstrap -->
    <!--    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />-->
    
    <link href="media/jquery-ui/jquery-ui.min.css" rel='stylesheet'/>
    <link href="bower_components/ng-grid/ng-grid.min.css" type="text/css" rel="stylesheet"/>
    <link rel="stylesheet" href="bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css"/>
    <!-- Icons-->
    <link href="node_modules\@coreui\icons\css\coreui-icons.min.css" rel="stylesheet">
    <link href="node_modules/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
    <link href="node_modules/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="node_modules/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">
    <!-- Main styles for this application-->
    <link href="media/css/core-ui.min.css" rel="stylesheet">
    <link href="vendors/pace-progress/css/pace.min.css" rel="stylesheet">
    <link href="media/css/site.css" type="text/css" rel="stylesheet"/>
    <link href="media/js/angular/xeditable.min.css" type="text/css" rel="stylesheet"/>
    <?php
    if ($this->mod != null) {
        echo $this->mod->myStyle();
    }
    ?>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>
<body ng-controller="mainCtrl" ng-cloak class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
<!--    <div class="alert alert-info center-block" ng-show="mainLoading" style="margin-left: auto; margin-right: auto; position: fixed; z-index: 999; width: 90%"><strong>Cargando</strong>
    Espera un momento...
</div> -->
<header class="app-header navbar">
        <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
      </button>
    <a class="navbar-brand" href="#">
        <img class="navbar-brand-full" src="media/img/logo.png" width="89" height="25" alt="CoreUI Logo">
        <img class="navbar-brand-minimized" src="media/img/brand/sygnet.svg" width="30" height="30" alt="CoreUI Logo">
    </a>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show"><span class="navbar-toggler-icon"></span></button>
    <ul class="nav navbar-nav d-md-down-none">
        <!--<li class="nav-item px-3"><a class="nav-link" href="#">Dashboard</a></li><li class="nav-item px-3"><a class="nav-link" href="#">Users</a></li><li class="nav-item px-3"><a class="nav-link" href="#">Settings</a></li>-->
    </ul>
    <ul class="nav navbar-nav ml-auto">
        <!--<li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="icon-bell"></i><span class="badge badge-pill badge-danger">5</span></a></li>-->
        <li class="nav-item d-md-down-none">
            <a class="nav-link" href="#">
                <i class="icon-list"></i>
            </a>
        </li>
        <li class="nav-item d-md-down-none">
            <a class="nav-link" href="#">
                <i class="icon-location-pin"></i>
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
               aria-expanded="false">
                Bienvenido <?php echo self_escape_string($first_name) . " " . self_escape_string($last_name) ?>
                <img class="img-avatar" src="media/img/avatars/6.jpg" alt="admin@bootstrapmaster.com">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header text-center">
                    <strong>Account</strong>
                </div>
                <a class="dropdown-item" href="#">
                    <i class="fa fa-wrench"></i> Cambiar contraseña
                </a>
                <a class="dropdown-item" href="?action=lo">
                    <i class="fa fa-lock"></i> Logout
                </a>
            </div>
        </li>
    </ul>
    
</header>
<div class="app-body">
    <div class="sidebar">
        <nav class="sidebar-nav ps ps--active-y">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="./">
                        <i class="nav-icon icon-speedometer"></i>
                        Inicio
                        <span class="badge badge-primary">NEW</span>
                    </a>
                </li>
                <?php
                        $cats = $this->getModulesByCategory();
                        foreach ($cats as $cat) {
                            if (count($cat['MODULES']) > 0) {
                ?>
                                <li class="nav-title">
                                    <span class="glyphicon <?php echo self_escape_string($cat['ICON']) ?>"></span>
                                    <?php echo self_escape_string($cat['NAME']) ?>
                                </li>
                                <?php
                                    foreach ($cat['MODULES'] as $mod) {
                                ?>
                                        <li class="nav-item">
                                            <a class="nav-link" href='./?mod=<?php echo $mod['PATH'] ?> '>
                                                <i class="nav-icon icon-drop"></i> <?php echo self_escape_string($mod['NAME']) ?>
                                            </a>
                                        </li>
                                <?php
                                    }
                                ?>
                <?php
                            }
                        }
                ?>
            </ul>
        </nav>
        <button class="sidebar-minimizer brand-minimizer" type="button"></button>
    </div>
    <main class="main" ng-controller="WrapperCtrl">
    <ol class="breadcrumb">
          <li class="breadcrumb-item font-weight-bold" style="font-size: 18px">
              <?php 
              echo $this->mod->myTitle()
              ?>
          </li>
          </li>
        </ol>
        <div class="container-fluid">
            <div class="animated fadeIn">
                <div class="card">
                    <div class="card-body">
                        <!-- here -->
                        <div id="moduleCont">
                                    <?php
                                    if ($this->mod != null) {
                                        $this->mod->init();
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<footer class="app-footer">
    <div>
        <a href="#">Alluring Concept</a>
        <span>&copy; 2018</span>
        </div>
        <div class="ml-auto">
        <span>Powered by</span>
        <a href="https://getsmartio.com">SmartIO</a>
    </div>
</footer>
<script src="node_modules/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="bower_components/moment/min/moment.min.js"></script>
<script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="node_modules/pace-progress/pace.min.js"></script>
<script src="node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
<script src="node_modules/@coreui/coreui/dist/js/coreui.min.js"></script>
<!-- Plugins and scripts required by this view-->
<script src="node_modules/chart.js/dist/Chart.min.js"></script>
<script src="node_modules/@coreui/coreui-plugin-chartjs-custom-tooltips/dist/js/custom-tooltips.min.js"></script>
<script src="media/tooltipster/js/jquery.tooltipster.min.js"></script>
<script src="bower_components/angular/angular.min.js" type="text/javascript"></script>
<script src="media/js/angular/angular-route.min.js"></script>
<script src="bower_components/ng-grid/ng-grid-2.0.14.min.js" type="text/javascript"></script>
<script src="media/js/angular/angular-sanitize.min.js"></script>
<script src="media/js/bootstrap-ui.min.js"></script>
<script src="media/jquery-ui/jquery-ui.min.js" type='text/javascript'></script>
<script src="media/js/angular/angular-file-upload.min.js" type="text/javascript"></script>
<script src="media/js/core-ui.js"></script>
<script src="media/js/angular/xeditable.min.js" type="text/javascript"></script>
<script src="media/js/angular/angular-file-upload.min.js" type="text/javascript"></script>
<script src='media/js/MainWrapper.js' type='text/javascript'></script>
<script>
        var app = angular.module('app', ['ngSanitize', 'ngRoute', 'angularFileUpload', 'ngGrid', 'ui.bootstrap', 'xeditable']);
        app.directive('compile', ['$compile', function ($compile) {
            return function (scope, element, attrs) {
                scope.$watch(
                    function (scope) {
                        return scope.$eval(attrs.compile);
                    },
                    function (value) {
                        element.html(value);
                        $compile(element.contents())(scope);
                    }
                );
            };
        }]);

        app.run(function(editableOptions) {
            editableOptions.theme = 'bs3';
        });

        $(function () {
            $('.tipMe').tooltipster({
                position: 'left'
            });
            $('.dateMe').datetimepicker({
                format: "DD/MM/YYYY"
            });
            $("#menuToggle").click(function (e) {
                e.preventDefault();
                console.log('Cambiando');
                if ($("#menuCont").hasClass("col-lg-3")) {
                    $("#menuCont").removeClass("col-lg-3 col-md-3").addClass("hidden-lg hidden-md");
                    $("#moduleCont").removeClass("col-lg-9 col-md-9").addClass("col-lg-12 col-md-12");
                } else {
                    $("#menuCont").removeClass("hidden-lg hidden-md").addClass("col-lg-3 col-md-3");
                    $("#moduleCont").removeClass("col-lg-12 col-md-12").addClass("col-lg-9 col-md-9");
                }
            });
        });
        app.controller('mainCtrl', function ($scope, $timeout, $rootScope, $filter) {
            $scope.mainLoading = true;
            $timeout(function () {
                $scope.mainLoading = false;
            }, 750);
            $rootScope.dataLoading = false;
            $scope.loading = function () {
                $rootScope.dataLoading = true;
            };
            $scope.doneLoading = function () {
                $rootScope.dataLoading = false;
            };
        });
    </script>
<?php
if ($this->mod != null) {
    $this->mod->myJavascript();
}
?>
</body>
</html>