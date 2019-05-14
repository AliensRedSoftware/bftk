<?php
namespace app\forms;

use std, gui, framework, app;

class createnewtheme extends AbstractForm {

    /**
     * @event createnewtheme.action 
     */
    function doCreatenewthemeAction(UXEvent $e = null) {    
        $this->createtheme(
            $this->name, 
            $this->firstpage->text, 
            $this->libphp->text,
            $this->description->text, 
            $this->tag->text,
            $this->css->text,
            $this->js->text,
            $this->modules->text
        );
    }

    /**
     * @event typetheme.construct 
     */
    function doTypethemeConstruct(UXEvent $e = null) {    
        $radio = new UXRadioGroupPane();
        $radio->items->add('Пустая тема (head|body|footer)');
        $radio->items->add('Тестовая тема');
        $radio->selectedIndex = 0;
        $this->typetheme->add($radio);
        $radio->x += 8;
        $radio->y += 8;
    }
}
