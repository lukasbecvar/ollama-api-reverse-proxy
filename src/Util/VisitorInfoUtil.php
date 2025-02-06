<?php

namespace App\Util;

/**
 * Class VisitorInfoUtil
 *
 * Util for get visitor info
 *
 * @package App\Util
 */
class VisitorInfoUtil
{
    private SecurityUtil $securityUtil;

    public function __construct(SecurityUtil $securityUtil)
    {
        $this->securityUtil = $securityUtil;
    }

    /**
     * Get visitor IP address
     *
     * @return string|null The current visitor IP address
     */
    public function getIP(): ?string
    {
        $ipAddress = null;

        // check client IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        }

        // check forwarded IP (get IP from cloudflare visitors)
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && $ipAddress == null) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'Unknown';
        }

        // get ip address from remote addr
        if ($ipAddress == null) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        // escape ip address
        if ($ipAddress !== null) {
            $ipAddress = $this->securityUtil->escapeString($ipAddress);
        }

        return $ipAddress ?? 'Unknown';
    }
}
