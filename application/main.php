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
    <meta name="description"
          content="CVJungle es una empresa que se dedica a facilitar a los usuarios la búsqueda de empleados y empleos, en Guatemala, por medio de una página web dinámica e interactiva.">
    <meta name="keywords" content="empresa,generar,empleo,trabajo,Guatemala,CVJungle,Jungle,currículum,vitae,cv">
    <title>Base / <?php echo $this->mod->myTitle() ?></title>
    <link rel="shortcut icon" href="media/img/geoico.ico"/>
    <!-- Bootstrap -->
    <!--    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />-->
    <link rel="stylesheet" href="bootstrap/css/coreui.min.css">
    <link href="media/jquery-ui/jquery-ui.min.css" rel='stylesheet'/>
    <link href="bower_components/ng-grid/ng-grid.min.css" type="text/css" rel="stylesheet"/>
    <link rel="stylesheet"
          href="bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css"/>
    <link href="media/css/site.css" type="text/css" rel="stylesheet"/>
    <?php
    if ($this->mod != null) {
        echo $this->mod->myStyle();
    }
    ?>
    <script src="media/js/jquery.min.js"></script>
    <script type="text/javascript" src="bower_components/moment/min/moment.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript"
            src="bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <script src="media/tooltipster/js/jquery.tooltipster.min.js"></script>
    <script src="bower_components/angular/angular.min.js" type="text/javascript"></script>
    <script src="media/js/angular/angular-route.min.js"></script>
    <script src="bower_components/ng-grid/ng-grid-2.0.14.min.js" type="text/javascript"></script>
    <script src="media/js/angular/angular-sanitize.min.js"></script>
    <script src="media/js/bootstrap-ui.min.js"></script>
    <script src="media/jquery-ui/jquery-ui.min.js" type='text/javascript'></script>
    <script src="media/js/angular/angular-file-upload.min.js" type="text/javascript"></script>
    <style>
        .main .container-fluid {
            padding: 0px;
        }
        .card-body {
            -ms-flex: 1 1 auto;
            flex: 1 1 auto;
            padding: 0rem;

        }
    </style>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
        var app = angular.module('app', ['ngSanitize', 'ngRoute', 'angularFileUpload', 'ngGrid', 'ui.bootstrap']);
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
        app.controller('mainCtrl', function ($scope, $timeout, $rootScope) {
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
    <style>
        .gridStyle {
            border: 1px solid rgb(212, 212, 212);
            width: 400px;
            height: 300px
        }
    </style>
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
        <img class="navbar-brand-full" src="media/img/brand/logo.svg" width="89" height="25" alt="CoreUI Logo">
        <img class="navbar-brand-minimized" src="media/img/brand/sygnet.svg" width="30" height="30" alt="CoreUI Logo">
    </a>
    <!--<button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show"><span class="navbar-toggler-icon"></span></button>-->
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
                    <i class="fa fa-bell-o"></i> Updates

                    <span class="badge badge-info">42</span>
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fa fa-envelope-o"></i> Messages

                    <span class="badge badge-success">42</span>
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fa fa-tasks"></i> Tasks

                    <span class="badge badge-danger">42</span>
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fa fa-comments"></i> Comments

                    <span class="badge badge-warning">42</span>
                </a>
                <div class="dropdown-header text-center">
                    <strong>Settings</strong>
                </div>
                <a class="dropdown-item" href="#">
                    <i class="fa fa-user"></i> Profile
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fa fa-wrench"></i> Settings
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fa fa-usd"></i> Payments

                    <span class="badge badge-secondary">42</span>
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fa fa-file"></i> Projects

                    <span class="badge badge-primary">42</span>
                </a>
                <div class="divider"></div>
                <a class="dropdown-item" href="#">
                    <i class="fa fa-shield"></i> Lock Account
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fa fa-lock"></i> Logout
                </a>
            </div>
        </li>
    </ul>
    <button class="navbar-toggler aside-menu-toggler d-md-down-none" type="button" data-toggle="aside-menu-lg-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    <button class="navbar-toggler aside-menu-toggler d-lg-none" type="button" data-toggle="aside-menu-show">
        <span class="navbar-toggler-icon"></span>
    </button>
</header>
<div class="app-body">
    <div class="sidebar">
        <nav class="sidebar-nav">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="./">
                        <i class="nav-icon icon-speedometer"></i>
                        Inicio
                        <span class="badge badge-primary">NEW</span>
                    </a>
                </li>
                <?php
                        foreach ($this->getModulesByCategory() as $cat) {
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
        <!--<button class="sidebar-minimizer brand-minimizer" type="button"></button>-->
    </div>
    <main class="main" ng-controller="WrapperCtrl">
        <div class="container-fluid">
            <div class="animated fadeIn">
                <div class="card">
                    <div class="card-body">
                        <!-- here -->
                        <div class="col-md-12 col-lg-12" id="moduleCont">
                            <div class="panel panel-default" style="min-height:750px;">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><?php echo $this->mod->myTitle() ?></h3>
                                </div>
                                <div class="panel-body">
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
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src='media/js/MainWrapper.js' type='text/javascript'></script>
<?php
if ($this->mod != null) {
    $this->mod->myJavascript();
}
?>
</body>
</html>