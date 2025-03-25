<?php

return [

    /*
    * User Profile Image Upload
    */
   'user_profile' => env('USER_PROFILE', 'assets/images/users'),

    /*
    * User Profile Image Upload to storage
    */
    'user_profile_storage' => env('USER_PROFILE', 'users'),
    
    /*
    * Trainer Signature Upload to storage
    */
    'trainer_sign_storage' => env('TRAINER_SIGN', 'trainer_signature'),

    /*
    * Course Generated Certificate
    */
   'course_certificate' => env('COURSE_CERTIFICATE', 'public/certificates/'),
    /*
    * Blank Certificate
    */
    'blank_certificate' => env('CERTIFICATE_IMAGE', 'public/images/course_certificate/'),

    /*
    * Course Resource
    */
    'course_resource' => env('CERTIFICATE_IMAGE', 'public/images/courseresource'),

    /*
    * Company Documents Upload
    */
   'med_doc' => env('MED_DOC', 'assets/docs'),
   /*
    * Course Image Upload
    */
   'course_img' => env('COURSE_IMG', 'assets/images/course'),
   /*
    * Course Document Upload
    */
   'course_document' => env('COURSE_DOCUMENT', 'assets/documents'),

   /*
    * Trainer Signature Upload
    */
    'trainer_sign' => env('TRAINER_SIGN', 'assets/images/trainer_signature'),

];
