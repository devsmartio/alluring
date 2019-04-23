<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of buttontemplate
 *
 * @author Edgar
 */
final class BootstrapBtnTemplate extends AngularGridTemplate{
    
    private $type;
    private $onClickBind;
    private $caption;
    
    function __construct($caption, $type = "btn-default", $onClickBind = "") {
        $this->caption = $caption;
        $this->type = $type;
        $this->onClickBind = $onClickBind;
    }
    public function renderTemplate() {
        ?>
        <button class="btn btn-sm <?php echo $this->type ?>" ng-click="<?php echo $this->onClickBind ?>">
            <?php echo $this->caption ?>
        </button>
        <?php
    }
}
