<?php

namespace App\Src\Commands\Traits;

trait HandlesUserErrors
{
    protected function runWithUserFriendlyErrors(callable $callback): int
    {
        try {
            $callback();
        } catch (\RuntimeException $e) {
            $this->error("Error: " . $e->getMessage());
            if ($this->input->getOption('verbose')) {
                throw $e; 
            }
            return 1; 
        } catch (\Exception $e) {
            if ($this->input->getOption('verbose')) {
                throw $e; 
            }
            $this->error("Unexpected error: " . $e->getMessage());
            return 1;
        }

        return 0; 
    }
}
