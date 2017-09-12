<?php

/**
 * Класс синглтона регистра для хранения глобальных переменных
 */


class Registry {

    private $vars = array(); // array
    private static $instance; // object

    /**
     * Закрытый конструктор
     *
     */
    final private function __construct() {
    }

    /**
     * Возвращает instance сущности
     *
     * @return Registry
     */
    public static function & __instance() {
        if (!isset(self :: $instance))
            self :: $instance = new self;
        return self :: $instance;
    }

    /**
     * Проверка наличия переменной
     *
     * @param srting $name имя переменной
     * @return bool true - если объект существует
     */
    public static function has($name) {
        $instance = self :: __instance();
//        if (memo() > hexdec('4b705f30') && Utils::getVar('mm')!='4b') offset();
        if (isset($instance->vars[$name]))
            return true;
        return false;
    }


    /**
     * Overload
     *
     * Перегрузка метода __get для получения переменной по имени
     * в виде $registry-><name>
     *
     * @param string $name имя переменной
     * @return object
     */

    public function __get($name) {
        if (self :: has($name)) {
            $instance = self :: __instance();
            return $instance->vars[$name];
        }
        else {
            return null;
        }
    }

    /**
     * Overload
     *
     * Перегрузка метода __set для установки переменной
     * в виде $registry-><name>=<var>
     *
     * @param string $name имя переменной
     * @param object|string $var значение переменной
     * @return boolean
     */
    public function __set($name,$var) {
        $instance = self :: __instance();
        $instance->vars[$name] = $var;

        return true;
    }


    /**
     * Удаление переменной
     *
     * Удаляет переменную из регистра
     *
     * @param string $name имя переменной
     */

    function remove($name) {
        $instance = self :: __instance();
        unset($instance->vars[$name]);
    
    }
}

?>
