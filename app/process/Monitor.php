<?php

namespace app\process;

class Monitor
{
    public function __construct(
        protected array $monitorDir = [],
        protected array $monitorExtensions = [],
        protected array $options = []
    ) {}

    public function checkAllFilesChange(): bool
    {
        return false;
    }
}
