<?php

namespace App\Traits;

trait FileInfo
{

    /*
    |--------------------------------------------------------------------------
    | File Information
    |--------------------------------------------------------------------------
    |
    | This trait basically contain the path of files and size of images.
    | All information are stored as an array. Developer will be able to access
    | this info as method and property using FileManager class.
    |
    */

    public function fileInfo(){

        $data['depositVerify'] = [
            'path'      =>'assets/images/verify/deposit'
        ];
        $data['verify'] = [
            'path'      =>'assets/verify'
        ];
        $data['default'] = [
            'path'      => 'assets/images/general/default.png',
        ];
        $data['ticket'] = [
            'path'      => 'assets/support',
        ];
        $data['logoIcon'] = [
            'path'      => 'assets/images/general',
            'size'      => '458x96',
        ];
        $data['favicon'] = [
            'size'      => '128x128',
        ];
        $data['extensions'] = [
            'path'      => 'assets/images/plugins',
            'size'      => '36x36',
        ];
        $data['seo'] = [
            'path'      => 'assets/images/seo',
            'size'      => '1180x600',
        ];
        $data['userProfile'] = [
            'path'      =>'assets/images/user/profile',
            'size'      =>'300x300',
        ];
        $data['adminProfile'] = [
            'path'      =>'assets/admin/images/profile',
            'size'      =>'400x400',
        ];
        $data['adImage'] = [
            'path'      =>'assets/images/frontend/adImage',
        ];

        $data['paymentGateway'] = [
            'path'      => 'assets/images/gateway/method',
            'size'      => '400x400',
        ];

        $data['language'] = [
            'path'      => 'assets/images/language',
            'size'      => '50x50',
        ];
        $data['blog'] = [
            'path'      =>'assets/images/frontend/blog',
            'size'      => '728x465',
            'thumb'     => '120x80',
        ];
        $data['platform'] = [
            'path'      =>'assets/images/frontend/platform',
            'size'      => '150x150'
        ];
        $data['banner'] = [
            'path'      =>'assets/images/frontend/banner',
            'size'      => '150x150'
        ];
        $data['testimonial'] = [
            'path'      =>'assets/images/frontend/testimonial',
            'size'      => '300x300'
        ];
        $data['cta'] = [
            'path'      =>'assets/images/frontend/cta',
            'size'      => '150x150'
        ];
        $data['banner_image'] = [
            'path'      =>'assets/images/frontend/banner_image',
            'size'      => '610x655'
        ];
        $data['feature'] = [
            'path'      =>'assets/images/frontend/feature',
            'size'      => '850x585'
        ];
        $data['postMedia'] = [
            'path'      =>'assets/images/posts/media',
        ];
        return $data;
	}

}
