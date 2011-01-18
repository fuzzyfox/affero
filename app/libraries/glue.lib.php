<?php
    
    /**
     * GluePHP
     *
     * Glue is a PHP micro-framework. It provides one simple service: to maps
     * URLs to Classes. Everything else is up to you. The database, ORM, template
     * engine and all other components are under your control. Glue just glues
     * everything together.
     *
     * In MVC terms, Glue is the URL Routing and Controller portion while you
     * have total control over your choice of a Model and View layer.
     *
     * @author Joe Topjian
     * @version 1.0
     * @package affero
     * @subpackage libraries
     * @copyright 2009 Joe Topjian
     * @license BSD License
     */
    
    /**
     * glue
     *
     * Provides an easy way to map URLs to classes. URLs can be literal
     * strings or regular expressions.
     *
     * When the URLs are processed:
     *      * deliminators (/) are automatically escaped: (\/)
     *      * The beginning and end are anchored (^ $)
     *      * An optional end slash is added (/?)
     *	    * The i option is added for case-insensitive searches
     *
     * Example:
     *
     * $urls = array(
     *     '/' => 'index',
     *     '/page/(\d+) => 'page'
     * );
     *
     * class page {
     *      function GET($matches) {
     *          echo "Your requested page " . $matches[1];
     *      }
     * }
     *
     * glue::stick($urls);
     *
     */
    class glue {

        /**
         * stick
         *
         * the main static function of the glue class.
         *
         * @param   array    	$urls  	    The regex-based url to class mapping
         * @throws  Exception               Thrown if corresponding class is not found
         * @throws  Exception               Thrown if no match is found
         * @throws  BadMethodCallException  Thrown if a corresponding GET,POST is not found
         *
         */
        static function stick ($urls) {
			
            $method = strtoupper($_SERVER['REQUEST_METHOD']);
            $path = $_SERVER['REQUEST_URI'];
			
            $found = false;
			
            krsort($urls);
			
            foreach ($urls as $regex => $class) {
                $regex = str_replace('/', '\/', $regex);
                $regex = '^' . $regex . '\/?$';
                if (preg_match("/$regex/i", $path, $matches)) {
                    $found = true;
                    if (class_exists($class)) {
                        $obj = new $class;
                        if (method_exists($obj, $method)) {
                            $obj->$method($matches);
                        } else {
                            throw new BadMethodCallException("Method, $method, not supported.");
                        }
                    } else {
                        throw new Exception("Class, $class, not found.");
                    }
                    break;
                }
            }
            if (!$found) {
                throw new Exception("URL, $path, not found.");
            }
        }
    }
