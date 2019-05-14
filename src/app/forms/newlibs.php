<?php
namespace app\forms;

use facade\Json;
use std, gui, framework, app;

class newlibs extends AbstractForm {

    /**
     * @event filezip.action 
     */
    function doFilezipAction(UXEvent $e = null) {
        if (!$this->openfile->selected) {
            $dir = new DirectoryChooserScript();
            $dir->on('action', function() use ($dir) {
                $this->listView->items->clear();
                if ($this->css->selected) {
                   $file = fs::scan($dir->file->getPath(), ['extensions' => ['css'], 'excludeDirs' => true]); 
                } 
                if ($this->js->selected) {
                    $file = fs::scan($dir->file->getPath(), ['extensions' => ['js'], 'excludeDirs' => true]);
                } 
                if ($this->js->selected && $this->css->selected) {
                    $file = fs::scan($dir->file->getPath(), ['extensions' => ['js', 'css'], 'excludeDirs' => true]);
                }
                foreach ($file as $value) {
                    $this->listView->items->add($value);
                }
            });
            $dir->execute();
        } else {
            $dir = new FileChooserScript();
            $dir->on('action', function() use ($dir) {
                $this->listView->items->add($dir->file->getPath());
            });
            $dir->execute();
        }
    }

    /**
     * @event send.action 
     */
    function doSendAction(UXEvent $e = null) {    
        if (!$this->listView->items->isEmpty()) {
            if (!fs::isDir('tmp')) {
                fs::delete('tmp');
                mkdir('tmp');
            }
            fs::clean('./tmp/');
            $name = $this->name->text;
            $url = $this->url->text;
            $categoria = $this->categoria->selected;
            $ver = $this->ver->text;
            mkdir("./tmp/$name");
            mkdir("./tmp/$name/$ver");
            $js = [];
            $css = [];
            $php = [];
            foreach ($this->listView->items->toArray() as $value) {
                $extension = explode(".", $value);
                if ($extension[count($extension) - 1] == 'css') {
                    if (!fs::isDir("./tmp/$name/$ver/css/")) {
                        mkdir("./tmp/$name/$ver/css");
                    }
                    $execute = explode("/", $value);
                    array_push($css, $execute[count($execute) - 1]);
                    fs::copy($value, fs::abs("./tmp/$name/$ver/css/" . $execute[count($execute) - 1]));
                } elseif ($extension[count($extension) - 1] == 'js') {
                    if (!fs::isDir("./tmp/$name/$ver/js/")) {
                        mkdir("./tmp/$name/$ver/js");
                    }
                    $execute = explode("/", $value);
                    array_push($js, $execute[count($execute) - 1]);
                    fs::copy($value, fs::abs("./tmp/$name/$ver/js/" . $execute[count($execute) - 1]));
                } elseif ($extension[count($extension) - 1] == 'php') {
                    if (!fs::isDir("./tmp/$name/$ver/php/")) {
                        mkdir("./tmp/$name/$ver/php");
                    }
                    $execute = explode("/", $value);
                    array_push($php, explode(".", $execute[count($execute) - 1])[0]);
                    fs::copy($value, fs::abs("./tmp/$name/$ver/php/" . explode(".", $execute[count($execute) - 1])[0]));
                }
            }
            Json::toFile("./tmp/$name.json", [
                'url' => $url, 
                'categoria' => $categoria,
                "version" => ["$ver"],
                "modules" => $this->php->selected,
                "$ver" => [
                    "js" => [
                        $js
                    ],
                    "css" => [
                        $css
                    ],
                    "php" => [
                        $php
                    ]
                ]
            ]);
            UXDialog::showAndWait('Успешно создалось в tmp');
        } else {
            UXDialog::showAndWait('Нужно хотя-бы загрузить 1 файл', 'ERROR');
        }
    }

    /**
     * @event clear.action 
     */
    function doClearAction(UXEvent $e = null) {
        $this->toast("Я почистил лист :)");
        $this->listView->items->clear();
    }
}
