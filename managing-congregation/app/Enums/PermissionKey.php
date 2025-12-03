<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionKey: string
{
    // Territories
    case TERRITORIES_VIEW = 'territories.view';
    case TERRITORIES_ASSIGN = 'territories.assign';
    case TERRITORIES_MANAGE = 'territories.manage';

    // Publishers
    case PUBLISHERS_VIEW = 'publishers.view';
    case PUBLISHERS_MANAGE = 'publishers.manage';

    // Reports
    case REPORTS_VIEW = 'reports.view';
    case REPORTS_EXPORT = 'reports.export';

    // Formation
    case FORMATION_VIEW = 'formation.view';
    case FORMATION_MANAGE = 'formation.manage';
}
