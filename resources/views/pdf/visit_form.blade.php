<!doctype html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>VisitForm</title>
    <style>
        body {
            font-family: 'arial', sans-serif;
            line-height: 0 !important;
            font-size: 18px;
        }

        table {
            border-spacing: 0 !important;
        }


        .text-center {
            text-align: center;
        }

        .w-half {
            width: 50%;
        }

        .w-twenty {
            width: 20%;
        }

        .w-thirty {
            width: 30%;
        }

        .border {
            border-color: black;
            border-style: solid;
            border-width: 1px;
        }

        .bg-gray {
            background-color: #dfdfdf;
        }


        .border-none {
            border: none !important;

            .
        }
    </style>
    <style>
        .tg {
            border-collapse: collapse;
            border-spacing: 0;
        }

        .tg td {
            border-color: black;
            border-style: solid;
            border-width: 1px;
            font-family: Arial, sans-serif;
            font-size: 17px;
            overflow: hidden;
            word-break: normal;
        }

        .tg th {
            border-color: black;
            border-style: solid;
            border-width: 1px;
            font-family: Arial, sans-serif;
            font-size: 18px;
            font-weight: normal;
            overflow: hidden;
            padding: 10px 5px;
            word-break: normal;
        }


        .tg .tg-eqm3 {
            border-color: inherit;
            border-width: 1px;
            font-size: 18px;
            text-align: center;
            vertical-align: top
        }
    </style>
</head>
<body dir="rtl">.
@php
    $relation =  $visit->relative->residents()->where('residents.id', $visit->resident_id)->first()->pivot->relation;
@endphp
<table style="width: 100%;">
    <tr>
        <td class="border-none">
            <img width="150" height="75" src="{{public_path('logo.png')}}" alt="logo">
        </td>
        <td class="border-none" style="text-align: left">
            {{'الرقم المرجعي للزيارة: ' . $ar->arNormalizeText(str_pad($visit->id, '5', '0', STR_PAD_LEFT), 'Hindu')}}
        </td>
    </tr>
    <tr>
        <td style="text-align: center;" colspan="2">{{$ar->arNormalizeText('نموذج رقم (19)', 'Hindu')}}</td>
    </tr>
    <tr>
        <td style="text-align: center;" colspan="2">{{$ar->arNormalizeText('(استمارة الزيارات)', 'Hindu')}}</td>
    </tr>
</table>
<table style="margin-top: 5mm;width: 100%">
    <tr>
        <td class="text-center border w-twenty bg-gray">اليوم</td>
        <td class="text-center border w-thirty">{{$ar->arNormalizeText($ar->date('l', $visit->date_time->timestamp), 'Hindu')}}</td>
        <td class="text-center border w-twenty bg-gray">التاريخ</td>
        <td class="text-center border w-thirty">{{$ar->arNormalizeText($visit->date_time->format('Y/m/d'), 'Hindu')}}</td>
    </tr>
    <tr>
        <td class="text-center border w-twenty bg-gray">اسم المستفيد</td>
        <td class="text-center border w-thirty">{{ $visit->resident->name}}</td>
        <td class="text-center border w-twenty bg-gray">نوع الزيارة</td>
        <td class="text-center border w-thirty">{{\App\Models\Visit::TYPE[$visit->type]}}</td>
    </tr>
    <tr>
        <td class="text-center border w-twenty bg-gray">وقت الزيارة</td>
        <td class="text-center border w-thirty">{{$ar->arNormalizeText($ar->date('h:i a', $visit->date_time->timestamp), 'Hindu')}}</td>
        <td class="text-center border w-twenty bg-gray">مدة الزيارة</td>
        <td class="text-center border w-thirty">{{$visit->type=='internal'?  $ar->arNormalizeText($visit->duration . ' ' . \App\Models\Visit::DURATION_TYPE[$visit->duration_type], 'Hindu'):''}}</td>
    </tr>
    <tr>
        <td class="text-center border w-twenty bg-gray">اسم مسجل البيانات</td>
        <td class="text-center border w-thirty">{{auth()->user()->name}}</td>
        <td class="text-center border w-twenty bg-gray">اخصائي المتابعة</td>
        <td class="text-center border w-thirty"></td>
    </tr>
    <tr>
        <td class="text-center border w-twenty bg-gray">الطبيب</td>
        <td class="text-center border w-thirty"></td>
        <td class="text-center border w-twenty bg-gray">الممرض</td>
        <td class="text-center border w-thirty"></td>
    </tr>
</table>

<table class="tg" style="margin-top: 5mm">
    <tr>
        <td class="wtg-eqm3 text-center w-half" colspan="2">
            القائم بالزيارة من الاسرة المصرح لها
        </td>
        <td class="tg-eqm3 text-center w-half" colspan="2">
            @foreach(\App\Models\RelativeResident::RELATION as $key=>$item)
                <svg height="16" width="16" style="float: left;width: 10px;">
                    <circle cx="8" cy="8" r="5" stroke="black" stroke-width="1" fill="{{$key==$relation || ($key == 'other' and !array_key_exists($relation, \App\Models\RelativeResident::RELATION))? 'black':''}}"/>
                </svg>
                <span>{{$item}}</span>
            @endforeach
            :
            @if($key == 'other' and !array_key_exists($relation, \App\Models\RelativeResident::RELATION))
                {{$relation}}
            @endif
        </td>
    </tr>
    <tr>
        <td class="tg-eqm3 bg-gray">اسم الزائر</td>
        <td class="tg-eqm3">{{$visit->relative->name}}</td>
        <td class="tg-eqm3 bg-gray">رقم السجل الوطني</td>
        <td class="tg-eqm3">{{$ar->arNormalizeText($visit->relative->id_number, 'Hindu')}}</td>
    </tr>
    <tr>
        <td class="tg-eqm3 bg-gray">تاريخ اخر زيارة</td>
        <td class="tg-eqm3">{{$ar->arNormalizeText(($last_visit? :$visit)->date_time->format('Y/m/d'), 'Hindu')}}</td>
        <td class="tg-eqm3 bg-gray">مدي تواصل الاسرة</td>
        <td class="tg-eqm3"></td>
    </tr>
    <tr>
        <td class="tg-eqm3 bg-gray">عدد ايام الزيارة</td>
        <td class="tg-eqm3">{{$visit->type=='external' ?  $ar->arNormalizeText($visit->duration . ' ' . \App\Models\Visit::DURATION_TYPE[$visit->duration_type], 'Hindu'):''}}</td>
        <td class="tg-eqm3 bg-gray">رقم التواصل</td>
        <td class="tg-eqm3">{{$ar->arNormalizeText($visit->relative->phon1, 'Hindu')}}</td>
    </tr>
    <tr>
        <td class="tg-eqm3 bg-gray" colspan="2">مدي الاندماج بين المستفيد والاسرة</td>
        <td class="tg-eqm3" colspan="2">
            <svg width="10" height="10">
                <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
            </svg>
            <span>جيدة</span>
            <svg width="10" height="10">
                <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
            </svg>
            <span>غير جيدة</span>
            <svg width="10" height="10">
                <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
            </svg>
            <span>الي حد ما</span>
        </td>
    </tr>
    <tr>
        <td class="tg-eqm3" colspan="2">عرض وتدريب علي خطة العمل العلاجية</td>
        <td class="tg-eqm3" colspan="2">
            <svg width="10" height="10">
                <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
            </svg>
            <span>تم</span>
            <svg width="10" height="10">
                <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
            </svg>
            <span>لم يتم</span>
        </td>
    </tr>
    <tr>
        <td class="tg-eqm3" colspan="2">الهدف من الزيارة</td>
        <td class="tg-eqm3" colspan="2">
            <svg width="10" height="10">
                <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
            </svg>
            <span>طلب المركز الحضور</span>
            <svg width="10" height="10">
                <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
            </svg>
            <span>حضور ورشة عمل تدريبية</span>
            <div>
                <svg width="10" height="10">
                    <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
                </svg>
                <span>متابعة حالة المستفيد</span>
            </div>
        </td>
    </tr>
    <tr>
        <td class="tg-eqm3" colspan="2" style="text-align: right;padding-right: 5mm">الاجراءات المتخذة قبل الخروج للزيارات الخارجية</td>
        <td class="tg-eqm3" style="text-align: right; padding-right: 5mm" colspan="2">
            <div>
                <svg width="10" height="10">
                    <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
                </svg>
                <span>الكشف الطبي قبل وبعد الزيارة</span>
            </div>

            <div>
                <svg width="10" height="10">
                    <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
                </svg>
                <span>النظافة الشخصية للحالة</span>
            </div>
            <div>
                <svg width="10" height="10">
                    <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
                </svg>
                <span>تسليم الادوية</span>
            </div>
            <div>
                <svg width="10" height="10">
                    <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
                </svg>
                <span>متابعة الوزن</span>
            </div>
            <div>
                <svg width="10" height="10">
                    <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
                </svg>
                <span>اعطاء الادوية</span>
            </div>
            <div>
                <svg width="10" height="10">
                    <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
                </svg>
                <span>ارجاع الكسوة مع المقييم</span>
            </div>
            <div>
                <svg width="10" height="10">
                    <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
                </svg>
                <span>الاعتناء بالمقيم والمحافظة علي سلامته</span>
            </div>
            <div>
                <svg width="10" height="10">
                    <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
                </svg>
                <span>اتباع البرنامج الغذائي للمقييم</span>
            </div>
            <div>
                <svg width="10" height="10">
                    <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
                </svg>
                <span>متابعة مواعيده بالمستشفي اثناء الزيارة</span>
            </div>
            <div>
                <svg width="10" height="10">
                    <rect width="10" height="10" stroke="black" stroke-width="1" fill="white"/>
                </svg>
                <span>متابعة الخطط والبرامج التطويرية مع المقييم بالمنزل</span>
            </div>
        </td>
    </tr>
    <tr>
        <td class="tg-eqm3" style="text-align: right; padding-right: 5mm" colspan="2" rowspan="6">
            ملاحظات من ولي الامر
        </td>
        <td class="tg-eqm3">اخصائي المتابعة</td>
        <td class="tg-eqm3">التوقيع</td>
    </tr>
    <tr>
        <td class="tg-eqm3">&nbsp;</td>
        <td class="tg-eqm3">&nbsp;</td>
    </tr>
    <tr>
        <td class="tg-eqm3">مراقب القسم</td>
        <td class="tg-eqm3">التوقيع</td>
    </tr>
    <tr>
        <td class="tg-eqm3">&nbsp;</td>
        <td class="tg-eqm3">&nbsp;</td>
    </tr>
    <tr>
        <td class="tg-eqm3">{{$visit->relative->name}}</td>
        <td class="tg-eqm3">التوقيع</td>
    </tr>
    <tr>
        <td class="tg-eqm3">&nbsp;</td>
        <td class="tg-eqm3">&nbsp;</td>
    </tr>
</table>
<table style="width: 100%">
    <tr class="text-center">
        <td><img src="{{public_path('footer.jpg')}}" alt=""></td>
    </tr>
</table>
</body>
</html>
