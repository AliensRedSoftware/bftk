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

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null) {    
        if ($this->ini->get('execute', $this->themes->selected) != $this->execute->text) {
            if(is_file($this->themes->selected . fs::separator() . 'uri' . fs::separator() . $this->execute->text . '.php')) {
                Dialog::error('Такая страница уже есть придумайте что-то новое!');
            } else {
                rename($this->themes->selected . fs::separator() . fs::separator() . 'uri' . fs::separator() . $this->ini->get('execute', $this->themes->selected) .  '.php', $this->themes->selected . fs::separator() . 'uri' . fs::separator() . $this->execute->text . '.php');
            }
        }
        if ($this->ini->get('modules', $this->themes->selected) != $this->modules->text) {
            if (is_dir($this->themes->selected . fs::separator() . $this->platform->selected . fs::separator() . $this->modules->text)) {
                Dialog::error('Такая папка уже есть придумайте что-то новое!');
            } else {
                fs::move($this->themes->selected . fs::separator() . $this->platform->selected . fs::separator() . $this->ini->get('modules', $this->themes->selected), $this->themes->selected . fs::separator() . $this->platform->selected . fs::separator() . $this->modules->text);
            }
        }
        $this->setoptions($this->logserver->selected, $this->themes->selected, $this->execute->text, $this->platform->selected, $this->libphp->text, $this->css->text, $this->js->text, $this->modules->text);
    }
}
