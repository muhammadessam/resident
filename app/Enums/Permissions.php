<?php

namespace App\Enums;

enum Permissions: string
{
        case ADD_VISIT = 'اضافة زيارة';
        case RETURN_RESIDENT = 'اعادة مقييم';
        case EXTEND_VISIT = 'تمديد الزيارة';
        case VIEW_REPORTS = 'استعراض التقاير';
        case ADD_AND_MODIFY_RELATIVE = 'تعديل القريب';
}
