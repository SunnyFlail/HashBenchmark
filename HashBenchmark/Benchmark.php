<?php

namespace SunnyFlail\HashBenchmark;

class Benchmark
{
    private function __construct(
        private string $hashName,
        private float $time,
        private string $result,
        private int $ramUsage,
    ) {
    }

    public function __call(string $methodName, array $args)
    {
        $propertyName = lcfirst(substr($methodName, 2));

        if (isset($this->$propertyName)) {
            return $this->$propertyName;
        }

        printf("%s doesn't have property named %s !", __CLASS__, $propertyName);
    }

    public function fasterThan(?Benchmark $benchmark): bool
    {
        if (is_null($benchmark)) {
            return true;
        }

        return $time < $benchmark->getTime();
    }

    public function mapToPdo(): array
    {
        return [
            ":hashName" => $this->hashName,
            ":time" => $this->time,
            ":result" => $this->result,
            ":ramUsage" => $this->ramUsage
        ];
    }

    public static function run(string $hashName, string $input): Benchmark
    {
        $time = microtime(true);
        $ram = memory_get_usage();
        $input = hash($hashName, $input);
        $time = number_format(microtime(true) - $time, 20);
        $ram = memory_get_usage() - $ram;
        
        return new Benchmark($hashName, $time, $input, $ram);
    }

}
