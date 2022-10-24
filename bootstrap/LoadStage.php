<?php

declare(strict_types = 1);

namespace Bootstrap;

/**
 * A load stage enum instance.
 */
enum LoadStage
{
    case INIT;
    case EXEC;

    /**
     * Whether the current mode should load.
     * @param LoadStage $stage
     * @return bool
     */
    public function shouldLoad(LoadStage $stage): bool
    {
        return $this == $stage;
    }
}
