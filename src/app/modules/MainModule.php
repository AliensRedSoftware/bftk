<?php
namespace app\modules;

use std, gui, framework, app;

class MainModule extends AbstractModule {

    /**
     * Возвращаем подключенные темы
     */
    public function getThemes(UXComboBox $e) {
        $e->items->clear();
        $files = fs::scan('./', ['excludeFiles' => true]);
        $i = 0;
        foreach ($files as $value) {
            $val = explode('/', $value);
            if ($e->items->isEmpty()) {
                $selected = $val[1];
                $e->items->add($val[1]);
            }
            if ($selected != $val[1]) {
                $selected = $val[1];
                $e->items->add($val[1]);
            }
        }
        $e->selectedIndex = 0;
    }
    
    /**
     * Сохранить изменение mysql 
     */
    public function saveMysql($ip = '127.0.0.1', $user = 'root', $password = 'root', $database = 'test') {
        if (trim($ip->text) == null){
            Dialog::error('Поля ip не должно быть пустое!');
            $ip->text = null;
            return ;
        }
        if (trim($user->text) == null){
            Dialog::error('Поля имя пользователя не должно быть пустое!');
            $user->text = null;
            return ;
        }
        if (trim($password->text) == null){
            Dialog::error('Поля пароль не должно быть пустое!');
            $password->text = null;
            return ;
        }        
        if (trim($database->text) == null){
            Dialog::error('Поля имя базы данных не должно быть пустое!');
            $database->text = null;
            return ;
        }
        $this->ini->put([
            'ip' => $ip->text,
            'user' => $user->text,
            'password' => $password->text,
            'database' => $database->text
        ], 'mysql');
        file_put_contents('mysql.php', "<?php
class mysql {
    /* Настройка mysql
     * ip - Адрес сервера mysql
     * user - Имя пользователя mysql
     * password - Пароль пользователя mysql
     * database - Имя базы данных mysql
     */
    public " . '$ip' . " = '$ip->text';
    public " . '$user' . " = '$user->text';
    public " . '$password' . " = '$password->text';
    public " . '$database' . " = '$database->text';
}");
        Dialog::alert('Успешно сохранились данные mysql!');
    }
    
    /**
     * Загрузить данные mysql
     */
     public function loadMysql(option $option) {
         $option->ip->text = $this->ini->get('ip', 'mysql');
         $option->user->text = $this->ini->get('user', 'mysql');
         $option->password->text = $this->ini->get('password', 'mysql');
         $option->database->text = $this->ini->get('database', 'mysql');
     }
     
    /**
     * Загрузить данные option
     */
     public function loadoption(MainForm $MainForm) {
         $MainForm->logserver->selected = $this->ini->get('log', $MainForm->themes->selected);
         $MainForm->execute->text = $this->ini->get('execute', $MainForm->themes->selected);
         $MainForm->libphp->text = $this->ini->get('libphp', $MainForm->themes->selected);
         $MainForm->platform->selected = $this->ini->get('platform', $MainForm->themes->selected);
         $MainForm->js->text = $this->ini->get('js', $MainForm->themes->selected);
         $MainForm->css->text = $this->ini->get('css', $MainForm->themes->selected);
         $MainForm->modules->text = $this->ini->get('modules', $MainForm->themes->selected);
     }
     
    /**
     * Создать новую тему
     */
    public function createtheme ($name, $execute, $libphp, $description, $tag, $js, $css, $modules) {
        if (trim($js) == null) {
            $js = 'js';
        }        
        if (trim($css) == null) {
            $css = 'css';
        }
        if (trim($libphp) == null) {
            $libphp = 'page';
        }
        if (trim($modules) == null) {
            $modules = 'modules';
        }
        if (trim($name->text) == null) {
            $name->text = null;
            Dialog::error('Имя темы не должно быть пустое!');
            return;
        } else {
            $theme = app()->getForm(MainForm)->themes;
            foreach ($theme->items->toArray() as $value){
                if ($value == $name->text) {
                    Dialog::error("Такая тема уже существует => $name->text");
                    return ;
                }
            }
            mkdir($name->text);
            $this->getThemes($theme);
            $theme->selected = $name->text;
            mkdir($name->text . fs::separator() . 'uri');
            mkdir($name->text . fs::separator() . 'android');
            mkdir($name->text . fs::separator() . 'linux');
            //android
                mkdir($name->text . fs::separator() . 'android' . fs::separator() . $libphp);
                mkdir($name->text . fs::separator() . 'android' . fs::separator() . $js);
                mkdir($name->text . fs::separator() . 'android' . fs::separator() . $css);
                mkdir($name->text . fs::separator() . 'android' . fs::separator() . $modules);
                file_put_contents($name->text . fs::separator() . 'android' . fs::separator() . $css . fs::separator() . 'css.css', null);
                file_put_contents($name->text . fs::separator() . 'android' . fs::separator() . $js . fs::separator() . 'js.js', null);
                if (trim($execute) == null) {
                    $execute = $name->text;
                }
                $this->chead($name->text,$execute);
                $this->cheadlib($name->text, 'android' . fs::separator() . $libphp, $description, $tag, $css);
                $this->cbody($name->text, 'android' . fs::separator() . $libphp);
                $this->cfooter($name->text, 'android' . fs::separator() . $libphp, $js);
                $this->c404($name->text);
                $this->c403($name->text);
            //linux
                mkdir($name->text . fs::separator() . 'linux' . fs::separator() . $libphp);
                mkdir($name->text . fs::separator() . 'linux' . fs::separator() . $js);
                mkdir($name->text . fs::separator() . 'linux' . fs::separator() . $css);
                mkdir($name->text . fs::separator() . 'linux' . fs::separator() . $modules);
                file_put_contents($name->text . fs::separator() . 'linux' . fs::separator() . $css . fs::separator() . 'css.css', null);
                file_put_contents($name->text . fs::separator() . 'linux' . fs::separator() . $js . fs::separator() . 'js.js', null);
                if (trim($execute) == null) {
                    $execute = $name->text;
                }
                $this->chead($name->text,$execute);
                $this->cheadlib($name->text, 'linux' . fs::separator() . $libphp, $description, $tag, $css);
                $this->cbody($name->text, 'linux' . fs::separator() . $libphp);
                $this->cfooter($name->text, 'linux' . fs::separator() . $libphp, $js);
                $this->c404($name->text);
                $this->c403($name->text);
            $this->setoptions(false, $name->text, $execute, 'auto', $libphp, $css, $js, $modules);
            Dialog::alert("Успешно создалась тема! => $name->text");
            app()->form(createnewtheme)->hide();
        }
    }
      
    /**
     * Создает главную страницу
     */
    public function chead ($name, $execute) {
        file_put_contents($name . fs::separator() . 'uri' . fs::separator() . $execute . '.php','<?php
class ftk extends xlib {
    function __construct () {
        $this->req(["head", "body", "footer"]);
        $head = new head();
        $body = new body();
        $footer = new footer();
        $this->execute($head, $body, $footer);
    }

    function execute($head, $body, $footer) {
        $head->execute(' . "'$name'" . ');
        $body->execute();
        $footer->execute();
    }
}
');
    }
    
    /**
     * Создает 403 ошибку
     */
    public function c403 ($name) {
        file_put_contents($name . fs::separator() . 'uri' . fs::separator() . '403' . '.php','<?php
class ftk extends xlib {
    function __construct () {
        $this->req(["head", "body", "footer"]);
        $head = new head();
        $body = new body();
        $footer = new footer();
        $this->execute($head, $body, $footer);
    }

    function execute($head, $body, $footer) {
        $head->execute(' . "'Ошибка доступ к папкам запрещен!'" . ');
        $body->layout_403();
        $footer->execute();
    }
}');
    }
    /**
     * Создает 404 ошибку открытие страницы
     */
    public function c404 ($name) {
        file_put_contents($name . fs::separator() . 'uri' . fs::separator() . '404' . '.php','<?php
class ftk extends xlib {
    function __construct () {
        $this->req(["head", "body", "footer"]);
        $head = new head();
        $body = new body();
        $footer = new footer();
        $this->execute($head, $body, $footer);
    }

    function execute($head, $body, $footer) {
        $head->execute(' . "'Ошибка страница не найдена!'" . ');
        $body->layout_404();
        $footer->execute();
    }
}');
    }
    
    /**
     * Создание загаловка 
     */
     public function cheadlib ($name, $libphp, $description = 'Пустое описание сайта!', $tag = 'Аниме,Фильмы,Кино,Тесты,Порно', $css = 'css') {
         file_put_contents($name . fs::separator() . $libphp . fs::separator() . 'head.php','<?php
class head extends xlib {
    function execute ($title) {
        echo "<head>";
        $this->setTitle($title);
        $this->utf8();
        $this->description(' . "'$description'" . ');//О сайте
        $this->tag(' . "'$tag'" . ');
        $this->loader_css(' . "'$css'" . ');
        echo "</head>";
    }
}
');
     }
     
     /**
      * Создание тела 
      */
      public function cbody ($name, $libphp) {
          file_put_contents($name . fs::separator() . $libphp . fs::separator() . 'body.php', '<?php
class body extends xlib {

    /**
     * Главная страница
     */
    function execute () {
        echo "Привет мир";
    }

    /**
     * Ошибка 403
     */
    function layout_403 () {
        echo "403 Ошибка доступа";
    }

    /**
     * Ошибка 404
     */
    function layout_404 () {
         echo "404 страница не найдена";
    }
}
');
      }
      
    /**
     * Создание пола 
     */
    public function cfooter($name, $libphp, $js = 'js') {
        file_put_contents($name . fs::separator() . $libphp . fs::separator() . 'footer.php', '<?php
class footer extends xlib {
    function execute () {
        echo "<footer>";
        $this->loader_js(' . "'$js'" . ');
        echo "</footer>";
    }
}
');
    }  
    /**
     * Установить настройки 
     */
    public function setoptions ($log, $theme, $execute, $platform, $libphp, $css, $js, $modules) {
        $this->ini->set('theme', $theme, 'options');
        $this->ini->put([
            'log' => $log,
            'theme' => $theme,
            'execute' => $execute,
            'platform' => $platform,
            'libphp' => $libphp,
            'css' => $css,
            'js' => $js,
            'modules' => $modules
        ], $theme);
        if ($log == true) {
            $log = 'true';
        } else {
            $log = 'false';
        }
        file_put_contents('options.php', "<?php
class options {
    /**
     * Опции фреймворка
     * $log - логирование сервера
     * $theme - Имя темы
     * $execute - Точка запуска темы (index.php)
     * $platform - Имя устройства темы (auto)
     * $libphp - Либы где находится php
     */
    public " . '$log' . " = $log;
    public " . '$theme' . " = '$theme';
    public " . '$execute' . " = '$execute';
    public " . '$platform' . " = '$platform';
    public " . '$libphp' . " = '$libphp';
}
");
        app()->form(MainForm)->toast('Успешно сохранилась!');
    }
    
    /**
     * Удаление темы  
     */
     public function removetheme ($name, $theme) {
         if (uiConfirm('Вы точно хотите удалить тему вернуть будет невозможно!')){
             fs::clean($name);
             fs::delete($name);
             $this->getThemes($theme);
             $this->ini->removeSection($name);
             dialog::alert('Успешно удалилась тема!');
         }
     }
     
    /**
     * Возвращает подключенную тему 
     */
    public function getTheme() {
        return $this->ini->get('theme','options');
    }
    
    /**
     * Возвращает путь к css
     */
    public function getPath_css() {
        return $this->ini->get('css', $this->getTheme());
    }
    
    /**
     * Возвращает путь к js
     */
    public function getPath_js() {
        return $this->ini->get('js', $this->getTheme());
    }
    
    /**
     * Возвращает путь к modules
     */
    public function getPath_modules() {
        return $this->ini->get('modules', $this->getTheme());
    }
}
