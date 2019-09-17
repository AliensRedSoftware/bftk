<?php
namespace app\modules;

use facade\Json;
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
        $this->httpClient->getAsync($this->url . $method, [], function ($e) use ($method) {
            $this->redirect($method, $e->body());
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
                    $val = explode('.', $value)[0];
                    $url = "https://dsafkjdasfkjnasgfjkasfbg.000webhostapp.com/manager/cuka/" . $val . '.json';
                    $this->httpClient->getAsync($url, [], function ($data) use ($val, $packagemanager) {
                        $data = Json::decode($data->body());
                        $modules = $data['modules'];
                        if ($modules == true && $packagemanager->isModules->selected) {
                            $packagemanager->listView->items->add($val);
                        } elseif ($modules == false && !$packagemanager->isModules->selected) {
                            $packagemanager->listView->items->add($val);
                        }
                    });
                    $packagemanager->hidePreloader();
                }
            break;
        }
    }
    
    /**
     * Возвращает есть такой модуль или нет 
     */
    public function getInstalled(array $csslist, array $jslist, array $moduleslist) {
        $MainModule = new MainModule();
        $form = app()->getForm(MainForm);
        $theme = $MainModule->getTheme();
        $css = $MainModule->getPath_css();
        $js = $MainModule->getPath_js();
        $modules = $MainModule->getPath_modules();
        $platform = $form->platform->selected;
        $csscurrent = fs::scan("./$theme/$platform/$css", ['extensions' => ['css'], 'excludeDirs' => true]);
        $jscurrent = fs::scan("./$theme/$platform/$js", ['extensions' => ['js'], 'excludeDirs' => true]);
        $modulescurrent = fs::scan("./$theme/$platform/$modules", ['extensions' => ['php'], 'excludeDirs' => true]);
        $accesscss = 0;
        $accessjs = 0;
        $accessmodules = 0;
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
        foreach ($modulescurrent as $modulescurrent) {
            $modules = explode("/", $modulescurrent);
            foreach ($moduleslist as $modulesvalue) {
                if ($modules[count($modules) - 1] == $modulesvalue . '.php') {
                    $accessmodules++;
                }
            }
        }
        if (count($csslist) == $accesscss && count($jslist) == $accessjs && count($moduleslist) == $accessmodules) {
            return true;
        } else {
            return false;
        }
    }
}
