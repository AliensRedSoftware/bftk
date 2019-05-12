<?php
namespace app\forms;

use std, gui, framework, app;

class packagemanager extends AbstractForm {

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null) {
        $this->request('getlist');
    }

    /**
     * @event hide 
     */
    function doHide(UXWindowEvent $e = null) {    
        $this->free();
    }

    /**
     * @event listView.action 
     */
    function doListViewAction(UXEvent $e = null) {
        if ($e->sender->items->isEmpty()) {
            return ;
        }
        if ($e->sender->selectedItem == null) {
            return ;
        }
        if (!$this->version->items->isEmpty()) {
            $this->version->items->clear();
        }
        $this->showPreloader('Обработка списка пожалуйста подождите...');
        $selected = $e->sender->selectedItem . '.json';
        $request = 'https://dsafkjdasfkjnasgfjkasfbg.000webhostapp.com/manager/cuka/' . $selected;
        $this->httpClient->getAsync($request, [], function ($e) {
            $data = Json::decode($e->body());
            $url = $data['url'];
            $ver = $data['version'];
            foreach ($ver as $version){
                $this->version->items->add($version);
            }
            $this->version->enabled = true;
            $this->version->selectedIndex = 0;
            $this->hidePreloader();
            if (trim($url) == null) {
                $this->toast("Я не смогла загрузить сайт с проектом :(");
                $this->hidePreloader();
                return ;
            }
            $this->browser->engine->load($url);
        });
    }

    /**
     * @event categoria.action 
     */
    function doCategoriaAction(UXEvent $e = null) {
        $this->csslist->items->clear();
        $this->jslist->items->clear();
        $this->version->items->clear();
        $this->install->enabled = false;
        $this->version->enabled = false;
        if ($this->categoria->selectedIndex == 0) {
            $request = 'https://dsafkjdasfkjnasgfjkasfbg.000webhostapp.com/manager/cuka.php/getlist';
            $this->showPreloader('Получение списка пожалуйста подождите...');
            $this->httpClient->getAsync($request, [], function ($e) use ($aut) {
                $this->listView->items->clear();
                foreach (explode("\n", $e->body()) as $value) {
                    $file = explode('.', $value);
                    $this->listView->items->add($file[0]);
                }
                $this->hidePreloader();
                $this->showPreloader('Обработка списка пожалуйста подождите...');
                $listSorting = new UXListView();
                foreach ($this->listView->items->toArray() as $val) {
                    $request = 'https://dsafkjdasfkjnasgfjkasfbg.000webhostapp.com/manager/cuka/' . $val . '.json';
                    $this->httpClient->getAsync($request, [], function ($e) use ($val,$listSorting) {
                        $listSorting->items->add($val);
                    });
                }
                $this->listView->items = $listSorting->items;
                $this->hidePreloader();
            });
        } elseif ($this->categoria->selectedIndex == 1) {
            $request = 'https://dsafkjdasfkjnasgfjkasfbg.000webhostapp.com/manager/cuka.php/getlist';
            $this->showPreloader('Получение списка пожалуйста подождите...');
            $this->httpClient->getAsync($request, [], function ($e) use ($aut) {
                $this->listView->items->clear();
                foreach (explode("\n", $e->body()) as $value) {
                    $file = explode('.', $value);
                    $this->listView->items->add($file[0]);
                }
                $this->hidePreloader();
                $this->showPreloader('Обработка списка пожалуйста подождите...');
                $listSorting = new UXListView();
                foreach ($this->listView->items->toArray() as $val) {
                    $request = 'https://dsafkjdasfkjnasgfjkasfbg.000webhostapp.com/manager/cuka/' . $val . '.json';
                    $this->httpClient->getAsync($request, [], function ($e) use ($val,$listSorting) {
                        $data = Json::decode($e->body());
                        if ($data['categoria'] == $this->categoria->selected) {
                            $listSorting->items->add($val);
                        }
                    });
                }
                $this->listView->items = $listSorting->items;
                $this->hidePreloader();
            });
        }
    }

    /**
     * @event newlibs.action 
     */
    function doNewlibsAction(UXEvent $e = null) {    
        $this->showPreloader('Ожидание ответа от формы...');
        $this->form('newlibs')->showAndWait();
        $this->hidePreloader();
    }

    /**
     * @event browser.load 
     */
    function doBrowserLoad(UXEvent $e = null) {    
        $this->toast("Я смогла загрузить сайт с проектом :)");
        $this->hidePreloader();
    }

    /**
     * @event browser.fail 
     */
    function doBrowserFail(UXEvent $e = null) {
        $this->toast("Я не смогла загрузить сайт с проектом :(");
        $this->hidePreloader();
    }

    /**
     * @event browser.running 
     */
    function doBrowserRunning(UXEvent $e = null) {    
        $this->showPreloader('Обработка сайта пожалуйста подождите...');
    }

    /**
     * @event version.action 
     */
    function doVersionAction(UXEvent $e = null) {
        if (!$this->csslist->items->isEmpty()) {
            $this->csslist->items->clear();
        }
        if (!$this->jslist->items->isEmpty()) {
            $this->jslist->items->clear();
        }
        if ($e->sender->items->isEmpty()) {
            return ;
        }
        $val = $this->listView->selectedItem . '.json';
        $request = 'https://dsafkjdasfkjnasgfjkasfbg.000webhostapp.com/manager/cuka/' . $val;
        $this->showPreloader('Получение списка пожалуйста подождите...');
        $this->httpClient->getAsync($request, [], function ($e) {
            $ver = $this->version->selected;
            $data = Json::decode($e->body());
            $csslist = $data[$ver]["css"];
            $jslist = $data[$ver]["js"];
            foreach ($csslist[0] as $css) {
                $this->csslist->items->add($css);
            }
            foreach ($jslist[0] as $js) {
                $this->jslist->items->add($js);
            }
            $this->hidePreloader();
            $installed = $this->getInstalled($this->csslist->items->toArray(), $this->jslist->items->toArray());
            $this->install->enabled = true;
            if ($installed == true) {
                $this->install->graphic = new UXImageView (new UXImage("res://.data/img/delete.png"));
                $this->install->tooltipText = 'Удалить';
            } else {
                $this->install->graphic = new UXImageView (new UXImage("res://.data/img/add.png")); 
                $this->install->tooltipText = 'Установить';
            }
        });
    }

    /**
     * @event install.action 
     */
    function doInstallAction(UXEvent $e = null) {
        $theme = $this->getTheme();
        $css = $this->getPath_css();
        $categoria = $this->categoria->selected;
        $name = $this->listView->selectedItem;
        $ver = $this->version->selected;
        $this->listView->selectedIndex = -1;
        $url = "https://dsafkjdasfkjnasgfjkasfbg.000webhostapp.com/manager/zip/$categoria/$name/$ver/css/";
        if ($e->sender->tooltipText == 'Удалить') {
            if (uiConfirm("Вы точно хотите удалить это ?)")) {
                $path = "./$theme/$css/";
                foreach ($this->csslist->items->toArray() as $value) {
                    fs::delete($path . $value);
                }
                $this->toast('Успешно удалилось');
                $this->listView->selectedIndex = 0;
            }
        } else {
            $this->showPreloader('Скачивается файл...');
            $arraycss = [];
            foreach ($this->csslist->items->toArray() as $value) {
                Logger::info($url . $value);
                array_push($arraycss, $url . $value);
            }
            $this->downloader->on('successAll', function () {
                $this->toast('Успешно устанавилась!');
                $this->listView->selectedIndex = 0;
                $this->hidePreloader();
            });
            $this->downloader->urls = $arraycss;
            $this->downloader->destDirectory = "./$theme/$css/";
            $this->downloader->start();
        }
    }

}
