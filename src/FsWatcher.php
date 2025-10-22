<?php

namespace CrudeSSG;

class FsWatcher
{
    private array $lastStates = [];

    public function __construct(private string $directory, private array $excludeDirs = [])
    {
    }

    public function watch(callable $onChange, int $intervalSeconds = 2, $running = true)
    {
        while ($running) {
            if ($this->hasChanges()) {
                $onChange();
            }
            sleep($intervalSeconds);
        }
    }

    /**
     * Check for changes in the directory since the last check
     */
    private function hasChanges(): bool
    {
        $currentStates = [];
        $files = FsUtil::rscandir($this->directory);
        foreach ($files as $file) {
            if ($this->isExcluded($file)) {
                continue;
            }
            $currentStates[$file] = filemtime($file);
        }

        $result = false;
        foreach ($currentStates as $file => $mtime) {
            if (!isset($this->lastStates[$file]) || $this->lastStates[$file] !== $mtime) {
                $result = true;
                break;
            }
        }

        $this->lastStates = [...$currentStates];
        return $result;
    }

    private function isExcluded(string $file): bool
    {
        foreach ($this->excludeDirs as $excludeDir) {
            if (str_starts_with($file, $excludeDir)) {
                return true;
            }
        }
        return false;
    }
}