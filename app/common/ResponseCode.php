<?php

namespace app\common;

enum ResponseCode: int
{
    case SUCCESS = 0;
    case ERROR_PARAM = 10000;
    case ERROR_AUTH = 20000;
    case ERROR_TOKEN_EXPIRED = 20001;
    case ERROR_FORBIDDEN = 20003;
    case ERROR_BUSINESS = 30000;
    case ERROR_SYSTEM = 40000;
    case ERROR_THIRD_PARTY = 50000;
}
