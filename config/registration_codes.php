<?php

/**
 * Registration verification codes for HealthChain.
 * These codes must be entered during sign-up to prove role eligibility.
 *
 * CHANGE THESE IN PRODUCTION.
 */
return [
    // Secret code required to register as SuperAdmin
    'admin_code' => 'HC@SUPERADMIN2024',

    // Valid Manager IDs
    'manager_ids' => [
        'MGR-001', 'MGR-002', 'MGR-003',
        'MGR-004', 'MGR-005',
    ],

    // Valid Staff IDs
    'staff_ids' => [
        'STF-0001', 'STF-0002', 'STF-0003',
        'STF-0004', 'STF-0005', 'STF-0006',
        'STF-0007', 'STF-0008', 'STF-0009', 'STF-0010',
    ],
];
