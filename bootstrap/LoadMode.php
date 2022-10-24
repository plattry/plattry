<?php

declare(strict_types = 1);

namespace Bootstrap;

/**
 * A load mode enum instance.
 */
enum LoadMode
{
    case ALL;
    case HTTP;

    /**
     * Whether the current mode should load.
     * @param array|LoadMode $modes
     * @return bool
     */
    public function shouldLoad(array|LoadMode $modes): bool
    {
        $modes instanceof LoadMode && $modes = [$modes];

        return in_array($this, $modes) || in_array(self::ALL, $modes);
    }
}
