<?php

namespace OZiTAG\Tager\Backend\HttpCache;


class HttpCache
{
    private function getDataFolder()
    {
        $folder = realpath(__DIR__ . '/../../../../../storage/app/tager-data');

        if (!is_dir($folder)) {
            mkdir($folder, 0777);
        }

        return $folder;
    }

    private function save($namespace, $filename, $data)
    {
        $namespaceFolder = $this->getDataFolder() . '/' . $namespace;
        if (!is_dir($namespaceFolder)) {
            mkdir($namespaceFolder, 0777);
        }

        $filename = $namespaceFolder . '/' . $filename . '.json';

        $f = fopen($filename, 'w+');
        fwrite($f, json_encode($data));
        fclose($f);
    }

    public function set($namespace, $filename, $jobClass)
    {
        //
    }

    public function clear($namespace)
    {
        //
    }
}
