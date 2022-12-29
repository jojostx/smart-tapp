<?php
// @formatter:off
// phpcs:ignoreFile

/**
 * A helper file for Laravel, to provide autocomplete information to your IDE
 * Generated for Laravel 9.43.0.
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @see https://github.com/barryvdh/laravel-ide-helper
 */

namespace Illuminate\View {
    /**
     * 
     *
     */
    class ComponentAttributeBag
    {
        /**
         * 
         *
         * @see \Livewire\LivewireServiceProvider::registerViewMacros()
         * @param mixed $name
         * @static 
         */
        public static function wire($name)
        {
            return \Illuminate\View\ComponentAttributeBag::wire($name);
        }
    }
    /**
     * 
     *
     */
    class View
    {
        /**
         * 
         *
         * @see \Livewire\Macros\ViewMacros::extends()
         * @param mixed $view
         * @param mixed $params
         * @static 
         */
        public static function extends($view, $params = [])
        {
            return \Illuminate\View\View::extends($view, $params);
        }
        /**
         * 
         *
         * @see \Livewire\Macros\ViewMacros::layout()
         * @param mixed $view
         * @param mixed $params
         * @static 
         */
        public static function layout($view, $params = [])
        {
            return \Illuminate\View\View::layout($view, $params);
        }
        /**
         * 
         *
         * @see \Livewire\Macros\ViewMacros::layoutData()
         * @param mixed $data
         * @static 
         */
        public static function layoutData($data = [])
        {
            return \Illuminate\View\View::layoutData($data);
        }
        /**
         * 
         *
         * @see \Livewire\Macros\ViewMacros::section()
         * @param mixed $section
         * @static 
         */
        public static function section($section)
        {
            return \Illuminate\View\View::section($section);
        }
        /**
         * 
         *
         * @see \Livewire\Macros\ViewMacros::slot()
         * @param mixed $slot
         * @static 
         */
        public static function slot($slot)
        {
            return \Illuminate\View\View::slot($slot);
        }
    }
}

namespace Illuminate\Contracts\View {
    /**
     * 
     *
     */
    class ComponentAttributeBag
    {
        /**
         * 
         *
         * @see \Livewire\LivewireServiceProvider::registerViewMacros()
         * @param mixed $name
         * @static 
         */
        public static function wire($name)
        {
            return \Illuminate\View\ComponentAttributeBag::wire($name);
        }
    }
    /**
     * 
     *
     */
    class View
    {
        /**
         * 
         *
         * @see \Livewire\Macros\ViewMacros::extends()
         * @param mixed $view
         * @param mixed $params
         * @static 
         */
        public static function extends($view, $params = [])
        {
            return \Illuminate\View\View::extends($view, $params);
        }
        /**
         * 
         *
         * @see \Livewire\Macros\ViewMacros::layout()
         * @param mixed $view
         * @param mixed $params
         * @static 
         */
        public static function layout($view, $params = [])
        {
            return \Illuminate\View\View::layout($view, $params);
        }
        /**
         * 
         *
         * @see \Livewire\Macros\ViewMacros::layoutData()
         * @param mixed $data
         * @static 
         */
        public static function layoutData($data = [])
        {
            return \Illuminate\View\View::layoutData($data);
        }
        /**
         * 
         *
         * @see \Livewire\Macros\ViewMacros::section()
         * @param mixed $section
         * @static 
         */
        public static function section($section)
        {
            return \Illuminate\View\View::section($section);
        }
        /**
         * 
         *
         * @see \Livewire\Macros\ViewMacros::slot()
         * @param mixed $slot
         * @static 
         */
        public static function slot($slot)
        {
            return \Illuminate\View\View::slot($slot);
        }
    }
}