<?php

namespace SunnyFlail\HashBenchmark;

class BenchmarkContainer
{

    protected array $benchmarks;
    protected array $textKeys;

    public function __construct(
        protected array &$hashes
    ) {
        $this->benchmarks = [];
        $this->textKeys = [];
    }

    public function run(string $text)
    {
        $key = $this->textKeys[$text] = count($this->benchmarks);

        if (!isset($this->benchmarks[$key])) {
            $this->benchmarks[$key] = array_map(
                fn ($hash) => HashBenchmark::run($hash, $text),
                $hashes
            );
        }

        return $this->benchmarks[$key];
    }

    public function getFastest(string|int|null $key = null): ?HashBenchmark
    {
        if (is_null($key)) {
            return $this->getFastest(
                array_map(
                    fn ($val, $key) => $this->getFastest($key),
                    $this->benchmarks
                )
            );
        }

        if (is_string($key)) {
            $key = $this->textKeys[$key];
        }
       
        $benchmarks = is_array($key) ? $key : $this->benchmarks[$key]; 

        return array_reduce(
            $benchmarks,
            fn ($fastest, $current) => $fastest = $current->fasterThan($fastest) ? $current : $fastest
        );
    }

}
