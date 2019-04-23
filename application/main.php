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
<body ng-controller="mainCtrl" ng-cloak class="app header-fixed sidebar-fixed aside-menu-fixed <?php echo ($this->mod->showSideBar() ? 'sidebar-lg-show' : '') ?>">
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
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
               aria-expanded="false">
                Bienvenido <?php echo self_escape_string($first_name) . " " . self_escape_string($last_name) ?>
                <img class="img-avatar" src="media/img/avatars/no-image.png" alt="admin@bootstrapmaster.com">
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
                        Inicio
                    </a>
                </li>
                <?php
                        $cats = $this->getModulesByCategory();
                        foreach ($cats as $cat) {
                            if (count($cat['MODULES']) > 0) {
                ?>
                                <li class="nav-title" style="font-size: 15px; background-color: #bababa; color: black; padding: .35rem 1rem">
                                    <!--<span class="glyphicon <?php echo self_escape_string($cat['ICON']) ?>"></span>-->
                                    <?php echo self_escape_string($cat['NAME']) ?>
                                </li>
                                <?php
                                    foreach ($cat['MODULES'] as $mod) {
                                ?>
                                        <li class="nav-item" >
                                            <a class="nav-link" style="font-size: 13px; padding: .5rem 1rem" href='./?mod=<?php echo $mod['PATH'] ?> '>
                                                <!--<i class="nav-icon icon-drop"></i>--> <?php echo self_escape_string($mod['NAME']) ?>
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
    <ol class="breadcrumb m-0">
          <li class="breadcrumb-item font-weight-bold" style="font-size: 18px">
              <?php 
              echo $this->mod->myTitle()
              ?>
          </li>
          </li>
        </ol>
        <div class="container-fluid p-2">
            <div class="animated fadeIn">
                <div class="card">
                    <div class="card-body p-3">
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
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.23.0/moment.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.23.0/locale/es.js"></script>
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
		//SETTING MOMENT
		moment.locale('es');
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

        app.filter('emptylistfilter', function($filter, $rootScope){
            const validateInput = function(input){
                switch(typeof input){
                    case 'object':
                        for(p in input){
                            if(input[p]){
                                return true;
                            }
                        }
                        return false;
                    default:
                        return !(!input);
                }
            }

            return function(input, filterVal, limit, emptyDefault){
                if(input && validateInput(filterVal)){
                    let list = $filter('filter')(input, filterVal);
                    $rootScope.$broadcast('emptylistfilter.found', list.length);
                    limit = limit || list.length
                    if(list.length <= limit){
                        return list;
                    } else {
                        return [];
                    }
                }
                $rootScope.$broadcast('emptylistfilter.found', 0);
                return emptyDefault || [];
            }
        })

        app.filter('trasladoemptylistfilter', function($filter, $rootScope){
            return function(input, filterVal, limit){
                return $filter('emptylistfilter')(input, filterVal, limit, input.filter(i => i.cantidad > 0));
            }
        })

        app.directive(
			"bnLazySrc",
			function( $window, $document ) {
 
 
				// I manage all the images that are currently being
				// monitored on the page for lazy loading.
				var lazyLoader = (function() {
 
					// I maintain a list of images that lazy-loading
					// and have yet to be rendered.
					var images = [];
 
					// I define the render timer for the lazy loading
					// images to that the DOM-querying (for offsets)
					// is chunked in groups.
					var renderTimer = null;
					var renderDelay = 100;
 
					// I cache the window element as a jQuery reference.
					var win = $( $window );
 
					// I cache the document document height so that
					// we can respond to changes in the height due to
					// dynamic content.
					var doc = $document;
					var documentHeight = doc.height();
					var documentTimer = null;
					var documentDelay = 2000;
 
					// I determine if the window dimension events
					// (ie. resize, scroll) are currenlty being
					// monitored for changes.
					var isWatchingWindow = false;
 
 
					// ---
					// PUBLIC METHODS.
					// ---
 
 
					// I start monitoring the given image for visibility
					// and then render it when necessary.
					function addImage( image ) {
 
						images.push( image );
 
						if ( ! renderTimer ) {
 
							startRenderTimer();
 
						}
 
						if ( ! isWatchingWindow ) {
 
							startWatchingWindow();
 
						}
 
					}
 
 
					// I remove the given image from the render queue.
					function removeImage( image ) {
 
						// Remove the given image from the render queue.
						for ( var i = 0 ; i < images.length ; i++ ) {
 
							if ( images[ i ] === image ) {
 
								images.splice( i, 1 );
								break;
 
							}
 
						}
 
						// If removing the given image has cleared the
						// render queue, then we can stop monitoring
						// the window and the image queue.
						if ( ! images.length ) {
 
							clearRenderTimer();
 
							stopWatchingWindow();
 
						}
 
					}
 
 
					// ---
					// PRIVATE METHODS.
					// ---
 
 
					// I check the document height to see if it's changed.
					function checkDocumentHeight() {
 
						// If the render time is currently active, then
						// don't bother getting the document height -
						// it won't actually do anything.
						if ( renderTimer ) {
 
							return;
 
						}
 
						var currentDocumentHeight = doc.height();
 
						// If the height has not changed, then ignore -
						// no more images could have come into view.
						if ( currentDocumentHeight === documentHeight ) {
 
							return;
 
						}
 
						// Cache the new document height.
						documentHeight = currentDocumentHeight;
 
						startRenderTimer();
 
					}
 
 
					// I check the lazy-load images that have yet to
					// be rendered.
					function checkImages() {
 
						// Log here so we can see how often this
						// gets called during page activity.
						console.log( "Checking for visible images..." );
 
						var visible = [];
						var hidden = [];
 
						// Determine the window dimensions.
						var windowHeight = win.height();
						var scrollTop = win.scrollTop();
 
						// Calculate the viewport offsets.
						var topFoldOffset = scrollTop;
						var bottomFoldOffset = ( topFoldOffset + windowHeight );
 
						// Query the DOM for layout and seperate the
						// images into two different categories: those
						// that are now in the viewport and those that
						// still remain hidden.
						for ( var i = 0 ; i < images.length ; i++ ) {
 
							var image = images[ i ];
 
							if ( image.isVisible( topFoldOffset, bottomFoldOffset ) ) {
 
								visible.push( image );
 
							} else {
 
								hidden.push( image );
 
							}
 
						}
 
						// Update the DOM with new image source values.
						for ( var i = 0 ; i < visible.length ; i++ ) {
 
							visible[ i ].render();
 
						}
 
						// Keep the still-hidden images as the new
						// image queue to be monitored.
						images = hidden;
 
						// Clear the render timer so that it can be set
						// again in response to window changes.
						clearRenderTimer();
 
						// If we've rendered all the images, then stop
						// monitoring the window for changes.
						if ( ! images.length ) {
 
							stopWatchingWindow();
 
						}
 
					}
 
 
					// I clear the render timer so that we can easily
					// check to see if the timer is running.
					function clearRenderTimer() {
 
						clearTimeout( renderTimer );
 
						renderTimer = null;
 
					}
 
 
					// I start the render time, allowing more images to
					// be added to the images queue before the render
					// action is executed.
					function startRenderTimer() {
 
						renderTimer = setTimeout( checkImages, renderDelay );
 
					}
 
 
					// I start watching the window for changes in dimension.
					function startWatchingWindow() {
 
						isWatchingWindow = true;
 
						// Listen for window changes.
						win.on( "resize.bnLazySrc", windowChanged );
						win.on( "scroll.bnLazySrc", windowChanged );
 
						// Set up a timer to watch for document-height changes.
						documentTimer = setInterval( checkDocumentHeight, documentDelay );
 
					}
 
 
					// I stop watching the window for changes in dimension.
					function stopWatchingWindow() {
 
						isWatchingWindow = false;
 
						// Stop watching for window changes.
						win.off( "resize.bnLazySrc" );
						win.off( "scroll.bnLazySrc" );
 
						// Stop watching for document changes.
						clearInterval( documentTimer );
 
					}
 
 
					// I start the render time if the window changes.
					function windowChanged() {
 
						if ( ! renderTimer ) {
 
							startRenderTimer();
 
						}
 
					}
 
 
					// Return the public API.
					return({
						addImage: addImage,
						removeImage: removeImage
					});
 
				})();
 
 
				// ------------------------------------------ //
				// ------------------------------------------ //
 
 
				// I represent a single lazy-load image.
				function LazyImage( element ) {
 
					// I am the interpolated LAZY SRC attribute of
					// the image as reported by AngularJS.
					var source = null;
 
					// I determine if the image has already been
					// rendered (ie, that it has been exposed to the
					// viewport and the source had been loaded).
					var isRendered = false;
 
					// I am the cached height of the element. We are
					// going to assume that the image doesn't change
					// height over time.
					var height = null;
 
 
					// ---
					// PUBLIC METHODS.
					// ---
 
 
					// I determine if the element is above the given
					// fold of the page.
					function isVisible( topFoldOffset, bottomFoldOffset ) {
 
						// If the element is not visible because it
						// is hidden, don't bother testing it.
						if ( ! element.is( ":visible" ) ) {
 
							return( false );
 
						}
 
						// If the height has not yet been calculated,
						// the cache it for the duration of the page.
						if ( height === null ) {
 
							height = element.height();
 
						}
 
						// Update the dimensions of the element.
						var top = element.offset().top;
						var bottom = ( top + height );
 
						// Return true if the element is:
						// 1. The top offset is in view.
						// 2. The bottom offset is in view.
						// 3. The element is overlapping the viewport.
						return(
								(
									( top <= bottomFoldOffset ) &&
									( top >= topFoldOffset )
								)
							||
								(
									( bottom <= bottomFoldOffset ) &&
									( bottom >= topFoldOffset )
								)
							||
								(
									( top <= topFoldOffset ) &&
									( bottom >= bottomFoldOffset )
								)
						);
 
					}
 
 
					// I move the cached source into the live source.
					function render() {
 
						isRendered = true;
 
						renderSource();
 
					}
 
 
					// I set the interpolated source value reported
					// by the directive / AngularJS.
					function setSource( newSource ) {
 
						source = newSource;
 
						if ( isRendered ) {
 
							renderSource();
 
						}
 
					}
 
 
					// ---
					// PRIVATE METHODS.
					// ---
 
 
					// I load the lazy source value into the actual
					// source value of the image element.
					function renderSource() {
 
						element[ 0 ].src = source;
 
					}
 
 
					// Return the public API.
					return({
						isVisible: isVisible,
						render: render,
						setSource: setSource
					});
 
				}
 
 
				// ------------------------------------------ //
				// ------------------------------------------ //
 
 
				// I bind the UI events to the scope.
				function link( $scope, element, attributes ) {
 
					var lazyImage = new LazyImage( element );
 
					// Start watching the image for changes in its
					// visibility.
					lazyLoader.addImage( lazyImage );
 
 
					// Since the lazy-src will likely need some sort
					// of string interpolation, we don't want to
					attributes.$observe(
						"bnLazySrc",
						function( newSource ) {
 
							lazyImage.setSource( newSource );
 
						}
					);
 
 
					// When the scope is destroyed, we need to remove
					// the image from the render queue.
					$scope.$on(
						"$destroy",
						function() {
 
							lazyLoader.removeImage( lazyImage );
 
						}
					);
 
				}
 
 
				// Return the directive configuration.
				return({
					link: link,
					restrict: "A"
				});
 
			}
        );

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