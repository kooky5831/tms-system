<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('settings')->truncate();
        DB::table('settings')->insert([
            [
                "name" => 'course_fee_account',
                "val" => '200',
                "group" => 'xero',
            ],
            [
                "name" => 'ssg_grant_account',
                "val" => '625',
                "group" => 'xero',
            ],
            [
                "name" => "payment_terms",
                "val" => '<div class="payment-terms">
                            <h4 class=" mb-4 font-poppins text-dark-blue fw-700">Payment Terms:</h4>
                            <p>For cancellation/rescheduling of your registered course, kindly inform us at least 14 days before the
                                course start date in order to get a refund on the course fees. The admin fee schedule is as follows:
                            </p>
                            <p>7-14 days before course start - 20% of full course fee <strong >before SSG subsidies</strong> (e.g $740 X 20% = $148) <br> 
                                2-7 days before course start - 50% of full course fee <strong>before SSG subsidies</strong> (e.g $740 X 50% = $370) <br> 
                                1 working day before course start - 100% of full course fee <strong>before SSG subsidies</strong> (e.g $740)
                            </p>
                            <p>SkillsFuture Singapore (SSG) provides funding on condition that trainees complete at least 75% of the course and pass the assessment. If trainee is ineligible for funding, Equinet reserves the right to collect the outstanding course fees (eligible funding amount by SSG) from trainee.</p>
                        </div>',
                "group" => "invoice",
            ],
            [
                "name" => "payment_methods",
                "val" => '<div class="payment-modes">
                            <h4 class=" mb-4 font-poppins text-dark-blue fw-700">Payment Modes:</h4>
                            <p> 1) For Bank Transfer - please transfer to DBS Current 003-947024-0 and notify us </p>
                            <p> 2) For cheque payments - please mail cheque in favour of "Equinet Academy Pte Ltd" to the address below:
                                International Plaza <br>
                                10 Anson Road, #25-07<br>
                                Singapore 079903 <br></p> 
                            <p> 3) For PayNow transfers, kindly transfer the funds to UEN 201708981D or scan the QR Code attached below and send us a receipt/ screenshot of the transaction. </p>
                        </div>',
                "group" => "invoice",
            ],
            [
                "name" => "invoice_address",
                "val" => '<h4 style="color:#e24d36;">Equinet Academy Private Limited</h4>

                            <p>International Plaza 10 Anson Road,<br />
                            #25-08 SINGAPORE 079903<br />
                            SINGAPORE</p>',
                "group" => "invoice",
            ],
            [
                "name" => "invoice_logo",
                "val" => 'invoice_logo.jpg',
                "group" => "invoice",
            ],
            [
                "name" => "invoice_qr",
                "val" => 'invoice_qr.jpg',
                "group" => "invoice",
            ],
            [
                "name" => 'feedback_qr',
                "val" => 'TRAQOM_QR.png',
                "group" => 'feedback',
            ],
            [
                "name" => 'feedback_text',
                "val" => '<p style="text-align: center;">Congratulations on successfully completing the course! Your feedback is invaluable in helping us improve. Please take  a moment to scan the following TRAQOM QR code to submit your feedback. You may locate the Course Run ID on the whiteboard or obtain it from the trainer. Thank you for helping us enhance your learning experience.</p>',
                "group" => 'feedback',
            ],
        ]);
    }
}