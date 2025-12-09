<?php

namespace App\Constants;

use Twilio\TwiML\Voice\Pay;

class Status{

    const ENABLE = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO = 0;

    const VERIFIED = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS = 1;
    const PAYMENT_PENDING = 2;
    const PAYMENT_REJECT = 3;

    CONST TICKET_OPEN = 0;
    CONST TICKET_ANSWER = 1;
    CONST TICKET_REPLY = 2;
    CONST TICKET_CLOSE = 3;

    CONST PRIORITY_LOW = 1;
    CONST PRIORITY_MEDIUM = 2;
    CONST PRIORITY_HIGH = 3;

    const USER_ACTIVE = 1;
    const USER_BAN = 0;


    const KYC_UNVERIFIED = 0;
    const KYC_VERIFIED = 1;
    const KYC_PENDING = 2;

    const ROLE_TYPE_ADMIN = 1;
    const ROLE_TYPE_USER = 2;

    const REG_COMPLETED = 1;
    const REG_PENDING = 0;


    CONST SYSTEM_LINK = 1;
    CONST EXTERNAL_LINK = 2;
    CONST PAGE_LINK = 3;

    const PLAN_MONTHLY = 1;
    const PLAN_YEARLY = 2;

    const SUBSCRIPTION_RUNNING = 1;
    const SUBSCRIPTION_EXPIRED = 0;

    const POST_DRAFT = 0;
    const POST_PUBLISHED = 1;
    const POST_SCHEDULE = 2;

    const FACEBOOK = 1;
    const INSTAGRAM = 2;
    const LINKEDIN = 3;
    const TWITTER = 4;
    const TIKTOK = 5;
    const YOUTUBE = 6;
    const SNAPCHAT = 7;


    const PUBLISH= 1;
    const SCHEDULE = 2;
    const DRAFT = 0;












    const MONTHLY = 1;
    const YEARLY = 2;
    const FREE = 0;

    const PLAN_ACTIVE = 1;
    const PLAN_CANCELED = 2;
    const PLAN_INACTIVE = 0;

    const AUTO_POST = 1;
    const NOTIFY = 2;



    const AUTOMATED = 1;
    const REMAINDER = 2;



    const FOR_KID = 1;
    const NOT_FOR_KID = 2;

    const VIDEO = 'video';
    const IMAGE = 'image';


}
