<?php

namespace Database\Seeders;
use App\Models\City;
use App\Models\OrderState;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStateTableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('order_states')->insert(
            [
                [
                    'state_en' => "order received",
                    'state_ar' => "تم استلام الطلب",
                    'customer_state_en'=> "order received",
                    'customer_state_ar'=> "تم استلام الطلب",
                    'code'=> "100",
                    'is_active' => 1
                ],
                [
                    'state_en' => "order confirmed",
                    'state_ar' => "تم تأكيد الطلب",
                    'customer_state_en'=> "order confirmed",
                    'customer_state_ar'=> "تم تأكيد الطلب",
                    'code'=> "101",
                    'is_active' => 1
                ],
                [
                    'state_en' => "preparing order",
                    'state_ar' => "جاري التجهيز",
                    'customer_state_en'=> "preparing order",
                    'customer_state_ar'=> "جاري التجهيز",
                    'code'=> "102",
                    'is_active' => 1
                ],
                [
                    'state_en' => "quality assurance",
                    'state_ar' => "اختبار الجودة",
                    'customer_state_en'=> "quality assurance",
                    'customer_state_ar'=> "اختبار الجودة",
                    'code'=> "103",
                    'is_active' => 1
                ],
                [
                    'state_en' => "out for delivery",
                    'state_ar' => "جاري التوصيل",
                    'customer_state_en'=> "out for delivery",
                    'customer_state_ar'=> "جاري التوصيل",
                    'code'=> "104",
                    'is_active' => 1
                ],
                [
                    'state_en' => "delivered",
                    'state_ar' => "تم التوصيل",
                    'customer_state_en'=> "delivered",
                    'customer_state_ar'=> "تم التوصيل",
                    'code'=> "200",
                    'is_active' => 1
                ],
            ]

        );

    }
}
