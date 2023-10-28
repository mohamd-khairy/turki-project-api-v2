<?php

namespace App\Services;


use App\Models\TraceError;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentType;
use Illuminate\Support\Str;
use App\Models\WalletLog;
class MyFatoorahApiService
{


   public function Set_Payment_myfatoora(Customer $customer, Order $order, PaymentType $paymentType, Country $country, $type = 'UAE')
    {
          $lastPayment = Payment::latest('id')->first();
    
            $data = [
                "ref_no" => GetNextPaymentRefNo($country->code, $lastPayment != null ? $lastPayment->id + 1 : 1),
                "customer_id" => $customer->id,
                "order_ref_no" => $order->ref_no,
                "price" => (double)$order->total_amount_after_discount,
                "payment_type_id" => $paymentType->id,
                "status" => "Waiting for Client", // need to move to enum class
                "description" => "Payment Created", // need to move to enum class
            ];
            
          
          
        $basURL = "https://api.myfatoorah.com";
        $CallBackUrl = "https://almaraacompany.com/turki-api/api/v1/invoicestatus";  
       
       //b9GUEu3nRvdp9T0CgMVQD7nrbySRW3bCkWEwTBXLK4S7C1GGPFFX8LThwyz3pr3D2nyOCnWTAp4pWz9BBNTCzKcioSIqzQ8qsUKTjvWSEmrA8hsaMd-XarT3mUnHmuGFZ5ZDA0B-mdtZ7PzGneRRliPlRrcbvFbjJQVNnYAmZ8fP-vIwYeSI0xekQ4babTHTLGUyTtL1-Q2mx3XIKqS0ONv4j1yTSfG2P-hxRQzTYFkxwhlS1bqWVfq7owMZmiWqh9Fjr40mbOnGgdJu-yVXywR2a2Lt4iiNgcsMjTu_zxFxjzb7G04e5ybnD7DUVXLdSVh5aJHY4niOinjuNA2e3tOvR_5VTw69ECAol6Jj6d7ERrTDkDxVuwyqOz1Vb-skEwhbMEnFjNaZv_JBTzmd2clCKeONPtW3qxKPvifOCeHV_6pxHzcGI6bHWxQjdSPSn1K6lZkXAf9qmVTx5pJWC0HuWcS1Z1d4mx9zNXMqiV_qZEj6ZGxRjpxbHe6mWPfWJ0Vq7S_BQM0tHvxiHjghWZc_4xG7WcvgDgtJHv7pYqR4Q9IUva37iO8A5fQVtpkK8BJMgYPjzX_PrP1dgw16AMGoUYD2_tpV_KAyjhOJfsTVj9S4myl8g_CbSyZfo_yePLXBqnSYVrWeXuLbN9ysd7vxWo3kRWMZeyeaXNOJ_5pyjB-f
       $token = '5YXvCrjBYeBJMW509RYg7ENJxpFQW-GpMXoQQXqYt4t4H75DM6Bm5vsPmaXV8owRddS6IEOzVsiSCW0r3N2H6BO_OKRPOjfM1fD6ZknX-sc0BnHkHisx9jYUw4sQOk1zhkt_HNLXE7uI70j-IwrHN9_pAzMKUKtUF7faAU4cTnXFCnZxgZRHUyC2iAHHNvXhAomWzm3ohN0so8tQZZ8bRmXYeka8fdUZ7q1setnQAaPUpY-YLLutQ7ilDglHOutoJYJoKVxlZ9clMN61uwHkv2Q3GaksZHlkDyLyUHiKhbvpNbqzJbMaSTmcom2CQoRoXaeMeCGVxPJuVjGvx2H7U7GD1Mptg84-9sqHVn2JeXPO5TF4lVvIqYvcyO9zAYmC7NyFkI_GptxsHlZIX5i_4DZwpgolk8IgHr7ERDKrgwG0V4BI9HvW_1Wcu7jJDM-JUv4zCrwnUOA1NHF8DdwR9qjy28ATkJRylGpJKzz2u4BxybhvB6fXvQytsnzl4V8DrOOk4ftndsX6AwFYkcTYRtODUbvw9a4Gy0zsACZrOmNLC0VTpNk4cORrQSf8drYtbQlzA5j704w7Egya0tPbUfGoNpg88NChl-gxJ42bBh9UUlQEtsbyqdiuSS4MzQ9Ni5wO7thg7HLKIhd2v8zIBA3h8NRpCYONXk6-9LHIRSOBXdGq';
       
       if($type == 'KSA'){
       		$basURL = "https://api-sa.myfatoorah.com";
           $CallBackUrl = "https://almaraacompany.com/turki-api/api/v1/invoicestatus/ksa";
            $token = 'b9GUEu3nRvdp9T0CgMVQD7nrbySRW3bCkWEwTBXLK4S7C1GGPFFX8LThwyz3pr3D2nyOCnWTAp4pWz9BBNTCzKcioSIqzQ8qsUKTjvWSEmrA8hsaMd-XarT3mUnHmuGFZ5ZDA0B-mdtZ7PzGneRRliPlRrcbvFbjJQVNnYAmZ8fP-vIwYeSI0xekQ4babTHTLGUyTtL1-Q2mx3XIKqS0ONv4j1yTSfG2P-hxRQzTYFkxwhlS1bqWVfq7owMZmiWqh9Fjr40mbOnGgdJu-yVXywR2a2Lt4iiNgcsMjTu_zxFxjzb7G04e5ybnD7DUVXLdSVh5aJHY4niOinjuNA2e3tOvR_5VTw69ECAol6Jj6d7ERrTDkDxVuwyqOz1Vb-skEwhbMEnFjNaZv_JBTzmd2clCKeONPtW3qxKPvifOCeHV_6pxHzcGI6bHWxQjdSPSn1K6lZkXAf9qmVTx5pJWC0HuWcS1Z1d4mx9zNXMqiV_qZEj6ZGxRjpxbHe6mWPfWJ0Vq7S_BQM0tHvxiHjghWZc_4xG7WcvgDgtJHv7pYqR4Q9IUva37iO8A5fQVtpkK8BJMgYPjzX_PrP1dgw16AMGoUYD2_tpV_KAyjhOJfsTVj9S4myl8g_CbSyZfo_yePLXBqnSYVrWeXuLbN9ysd7vxWo3kRWMZeyeaXNOJ_5pyjB-f';
        
        //   $token = 'G1FlLkOw8mg50jVaL44gP8qukYwGVIjQYXtOqnthyYY0hKj6c9m4z_10eOVpnSXxcGRZBuOxPALFSmdyXcPS8OKZ2N4zwoRIW71CSHTZPBXRRTjDe2l54FEKwW6vPvWJz4Q0CkRbBShAd5qesW69yQwo2MoORPny_SgpT-d2xeKrjP9daJbR9yjShOOx8WVNM42beUVH7n-AC7hHqTQk5pylsv8VFS7gHE7b6h4IuDIsmy_4S6RmOCV4l6t4ut4IofbBeAsrx6pmFMk8-5ofYlqHVzhXZBy_jwNKrKKJdEEdwhx0C7n39Mw5XON8_RjiJgJsPdMeleJLlDK418FTBJYowc_s3u_rFHysFO8GqYkwlPe-GrIhA4O2EZpPq1DIFkheQ3Rral1O5ln7S9Qg1P5U4VDRtbrGqGMF1kP1edOFePVNuCEP7x9hRn_nfVcH3U54Pibm23VlWAcEPf1FQNp9qBuW3fD2ISNjuuqIshkffzHcnB3kLSKY-NYUigJGHet_zfXS6WfzwFPK1BnH5p_PTAVyakvRtJYuodw2O4rBQenvEhYAFJWwAEWVQQkTg4kqJXll-Z6TawVrR--JFAMhJyeCJc8fZFD0CMCFJAi_3HylVuqYM2gGw-9M_uArNVx1Q_KaZ_ueBuQrN5YP6GPMyr3pKgUu8xvajvAc8v7_KmszejMDE6nv9iiBVjHTdzbRVc8QsjicsAwmUo3oBI-HXRhTxhc650Wg9z7GzzkawDz6';
       }
       
        
        
         $moblie = substr($customer->mobile, 4);
         $moblie_code = Str::substr($customer->mobile, 0, 4);
        
          $details =[
             'NotificationOption' => "LNK",
             'CustomerName' => $customer->name,
             'DisplayCurrencyIso' => $country->currency_en,
             'InvoiceValue' => (double)$order->total_amount_after_discount,
             'CallBackUrl' => $CallBackUrl,
             'CustomerReference' => $order->ref_no,
             'CustomerMobile' => $moblie,
             'MobileCountryCode' => $moblie_code,
             'CustomerCivilId' => $customer->id,
         ];
         
         $data_string = json_encode($details);
       
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$basURL/v2/SendPayment",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array("Authorization: Bearer $token", "Content-Type: application/json"),

        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        $json = json_decode((string) $response, true);

        if ($json['IsSuccess'] == true) {
            $InvoiceId = $json['Data']['InvoiceId'];
            $result['invoiceURL'] = $json['Data']['InvoiceURL'];
            $result['success'] = true;
            $data["bank_ref_no"] = $InvoiceId;
            $payment = Payment::create($data);
            $order->update(['payment_id' => $payment->id]);
            return $result;
        }else {
            $result['success'] = false;
            $result['error'] = $json;
            return $result;
        }
       
    }
    
    public function Get_Payment_Status_ksa(Request $request)
    {
        TraceError::create(['class_name' => "MyFatoorahApiService", 'method_name' => $request->query('Get_Payment_Status'), 'error_desc' => json_encode($request->all())]);
      
        $input = $request->all();
        $paymentId = $input['paymentId'];

       
     
    //  $apiKey = 'G1FlLkOw8mg50jVaL44gP8qukYwGVIjQYXtOqnthyYY0hKj6c9m4z_10eOVpnSXxcGRZBuOxPALFSmdyXcPS8OKZ2N4zwoRIW71CSHTZPBXRRTjDe2l54FEKwW6vPvWJz4Q0CkRbBShAd5qesW69yQwo2MoORPny_SgpT-d2xeKrjP9daJbR9yjShOOx8WVNM42beUVH7n-AC7hHqTQk5pylsv8VFS7gHE7b6h4IuDIsmy_4S6RmOCV4l6t4ut4IofbBeAsrx6pmFMk8-5ofYlqHVzhXZBy_jwNKrKKJdEEdwhx0C7n39Mw5XON8_RjiJgJsPdMeleJLlDK418FTBJYowc_s3u_rFHysFO8GqYkwlPe-GrIhA4O2EZpPq1DIFkheQ3Rral1O5ln7S9Qg1P5U4VDRtbrGqGMF1kP1edOFePVNuCEP7x9hRn_nfVcH3U54Pibm23VlWAcEPf1FQNp9qBuW3fD2ISNjuuqIshkffzHcnB3kLSKY-NYUigJGHet_zfXS6WfzwFPK1BnH5p_PTAVyakvRtJYuodw2O4rBQenvEhYAFJWwAEWVQQkTg4kqJXll-Z6TawVrR--JFAMhJyeCJc8fZFD0CMCFJAi_3HylVuqYM2gGw-9M_uArNVx1Q_KaZ_ueBuQrN5YP6GPMyr3pKgUu8xvajvAc8v7_KmszejMDE6nv9iiBVjHTdzbRVc8QsjicsAwmUo3oBI-HXRhTxhc650Wg9z7GzzkawDz6';
       $apiKey = 'b9GUEu3nRvdp9T0CgMVQD7nrbySRW3bCkWEwTBXLK4S7C1GGPFFX8LThwyz3pr3D2nyOCnWTAp4pWz9BBNTCzKcioSIqzQ8qsUKTjvWSEmrA8hsaMd-XarT3mUnHmuGFZ5ZDA0B-mdtZ7PzGneRRliPlRrcbvFbjJQVNnYAmZ8fP-vIwYeSI0xekQ4babTHTLGUyTtL1-Q2mx3XIKqS0ONv4j1yTSfG2P-hxRQzTYFkxwhlS1bqWVfq7owMZmiWqh9Fjr40mbOnGgdJu-yVXywR2a2Lt4iiNgcsMjTu_zxFxjzb7G04e5ybnD7DUVXLdSVh5aJHY4niOinjuNA2e3tOvR_5VTw69ECAol6Jj6d7ERrTDkDxVuwyqOz1Vb-skEwhbMEnFjNaZv_JBTzmd2clCKeONPtW3qxKPvifOCeHV_6pxHzcGI6bHWxQjdSPSn1K6lZkXAf9qmVTx5pJWC0HuWcS1Z1d4mx9zNXMqiV_qZEj6ZGxRjpxbHe6mWPfWJ0Vq7S_BQM0tHvxiHjghWZc_4xG7WcvgDgtJHv7pYqR4Q9IUva37iO8A5fQVtpkK8BJMgYPjzX_PrP1dgw16AMGoUYD2_tpV_KAyjhOJfsTVj9S4myl8g_CbSyZfo_yePLXBqnSYVrWeXuLbN9ysd7vxWo3kRWMZeyeaXNOJ_5pyjB-f';
        $apiURL = 'https://api-sa.myfatoorah.com';

        //####### Prepare Data ######
        $url = "$apiURL/v2/getPaymentStatus";
        $data = array(
            'KeyType' => 'paymentId', //paymentId invoiceid
            'Key' => "$paymentId", //the callback paymentID
        );
        $fields = json_encode($data);

        //####### Call API ######
        $curl = curl_init($url);

        curl_setopt_array($curl, array(
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => array("Authorization: Bearer $apiKey", 'Content-Type: application/json'),
            CURLOPT_RETURNTRANSFER => true,
        ));

        $res = curl_exec($curl);
       
        $err = curl_error($curl);

        curl_close($curl);
       

        if ($err) {
            return 'Please check your myfatoora API-KEY';
            return $this->sendError($err);
        } else {
            $res_arr = json_decode($res, true);
            
            $InvoiceStatus = $res_arr['Data']['InvoiceStatus'];
            $CustomerReference = $res_arr['Data']['CustomerReference'];
            
            //  if ($InvoiceStatus == 'Pending') {
                
            //       $order = Order::find($res_arr['Data']['CustomerReference']);
                     
            //     if (!empty($order)) {
            //      //   $this->PaymentRepository->update(['status' => 'Paid'], $order['payment_id']);
            //      $payment = Payment:: find($order->payment_id);
                 
            //         $payment->update([
            //             "description" => $res_arr['Data']['InvoiceStatus'],
            //             "status" => "Pending",
            //             "price" => (double)$res_arr['Data']['InvoiceValue'],
            //         ]);
            //     }
                 
            //  }
  TraceError::create(['class_name' => "MyFatoorahApiService 151", 'method_name' => $request->query('Get_Payment_Status'), 'error_desc' => json_encode($res_arr)]);
         //   $order = $this->orderRepository->update(['order_status_id' => '14'], $CustomerReference);
            
            $html_head = '<!DOCTYPE html>
                <html lang="en-US">
                <meta http-equiv="content-type" content="text/html;charset=UTF-8" />

                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width">
                    <title>تركي للذبائح</title>
                    <meta property="og:type" content="website"/>
                    <meta property="og:url" content="Food Explorer"/>
                    <meta property="og:title" content="Food Explorer"/>
                    <meta property="og:image" content="/images/PCh7OpSSPm1608014433.png"/>
                    <meta property="og:image:width" content="250px"/>
                    <meta property="og:image:height" content="250px"/>
                    <meta property="og:site_name" content="Food Explorer"/>
                    <meta property="og:description" content="Food Item Online Ordering System"/>
                    <meta property="og:keyword" content="Online,food"/>
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <style>
                    @font-face{font-family:\'29ltbukraregular\';font-style:normal;font-weight:400;src:url(/fonts/29ltbukraregular.eot);src:url(/fonts/29ltbukraregular.eot?#iefix) format(\'embedded-opentype\'),
                        url(/fonts/29ltbukraregular.woff2) format(\'woff2\'),
                        url(/fonts/29ltbukraregular.woff) format(\'woff\'),
                        url(/fonts/29ltbukraregular.ttf) format(\'truetype\');}
                        @font-face{font-family:\'29ltbukrabold\';src:url(\'/fonts/29ltbukrabold.eot\');src:url(\'/fonts/29ltbukrabold.eot?#iefix\') format(\'embedded-opentype\'),
                        url(\'/fonts/29ltbukrabold.woff\') format(\'woff\'),
                        url(\'/fonts/29ltbukrabold.ttf\') format(\'truetype\'),
                        url(\'/fonts/29ltbukrabold.svg#29ltbukrabold\') format(\'svg\');font-weight:normal;font-style:normal;}
                        body h1, body h2, body h3, body h4, body h5, body h6, p, .lte-header, .menu-item a, .citis, .lte-btn,#btnflag,#foodsection *
                        , #additemformsection *, #cartsection *,#myordersection *, .lte-content,#checkoutsection *
                        {font-family:\'29ltbukraregular\' !important;}
                        body{
                            text-align: center;
                            margin: 0;
                            font-family: \'29ltbukraregular\';
                            background: url(/images/slider-eclipse.png);
                            color: #fff;
                            font-size: 16px;
                            line-height: 2;
                            padding: 30px;
                            background-size: contain;
                        }
                        .lte-btn {
                            background-color: #e7c05d;
                            border-radius: 30px;
                            padding: 9px 6px;
                            color: #6c3434;
                            margin: 22px;
                        }

                    </style>
                    ';
                            $html_btn1 = '<div onclick="invokeNative()" class="lte-btn">
                    <span class="lte-btn-inner">
                        <span class="btnsm">
                        ';
                            $html_btn2 = '
                    </span>
                </span>
                </div>';
                            $html_sec1 = '<section id="turkeysection">
                    <img src="/images/PCh7OpSSPm1608014433.png" class="centerimglogo">
                    <div class="row">
                        <div class="turkeyd col-lg-6">

                        ';
                            $html_sec2 = '
                    </div>
                </div>
                </section>';
            if ($InvoiceStatus == 'Paid') {
                
                  $order = Order::find($res_arr['Data']['CustomerReference']);
                     
                if (!empty($order)) {
                 //   $this->PaymentRepository->update(['status' => 'Paid'], $order['payment_id']);
                 $payment = Payment:: find($order->payment_id);
                 
                    $payment->update([
                        "description" => $res_arr['Data']['InvoiceStatus'],
                        "status" => "Paid",
                        "price" => (double)$res_arr['Data']['InvoiceValue'],

                    ]);
           
                    $objOrder = $order;
                    $order = $order->toArray();
           
              $callPaymentNetsuiteApi = new CallPaymentNetsuiteApi();
              $res = $callPaymentNetsuiteApi->sendUpdatePaymentToNS($order , $request);
             
              
                    return $html_head . '


                    <head>
                        <script type="text/javascript">
                            function invokeNative() {
                                MessageInvoker.postMessage("1");
                            }
                        </script> </head>

                    <body>
                    ' . $html_sec1 . '
                    تمت عملية الدفع بنجاح
                    ' . $html_sec2 . '
                    ' . $html_btn1 . '
                            العودة إلى طلباتي
                    ' . $html_btn2 . '

                    </body>

                    </html>
                   ';
                }
            } else {
                return $html_head . '


                    <head>
                        <script type="text/javascript">
                        function invokeNative() {
                            MessageInvoker.postMessage("0");
                        }
                        </script> </head>

                    <body>
                    ' . $html_sec1 . '
                    لم يتم الدفع ... في حال أنك متأكد من نجاح عملية الدفع الرجاء مراجعة قسم المبيعات , رقم الدفعه
                    ' . $html_sec2 . '
                    ' . $html_btn1 . '
                        محاولة الدفع مرة أخرى
                    ' . $html_btn2 . '

                     </body>

                    </html>
               ' . $paymentId;
            }

            return response()->json($res_arr, 'InvoiceStatus' . $InvoiceStatus . ' --- CustomerReference' . $CustomerReference . ' -- Please check data >' . $paymentId . ' Message for status!');
        }
    }
    
    public function Get_Payment_Status(Request $request)
    {
        TraceError::create(['class_name' => "MyFatoorahApiService", 'method_name' => $request->query('Get_Payment_Status'), 'error_desc' => json_encode($request->all())]);
      
        $input = $request->all();
        $paymentId = $input['paymentId'];

       
        $apiKey = '5YXvCrjBYeBJMW509RYg7ENJxpFQW-GpMXoQQXqYt4t4H75DM6Bm5vsPmaXV8owRddS6IEOzVsiSCW0r3N2H6BO_OKRPOjfM1fD6ZknX-sc0BnHkHisx9jYUw4sQOk1zhkt_HNLXE7uI70j-IwrHN9_pAzMKUKtUF7faAU4cTnXFCnZxgZRHUyC2iAHHNvXhAomWzm3ohN0so8tQZZ8bRmXYeka8fdUZ7q1setnQAaPUpY-YLLutQ7ilDglHOutoJYJoKVxlZ9clMN61uwHkv2Q3GaksZHlkDyLyUHiKhbvpNbqzJbMaSTmcom2CQoRoXaeMeCGVxPJuVjGvx2H7U7GD1Mptg84-9sqHVn2JeXPO5TF4lVvIqYvcyO9zAYmC7NyFkI_GptxsHlZIX5i_4DZwpgolk8IgHr7ERDKrgwG0V4BI9HvW_1Wcu7jJDM-JUv4zCrwnUOA1NHF8DdwR9qjy28ATkJRylGpJKzz2u4BxybhvB6fXvQytsnzl4V8DrOOk4ftndsX6AwFYkcTYRtODUbvw9a4Gy0zsACZrOmNLC0VTpNk4cORrQSf8drYtbQlzA5j704w7Egya0tPbUfGoNpg88NChl-gxJ42bBh9UUlQEtsbyqdiuSS4MzQ9Ni5wO7thg7HLKIhd2v8zIBA3h8NRpCYONXk6-9LHIRSOBXdGq';

        $apiURL = 'https://api.myfatoorah.com';

        //####### Prepare Data ######
        $url = "$apiURL/v2/getPaymentStatus";
        $data = array(
            'KeyType' => 'paymentId', //paymentId invoiceid
            'Key' => "$paymentId", //the callback paymentID
        );
        $fields = json_encode($data);

        //####### Call API ######
        $curl = curl_init($url);

        curl_setopt_array($curl, array(
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => array("Authorization: Bearer $apiKey", 'Content-Type: application/json'),
            CURLOPT_RETURNTRANSFER => true,
        ));

        $res = curl_exec($curl);
       
        $err = curl_error($curl);

        curl_close($curl);
       

        if ($err) {
            return 'Please check your myfatoora API-KEY';
            return $this->sendError($err);
        } else {
            $res_arr = json_decode($res, true);
            
            $InvoiceStatus = $res_arr['Data']['InvoiceStatus'];
            $CustomerReference = $res_arr['Data']['CustomerReference'];
            
            //  if ($InvoiceStatus == 'Pending') {
                
            //       $order = Order::find($res_arr['Data']['CustomerReference']);
                     
            //     if (!empty($order)) {
            //      //   $this->PaymentRepository->update(['status' => 'Paid'], $order['payment_id']);
            //      $payment = Payment:: find($order->payment_id);
                 
            //         $payment->update([
            //             "description" => $res_arr['Data']['InvoiceStatus'],
            //             "status" => "Pending",
            //             "price" => (double)$res_arr['Data']['InvoiceValue'],
            //         ]);
            //     }
                 
            //  }
  TraceError::create(['class_name' => "MyFatoorahApiService 151", 'method_name' => $request->query('Get_Payment_Status'), 'error_desc' => json_encode($res_arr)]);
         //   $order = $this->orderRepository->update(['order_status_id' => '14'], $CustomerReference);
            
            $html_head = '<!DOCTYPE html>
                <html lang="en-US">
                <meta http-equiv="content-type" content="text/html;charset=UTF-8" />

                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width">
                    <title>تركي للذبائح</title>
                    <meta property="og:type" content="website"/>
                    <meta property="og:url" content="Food Explorer"/>
                    <meta property="og:title" content="Food Explorer"/>
                    <meta property="og:image" content="/images/PCh7OpSSPm1608014433.png"/>
                    <meta property="og:image:width" content="250px"/>
                    <meta property="og:image:height" content="250px"/>
                    <meta property="og:site_name" content="Food Explorer"/>
                    <meta property="og:description" content="Food Item Online Ordering System"/>
                    <meta property="og:keyword" content="Online,food"/>
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <style>
                    @font-face{font-family:\'29ltbukraregular\';font-style:normal;font-weight:400;src:url(/fonts/29ltbukraregular.eot);src:url(/fonts/29ltbukraregular.eot?#iefix) format(\'embedded-opentype\'),
                        url(/fonts/29ltbukraregular.woff2) format(\'woff2\'),
                        url(/fonts/29ltbukraregular.woff) format(\'woff\'),
                        url(/fonts/29ltbukraregular.ttf) format(\'truetype\');}
                        @font-face{font-family:\'29ltbukrabold\';src:url(\'/fonts/29ltbukrabold.eot\');src:url(\'/fonts/29ltbukrabold.eot?#iefix\') format(\'embedded-opentype\'),
                        url(\'/fonts/29ltbukrabold.woff\') format(\'woff\'),
                        url(\'/fonts/29ltbukrabold.ttf\') format(\'truetype\'),
                        url(\'/fonts/29ltbukrabold.svg#29ltbukrabold\') format(\'svg\');font-weight:normal;font-style:normal;}
                        body h1, body h2, body h3, body h4, body h5, body h6, p, .lte-header, .menu-item a, .citis, .lte-btn,#btnflag,#foodsection *
                        , #additemformsection *, #cartsection *,#myordersection *, .lte-content,#checkoutsection *
                        {font-family:\'29ltbukraregular\' !important;}
                        body{
                            text-align: center;
                            margin: 0;
                            font-family: \'29ltbukraregular\';
                            background: url(/images/slider-eclipse.png);
                            color: #fff;
                            font-size: 16px;
                            line-height: 2;
                            padding: 30px;
                            background-size: contain;
                        }
                        .lte-btn {
                            background-color: #e7c05d;
                            border-radius: 30px;
                            padding: 9px 6px;
                            color: #6c3434;
                            margin: 22px;
                        }

                    </style>
                    ';
                            $html_btn1 = '<div onclick="invokeNative()" class="lte-btn">
                    <span class="lte-btn-inner">
                        <span class="btnsm">
                        ';
                            $html_btn2 = '
                    </span>
                </span>
                </div>';
                            $html_sec1 = '<section id="turkeysection">
                    <img src="/images/PCh7OpSSPm1608014433.png" class="centerimglogo">
                    <div class="row">
                        <div class="turkeyd col-lg-6">

                        ';
                            $html_sec2 = '
                    </div>
                </div>
                </section>';
            if ($InvoiceStatus == 'Paid') {
                
                  $order = Order::find($res_arr['Data']['CustomerReference']);
                     
                if (!empty($order)) {
                 //   $this->PaymentRepository->update(['status' => 'Paid'], $order['payment_id']);
                 $payment = Payment:: find($order->payment_id);
                 
                    $payment->update([
                        "description" => $res_arr['Data']['InvoiceStatus'],
                        "status" => "Paid",
                        "price" => (double)$res_arr['Data']['InvoiceValue'],

                    ]);
           
                    $objOrder = $order;
                    $order = $order->toArray();
           
              $callPaymentNetsuiteApi = new CallPaymentNetsuiteApi();
              $res = $callPaymentNetsuiteApi->sendUpdatePaymentToNS($order , $request);
             
              
                    return $html_head . '


                    <head>
                        <script type="text/javascript">
                            function invokeNative() {
                                MessageInvoker.postMessage("1");
                            }
                        </script> </head>

                    <body>
                    ' . $html_sec1 . '
                    تمت عملية الدفع بنجاح
                    ' . $html_sec2 . '
                    ' . $html_btn1 . '
                            العودة إلى طلباتي
                    ' . $html_btn2 . '

                    </body>

                    </html>
                   ';
                }
            } else {
                return $html_head . '


                    <head>
                        <script type="text/javascript">
                        function invokeNative() {
                            MessageInvoker.postMessage("0");
                        }
                        </script> </head>

                    <body>
                    ' . $html_sec1 . '
                    لم يتم الدفع ... في حال أنك متأكد من نجاح عملية الدفع الرجاء مراجعة قسم المبيعات , رقم الدفعه
                    ' . $html_sec2 . '
                    ' . $html_btn1 . '
                        محاولة الدفع مرة أخرى
                    ' . $html_btn2 . '

                     </body>

                    </html>
               ' . $paymentId;
            }

            return response()->json($res_arr, 'InvoiceStatus' . $InvoiceStatus . ' --- CustomerReference' . $CustomerReference . ' -- Please check data >' . $paymentId . ' Message for status!');
        }
    }
    
    public function SetPaymentMyfatooraWallet(Customer $customer, Country $country, $amount)
    {
        $lastPayment = Payment::latest('id')->first();

        $data = [
            "ref_no" => GetNextPaymentRefNo($country->code, $lastPayment != null ? $lastPayment->id + 1 : 1),
            "customer_id" => $customer->id,
            "order_ref_no" => null,
            "price" => (double)$amount,
            "payment_type_id" => PaymentType::where('code', 'ARB')->get()->first()->id,
            "status" => "Waiting for Client", // need to move to enum class
            "description" => "Payment Created for wallet recharge", // need to move to enum class
        ];

        $token = '5YXvCrjBYeBJMW509RYg7ENJxpFQW-GpMXoQQXqYt4t4H75DM6Bm5vsPmaXV8owRddS6IEOzVsiSCW0r3N2H6BO_OKRPOjfM1fD6ZknX-sc0BnHkHisx9jYUw4sQOk1zhkt_HNLXE7uI70j-IwrHN9_pAzMKUKtUF7faAU4cTnXFCnZxgZRHUyC2iAHHNvXhAomWzm3ohN0so8tQZZ8bRmXYeka8fdUZ7q1setnQAaPUpY-YLLutQ7ilDglHOutoJYJoKVxlZ9clMN61uwHkv2Q3GaksZHlkDyLyUHiKhbvpNbqzJbMaSTmcom2CQoRoXaeMeCGVxPJuVjGvx2H7U7GD1Mptg84-9sqHVn2JeXPO5TF4lVvIqYvcyO9zAYmC7NyFkI_GptxsHlZIX5i_4DZwpgolk8IgHr7ERDKrgwG0V4BI9HvW_1Wcu7jJDM-JUv4zCrwnUOA1NHF8DdwR9qjy28ATkJRylGpJKzz2u4BxybhvB6fXvQytsnzl4V8DrOOk4ftndsX6AwFYkcTYRtODUbvw9a4Gy0zsACZrOmNLC0VTpNk4cORrQSf8drYtbQlzA5j704w7Egya0tPbUfGoNpg88NChl-gxJ42bBh9UUlQEtsbyqdiuSS4MzQ9Ni5wO7thg7HLKIhd2v8zIBA3h8NRpCYONXk6-9LHIRSOBXdGq';

        $basURL = "https://api.myfatoorah.com";
        $CallBackUrl = "https://almaraacompany.com/turki-api/api/v1/invoicestatus_wallet";

        $moblie = substr($customer->mobile, 4);
        $moblie_code = Str::substr($customer->mobile, 0, 4);

        $details =[
            'NotificationOption' => "LNK",
            'CustomerName' => $customer->name,
            'DisplayCurrencyIso' => $country->currency_en,
            'InvoiceValue' => (double)$amount,
            'CallBackUrl' => $CallBackUrl,
            'CustomerReference' => $data['ref_no'],
            'CustomerMobile' => $moblie,
            'MobileCountryCode' => $moblie_code,
            'CustomerCivilId' => $customer->id,
        ];

        $data_string = json_encode($details);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$basURL/v2/SendPayment",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array("Authorization: Bearer $token", "Content-Type: application/json"),

        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $json = json_decode((string) $response, true);

        if ($json['IsSuccess'] == true) {
            $InvoiceId = $json['Data']['InvoiceId'];
            $result['invoiceURL'] = $json['Data']['InvoiceURL'];
            $result['success'] = true;
            $data["bank_ref_no"] = $InvoiceId;
            $payment = Payment::create($data);
            return $result;
        }else {
            $result['success'] = false;
            $result['error'] = $json;
            return $result;
        }

    }

    public function GetPaymentStatusWallet(Request $request)
    {
        TraceError::create(['class_name' => "MyFatoorahApiService", 'method_name' => $request->query('Get_Payment_Status'), 'error_desc' => json_encode($request->all())]);

        $input = $request->all();
        $paymentId = $input['paymentId'];


        $apiKey = '5YXvCrjBYeBJMW509RYg7ENJxpFQW-GpMXoQQXqYt4t4H75DM6Bm5vsPmaXV8owRddS6IEOzVsiSCW0r3N2H6BO_OKRPOjfM1fD6ZknX-sc0BnHkHisx9jYUw4sQOk1zhkt_HNLXE7uI70j-IwrHN9_pAzMKUKtUF7faAU4cTnXFCnZxgZRHUyC2iAHHNvXhAomWzm3ohN0so8tQZZ8bRmXYeka8fdUZ7q1setnQAaPUpY-YLLutQ7ilDglHOutoJYJoKVxlZ9clMN61uwHkv2Q3GaksZHlkDyLyUHiKhbvpNbqzJbMaSTmcom2CQoRoXaeMeCGVxPJuVjGvx2H7U7GD1Mptg84-9sqHVn2JeXPO5TF4lVvIqYvcyO9zAYmC7NyFkI_GptxsHlZIX5i_4DZwpgolk8IgHr7ERDKrgwG0V4BI9HvW_1Wcu7jJDM-JUv4zCrwnUOA1NHF8DdwR9qjy28ATkJRylGpJKzz2u4BxybhvB6fXvQytsnzl4V8DrOOk4ftndsX6AwFYkcTYRtODUbvw9a4Gy0zsACZrOmNLC0VTpNk4cORrQSf8drYtbQlzA5j704w7Egya0tPbUfGoNpg88NChl-gxJ42bBh9UUlQEtsbyqdiuSS4MzQ9Ni5wO7thg7HLKIhd2v8zIBA3h8NRpCYONXk6-9LHIRSOBXdGq';

        $apiURL = 'https://api.myfatoorah.com';

        //####### Prepare Data ######
        $url = "$apiURL/v2/getPaymentStatus";
        $data = array(
            'KeyType' => 'paymentId', //paymentId invoiceid
            'Key' => "$paymentId", //the callback paymentID
        );
        $fields = json_encode($data);

        //####### Call API ######
        $curl = curl_init($url);

        curl_setopt_array($curl, array(
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => array("Authorization: Bearer $apiKey", 'Content-Type: application/json'),
            CURLOPT_RETURNTRANSFER => true,
        ));

        $res = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);


        if ($err) {
            return 'Please check your myfatoora API-KEY';
            return $this->sendError($err);
        } else {
            $res_arr = json_decode($res, true);

            $InvoiceStatus = $res_arr['Data']['InvoiceStatus'];
            $CustomerReference = $res_arr['Data']['CustomerReference'];

            TraceError::create(['class_name' => "MyFatoorahApiService 151", 'method_name' => $request->query('Get_Payment_Status'), 'error_desc' => json_encode($res_arr)]);

            $html_head = '<!DOCTYPE html>
                <html lang="en-US">
                <meta http-equiv="content-type" content="text/html;charset=UTF-8" />

                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width">
                    <title>تركي للذبائح</title>
                    <meta property="og:type" content="website"/>
                    <meta property="og:url" content="Food Explorer"/>
                    <meta property="og:title" content="Food Explorer"/>
                    <meta property="og:image" content="/images/PCh7OpSSPm1608014433.png"/>
                    <meta property="og:image:width" content="250px"/>
                    <meta property="og:image:height" content="250px"/>
                    <meta property="og:site_name" content="Food Explorer"/>
                    <meta property="og:description" content="Food Item Online Ordering System"/>
                    <meta property="og:keyword" content="Online,food"/>
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <style>
                    @font-face{font-family:\'29ltbukraregular\';font-style:normal;font-weight:400;src:url(/fonts/29ltbukraregular.eot);src:url(/fonts/29ltbukraregular.eot?#iefix) format(\'embedded-opentype\'),
                        url(/fonts/29ltbukraregular.woff2) format(\'woff2\'),
                        url(/fonts/29ltbukraregular.woff) format(\'woff\'),
                        url(/fonts/29ltbukraregular.ttf) format(\'truetype\');}
                        @font-face{font-family:\'29ltbukrabold\';src:url(\'/fonts/29ltbukrabold.eot\');src:url(\'/fonts/29ltbukrabold.eot?#iefix\') format(\'embedded-opentype\'),
                        url(\'/fonts/29ltbukrabold.woff\') format(\'woff\'),
                        url(\'/fonts/29ltbukrabold.ttf\') format(\'truetype\'),
                        url(\'/fonts/29ltbukrabold.svg#29ltbukrabold\') format(\'svg\');font-weight:normal;font-style:normal;}
                        body h1, body h2, body h3, body h4, body h5, body h6, p, .lte-header, .menu-item a, .citis, .lte-btn,#btnflag,#foodsection *
                        , #additemformsection *, #cartsection *,#myordersection *, .lte-content,#checkoutsection *
                        {font-family:\'29ltbukraregular\' !important;}
                        body{
                            text-align: center;
                            margin: 0;
                            font-family: \'29ltbukraregular\';
                            background: url(/images/slider-eclipse.png);
                            color: #fff;
                            font-size: 16px;
                            line-height: 2;
                            padding: 30px;
                            background-size: contain;
                        }
                        .lte-btn {
                            background-color: #e7c05d;
                            border-radius: 30px;
                            padding: 9px 6px;
                            color: #6c3434;
                            margin: 22px;
                        }

                    </style>
                    ';
            $html_btn1 = '<div onclick="invokeNative()" class="lte-btn">
                    <span class="lte-btn-inner">
                        <span class="btnsm">
                        ';
            $html_btn2 = '
                    </span>
                </span>
                </div>';
            $html_sec1 = '<section id="turkeysection">
                    <img src="/images/PCh7OpSSPm1608014433.png" class="centerimglogo">
                    <div class="row">
                        <div class="turkeyd col-lg-6">

                        ';
            $html_sec2 = '
                    </div>
                </div>
                </section>';
            if ($InvoiceStatus == 'Paid') {

                $payment = Payment::where('ref_no',$CustomerReference)->get()->first();
                if ($payment != null) {
                    $amount = (double)$res_arr['Data']['InvoiceValue'];
                    $payment->update([
                        "description" => $res_arr['Data']['InvoiceStatus'],
                        "status" => "Paid",
                        "price" => $amount,

                    ]);

                    $customer = Customer::find($payment->customer_id);
                    WalletLog::create([
                        'user_id' => auth()->user(),
                        'customer_id' => $payment->customer_id,
                        'last_amount' => $customer->wallet,
                        'new_amount' => $amount,
                        'action' => 'induction'
                    ]);

                    $customer->wallet = $customer->wallet + $amount;
                    $customer->save();


                    return $html_head . '


                    <head>
                        <script type="text/javascript">
                            function invokeNative() {
                                MessageInvoker.postMessage("1");
                            }
                        </script> </head>

                    <body>
                    ' . $html_sec1 . '
                    تمت عملية الدفع بنجاح
                    ' . $html_sec2 . '
                    ' . $html_btn1 . '
                            العودة إلى طلباتي
                    ' . $html_btn2 . '

                    </body>

                    </html>
                   ';
                }
            } else {
                return $html_head . '


                    <head>
                        <script type="text/javascript">
                        function invokeNative() {
                            MessageInvoker.postMessage("0");
                        }
                        </script> </head>

                    <body>
                    ' . $html_sec1 . '
                    لم يتم الدفع ... في حال أنك متأكد من نجاح عملية الدفع الرجاء مراجعة قسم المبيعات , رقم الدفعه
                    ' . $html_sec2 . '
                    ' . $html_btn1 . '
                        محاولة الدفع مرة أخرى
                    ' . $html_btn2 . '

                     </body>

                    </html>
               ' . $paymentId;
            }

            return response()->json($res_arr, 'InvoiceStatus' . $InvoiceStatus . ' --- CustomerReference' . $CustomerReference . ' -- Please check data >' . $paymentId . ' Message for status!');
        }
    }
    
}
