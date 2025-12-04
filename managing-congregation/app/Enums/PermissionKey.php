<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionKey: string
{
    // Members Module
    case MEMBERS_VIEW = 'members.view';
    case MEMBERS_CREATE = 'members.create';
    case MEMBERS_EDIT = 'members.edit';
    case MEMBERS_DELETE = 'members.delete';
    case MEMBERS_EXPORT = 'members.export';

    // Financials Module
    case FINANCIALS_VIEW = 'financials.view';
    case FINANCIALS_CREATE = 'financials.create';
    case FINANCIALS_APPROVE = 'financials.approve';
    case FINANCIALS_EXPORT = 'financials.export';
    case FINANCIALS_MANAGE = 'financials.manage';

    // Documents Module
    case DOCUMENTS_VIEW = 'documents.view';
    case DOCUMENTS_UPLOAD = 'documents.upload';
    case DOCUMENTS_DOWNLOAD = 'documents.download';
    case DOCUMENTS_DELETE = 'documents.delete';
    case DOCUMENTS_MANAGE = 'documents.manage';

    // Communities Module
    case COMMUNITIES_VIEW = 'communities.view';
    case COMMUNITIES_CREATE = 'communities.create';
    case COMMUNITIES_EDIT = 'communities.edit';
    case COMMUNITIES_ASSIGN_MEMBERS = 'communities.assign_members';

    // Reports Module
    case REPORTS_VIEW = 'reports.view';
    case REPORTS_GENERATE = 'reports.generate';
    case REPORTS_EXPORT = 'reports.export';
    case REPORTS_SCHEDULE = 'reports.schedule';

    // Territories (Legacy - keeping for backward compatibility)
    case TERRITORIES_VIEW = 'territories.view';
    case TERRITORIES_ASSIGN = 'territories.assign';
    case TERRITORIES_MANAGE = 'territories.manage';

    // Publishers (Legacy - keeping for backward compatibility)
    case PUBLISHERS_VIEW = 'publishers.view';
    case PUBLISHERS_MANAGE = 'publishers.manage';

    // Formation (Legacy - keeping for backward compatibility)
    case FORMATION_VIEW = 'formation.view';
    case FORMATION_MANAGE = 'formation.manage';
}
