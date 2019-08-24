<?php
namespace Test\Models\TestHelpers;

class ProfileProvider
{

    protected $userAccountNameArray = [
        'given_name'       => 'Franz',
        'surname'          => 'Ferdinand',
        'familiar_name'    => 'Franz',
        'display_name'     => 'Franz Ferdinand (Personal)',
        'abbreviated_name' => 'FF'
    ];

    protected $profile = [
        'account_id'        => 'dbid:AAH4f99T0taONIb-OurWxbNQ6ywGRopQngc',
        'email'             => 'franz@gmail.com',
        'email_verified'    => false,
        'disabled'          => false,
        'profile_photo_url' => 'https://dl-web.dropbox.com/account_photo/get/dbaphid%3AAAHWGmIXV3sUuOmBfTz0wPsiqHUpBWvv3ZA?vers=1556069330102\u0026size=128x128',
        'locale'            => 'en',
        'country'           => 'US',
        'referral_link'     => 'https://db.tt/ZITNuhtI',
        'is_paired'         => false,
        'account_type'      => [
            '.tag' => 'basic'
        ]
    ];

    public function getProfile()
    {
        $profile = $this->profile;
        $profile['name'] = $this->userAccountNameArray;

        return $profile;
    }

    public function getProfilesList()
    {
        $profile = $this->getProfile();

        return [$profile, $profile, $profile];
    }
}
