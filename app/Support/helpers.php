<?php

if (! function_exists('route_paths')) {
    /**
     * Register routes from a file or return the path.
     *
     * @return mixed
     */
    function route_paths(string $path, ?bool $require = true)
    {
        $path = ltrim($path, '/');
        $path = base_path('routes/'.$path.'.php');

        if (! $require) {
            return $path;
        }

        if ($require && file_exists($path)) {
            require $path;
        }
    }
}
