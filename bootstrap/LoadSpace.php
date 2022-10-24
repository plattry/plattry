<?php

declare(strict_types = 1);

namespace Bootstrap;

/**
 * A load space enum instance.
 */
enum LoadSpace: string
{
    case DEVELOPMENT = "development";
    case TESTING = "testing";
    case PRODUCT = "product";

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
