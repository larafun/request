<?php

namespace Larafun\Request\Traits;

use ExtPHP\Formatter\Formatter;

trait FormatsInput
{
    public function defaults()
    {
        return [];
    }

    public function formatters()
    {
        return [];
    }

    public function all($keys = null)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $formatter = new Formatter(parent::all($keys), $this->formatters());
        $merged = $this->customMerge($this->getDefaults($keys), $formatter->format());
        if (!count($keys)) {
            return $merged;
        }
        return array_intersect_key($merged, array_flip($keys));
    }

    /**
     * The defaults are being merge only with the non null formatted
     * values in order to avoid returning a null when a default
     * has been provided
     */
    protected function customMerge($defaults, $formatted)
    {
        $keys = array_merge(array_keys($defaults), array_keys($formatted));
        $results = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $formatted) && !is_null($formatted[$key])) {
                $results[$key] = $formatted[$key];
                continue;
            }
            if (array_key_exists($key, $defaults)) {
                $results[$key] = $defaults[$key];
                continue;
            }
            $results[$key] = $formatted[$key];
        }
        return $results;
    }

    protected function getDefaults($keys = null)
    {
        if (!$keys) {
            return $this->defaults();
        }

        $defaults = $this->defaults();
        $results = [];

        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            if (array_key_exists($key, $defaults)) {
                $results[$key] = $defaults[$key];
            }
        }

        return $results;
    }
}