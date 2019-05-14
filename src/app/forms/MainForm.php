<?php
namespace app\forms;

use std, gui, framework, app;

class MainForm extends AbstractForm {

    /**
     * @event option.action 
     */
    function doOptionAction(UXEvent $e = null) {
        $this->showPreloader('Ожидание формы...');    
        $this->form('option')->showAndWait();
        $this->hidePreloader();
    }

    /**
     * @event applysettings.action 
     */
    function doApplysettingsAction(UXEvent $e = null) {
        if ($this->ini->get('execute', $this->themes->selected) != $this->execute->text) {
            rename($this->themes->selected . fs::separator() . 'uri' . fs::separator() . $this->ini->get('execute', $this->themes->selected) .  '.php', $this->themes->selected . fs::separator() . 'uri' . fs::separator() . $this->execute->text . '.php');
        }
        $this->setoptions($this->logserver->selected, $this->themes->selected, $this->execute->text, $this->libphp->text, $this->css->text, $this->js->text, $this->modules->text);
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null) {
        $this->getThemes($this->themes); 
        $this->loadoption($this);
        $this->themes->selected = $this->ini->get('theme', 'options');
    }

    /**
     * @event newtheme.action 
     */
    function doNewthemeAction(UXEvent $e = null) {    
        $this->showPreloader('Ожидание формы...');    
        $this->form('createnewtheme')->showAndWait();
        $this->hidePreloader();
    }

    /**
     * @event themes.action 
     */
    function doThemesAction(UXEvent $e = null) {    
        $this->loadoption($this);
    }

    /**
     * @event deletetheme.action 
     */
    function doDeletethemeAction(UXEvent $e = null) {    
        $this->removetheme($this->themes->selected, $this->themes);
    }

    /**
     * @event manager.action 
     */
    function doManagerAction(UXEvent $e = null) {    
        $this->showPreloader('Ожидание формы...');    
        $this->form('packagemanager')->showAndWait();
        $this->hidePreloader();

    }
}
