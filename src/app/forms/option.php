<?php
namespace app\forms;

use std, gui, framework, app;

class option extends AbstractForm {

    /**
     * @event applymysql.action 
     */
    function doApplymysqlAction(UXEvent $e = null) {    
        $this->saveMysql($this->ip, $this->user, $this->password, $this->database);
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null) {    
        $this->loadMysql($this);
    }
}
