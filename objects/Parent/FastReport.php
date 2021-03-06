<?php
require VENDOR . DS . "autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
/**
 * Description of FastReport
 *
 * @author Bryan Cruz
 */
class FastReport extends FastModWrapper{
    private $resultSet;
    private $params;
    private $repPrefix;
    protected $r = 1;
    protected $msg = '';
    protected $excelFileName = 'Reporte'; 
    protected $useDefaultView = true;
    protected $columns = [];
    
    protected function showLeft() {}
    
    protected function showRight() {}
    
    protected function setParams($params){
        $this->params = is_array($params) ? $params : array();
    }
    
    protected function getParams(){
        return $this->params;
    }
    
    protected function setPrefix($prefix){
        $this->repPrefix = $prefix;
    }
    
    protected function showMiddle() {
        include VIEWS . DS . "rep_params.phtml";
    }
    
    private function showModule(){
        include VIEWS . DS . $this->repPrefix . "_params.phtml";
    }
    
    protected function fieldsAreValid(){
        return true;
    }
    
    protected function getResultSet(){
        return array();
    }
    
    public function processReport(){
        if($this->fieldsAreValid()){
            $this->resultSet = $this->getResultSet();
            $this->serveResultView();
        } else {
            $this->throwResponse();
        } 
    }
    
    public function generaExcel(){
        if($this->fieldsAreValid()){
            $this->resultSet = $this->getResultSet();
            $this->procesarExcel();
        } else {
            $this->throwWindowResponse();
        }
    }
    
    /*
    private function procesarExcel(){
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Hello World !');

        /*
        $cells = range('A', 'Z');
        $objPHPExcel = new Spreadsheet();
        // Add some data
        $activeSheet = $objPHPExcel->getActiveSheet();
        
        //Populamos el reporte
        for($i = 0; count($this->columns) > $i; $i++){
            if($this->columns[$i] instanceof FastReportColumn){
                $activeSheet->setCellValue($cells[$i] . "1", $this->columns[$i]->name);
            }
        }
        
        for($i = 0; count($this->resultSet) > $i; $i++){
            for($i2 = 0; count($this->columns) > $i2; $i2++){
                if($this->columns[$i2] instanceof FastReportColumn){
                    $activeSheet->setCellValue($cells[$i2] . ($i + 2), $this->columns[$i2]->serveValue($this->resultSet[$i]));
                }
            }
        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        
        
        // Redirect output to a client’s web browser (Excel2007)
        //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        
        header('Content-Type: application/xlsx');
        header('Content-Disposition: attachment;filename=test.xlsx');
        
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $writer = new Xlsx($objPHPExcel);
        /*
        $objWriter->save($this->excelFileName . $date->format(SHOW_DT_FORMAT) . ".xlsx");
        //$objWriter->save('php://output');
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        //$writer->save("05featuredemo.xlsx");
        $writer->save("php://output");
        
        $report = array(array('id'=>1,'name'=>'raj'),array('id'=>2,'name'=>'monica'),array('id'=>3,'name'=>'sonia'));
$t = time();
$filename = 'excel_report-'.$t.'.xls';
echo '<table class="table table-responsive table-hover " border="1" ><tbody>';
// Display results.
foreach($report as $row) {   
    echo '<tr>';
    foreach($row as $column) {
     // printf('%25s', $column);?>
       <td><?php echo $column; ?></td>
      <?php
    }
    print "</tr>";
}  
print "</tbody></table>";
@header("Content-type: application/vnd.ms-excel");
// Defines the name of the export file "filename.xls"
@header("Content-Disposition: attachment; filename=".$filename); 
    }
    */

    public function procesarExcel() {

        $result = "<table>";
        $result .= "<tr>";
        for($i = 0; count($this->columns) > $i; $i++){
            $result .= "<th>";
            if($this->columns[$i] instanceof FastReportColumn){
                $result.= $this->columns[$i]->name;
            }
            $result .= "</th>";
        }
        $result .= "</tr>";
        for($i = 0; count($this->resultSet) > $i; $i++){
            $result .= "<tr>";
            for($i2 = 0; count($this->columns) > $i2; $i2++){
                $result .= "<td>";
                if($this->columns[$i2] instanceof FastReportColumn){
                    $result .= $this->columns[$i2]->serveValue($this->resultSet[$i]);
                }
                $result .= "</td>";
            }
            $result .= "</tr>";
        }
        $result .= "<table>";
        $date = new Datetime();
        header('Content-Type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment;filename=%s_%s.xls', $this->excelFileName, $date->format(SQL_DT_FORMAT)));
        
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        print($result);
    }
    
    private function throwResponse(){
        echo json_encode(array('result' => $this->r, 'msg' => $this->msg));
    }
    
    private function throwWindowResponse(){
        ?>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="alert alert-<?php echo ($this->r == 0) ? "danger" : ($this->r == 1 ? "success" : "info") ?>">
                    <?php echo $this->msg ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    private function serveResultView(){
        ob_start();
        $file = $this->useDefaultView ? "rep_view.phtml" : $this->repPrefix . "_view.phtml";
        include VIEWS . DS . $file;
        $html = ob_get_clean();
        echo json_encode(array('result' => 1, 'data' => $html));
    }
    
    public function myJavascript() {
        parent::myJavascript();
        ?>
    <script>
        app.controller('WrapperCtrl', function($scope, $http, $timeout){
            $scope.ajaxUrl = "./?<?php echo AJAX ?>=true&mod=<?php echo $this->instanceName ?>";
            $scope.inResult = false;
            $scope.generate = function(){
                $scope.send($scope.ajaxUrl + '&act=processReport', $("#reportForm").serialize(), function(r){
                    $scope.report = r.data;
                    $scope.inResult = true;
                });
            };
            $scope.send = function(url, data, $cb){
                //$scope.loading();
                $http.post(url, data, {
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }).success(function(response) {
                    if(response.result == 1){
                        $cb(response);
                        //$scope.doneLoading();
                    } else if((response.result == 0)){
                        //$scope.doneLoading();
                        $scope.alerts = new Array();
                        $scope.alerts.push({
                            type: "alert-danger",
                            msg: response.msg
                        });
                        $timeout(function(){
                            $scope.alerts = new Array();
                        }, 3500);
                    }                       
                });
                //$scope.doneLoading();
            };
            
            $scope.generarExcel = function(){
                window.open($scope.ajaxUrl + "&act=generaExcel&" + $("#reportForm").serialize());
            };
            
            $scope.postToWindow = function(path, params, method) {
                method = method || "post"; // Set method to post by default if not specified.
                
                // The rest of this code assumes you are not using a library.
                // It can be made less wordy if you use one.
                var form = document.createElement("form");
                form.setAttribute("method", method);
                form.setAttribute("action", path);
                form.setAttribute("target", "_blank");

                for(var key in JSON.parse(params)) {
                    if(params.hasOwnProperty(key)) {
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", key);
                        hiddenField.setAttribute("value", params[key]);

                        form.appendChild(hiddenField);
                     }
                }

                document.body.appendChild(form);
                form.submit();
            }
        });
    </script>
        <?php
    }
}
