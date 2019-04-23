<?PHP 
//$date = new DateTime();
//$insert = array(
//    'ID' => sqlValue(encode_email_address('baci5190'), 'text'),
//    'PASSWORD' => sqlValue(md5('123456'),'text'),
//    'FIRST_NAME' => sqlValue('Bryan', 'text'),
//    'CREATED' => sqlValue($date->format('Y-m-d H:i:s'), 'date'),
//    'FK_PROFILE' => 1,
//    'LAST_LOGIN' => sqlValue($date->format('Y-m-d H:i:s'), 'date')
//);
//$this->db->query_insert('app_user',$insert);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Base</title>
    <link rel="shortcut icon" href="media/img/geoico.ico" />
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="media/js/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <!--script src="media/js/googleAnalytics.min.js"></script-->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="content-fluid">        
        <div class="row">
            <div class="modal fade" tabindex="-1" id="resetPass" role="dialog" aria-labelledby="resetPass" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4>Reseteo de contraseña</h4>
                        </div>
                        <form id="resetInfoForm" method="post" action="./?action=reset_req" role="form">             
                            <div class="modal-body">                    
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-1">&nbsp</div>
                                        <div class="col-md-10">
                                            <p>Solicita a uno de los administradores que resetee la contraseña por ti</p>
                                        </div>
                                        <div class="col-md-1">&nbsp</div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn btn-primary">Aceptar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <?php messageMe()?>
                <div class='row'>
                    <div class="col-md-4">&nbsp;</div>
                    <div class="col-md-4">
                        
                    </div>
                    <div class="col-md-4">&nbsp;</div>
                </div>
                <div class='row'>
                    <div class="col-md-4 hidden-sm hidden-xs">
                        
                    </div>
                        <div class="col-md-4" style="padding-top: 70px;">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="panel-title">Ingreso sistema</div>
                                </div>
                                <div class="panel-body">
                                    <form role="form" method="post" action="?action=li">
                                        <h4>Ingreso</h4>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="USER_LOG" placeholder="Usuario">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" name="PASS_LOG" placeholder="Contraseña">
                                            <span class="help-block forgot-block pointMe" onclick="$('#resetPass').modal()">¿Olvidaste la contraseña?</span>
                                        </div>
                                        <button type="submit" class="btn btn-primary myBtn">Ingresar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $("input[name='USER_LOG']").focus();
        })
    </script>
  </body>
</html>