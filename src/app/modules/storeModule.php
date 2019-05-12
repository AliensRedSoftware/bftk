<?php
namespace app\modules;

use std, gui, framework, app;

class storeModule extends AbstractModule {

    $url = 'https://dsafkjdasfkjnasgfjkasfbg.000webhostapp.com/manager/cuka.php/';
    
    /**
     * @event httpClient.success 
     */
    function doHttpClientSuccess(ScriptEvent $e = null){    
        $form = app()->getForm(packagemanager);
        if ($form->listView->selectedIndex == -1) {
            $form->listView->selectedIndex = 0;
        }
    }

    /**
     * Запрос к серверу
     */
    public function request ($method) {
        $packagemanager = app()->getForm(packagemanager);
        $packagemanager->showPreloader('Ожидание запроса...');
        $this->httpClient->getAsync($this->url . $method, [], function ($e) use ($method, $packagemanager) {
            $this->redirect($method, $e->body());
            $packagemanager->hidePreloader();
        });
    }
    
    /**
     * Редирект ссылок
     */
    public function redirect ($method, $content) {
        $packagemanager = app()->getForm(packagemanager);
        switch ($method) {
            case 'getlist': 
                $packagemanager->listView->items->clear();
                foreach (explode("\n", $content) as $value) {
                    $this->listView->items->add(explode('.', $value)[0]);
                }
            break;
        }
        $packagemanager->hidePreloader();
    }
    
    /**
     * Возвращает есть такой модуль или нет 
     */
    public function getInstalled(array $csslist, array $jslist) {
        $MainModule = new MainModule();
        $theme = $MainModule->getTheme();
        $css = $MainModule->getPath_css();
        $js = $MainModule->getPath_js();
        $csscurrent = fs::scan("./$theme/$css", ['extensions' => ['css'], 'excludeDirs' => true]);
        $jscurrent = fs::scan("./$theme/$js", ['extensions' => ['js'], 'excludeDirs' => true]);
        $accesscss = 0;
        $accessjs = 0;
        foreach ($csscurrent as $csscurrent) {
            $css = explode('/', $csscurrent);
            foreach ($csslist as $cssvalue) {
                if ($css[count($css) - 1] == $cssvalue) {
                    $accesscss++;
                }
            }
        }
        foreach ($jscurrent as $jscurrent) {
            $js = explode("/", $jscurrent);
            foreach ($jslist as $jsvalue) {
                if ($js[count($js) - 1] == $jsvalue) {
                    $accessjs++;
                }
            }
        }
        if (count($csslist) == $accesscss && count($jslist) == $accessjs) {
            return true;
        } else {
            return false;
        }
    }
}
