<?php

return [
    'use_sandbox'               => true,// 是否使用沙盒模式

    'partner'                   => '2088102175241050',
    'appid'                     => '2016091200491244',  //https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
    'returnUrl'                 => 'http://test2.pinsetang.net/orderApi/payment/alipay/return.php',     //付款成功后的同步回调地址
    'notifyUrl'                 => 'http://test2.pinsetang.net/orderApi/payment/alipay/notify.php',     //付款成功后的异步回调地址

    'signType'                  => 'RSA2',            //签名算法类型，支持RSA2和RSA，推荐使用RSA2
    'rsaPrivateKey'             => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCU+r4U4sGjpBKPYoyYqLZhp0cJJVY6v4LtUJUD7A9bJyJI8jT0wzMZJWL3vn0ITuy2JsgMtIyzTTlkBuIsa3SoT+l56BtB/HI/QxmH4M+V7z0PKb5xgy7i7YpJgUaau9TCp/iJn8Xz+uLnu1OmM40y+J9IVMDOUrHWCvmYkMWetwHbkmzzMvxkBm692YJR1mZ8SHeSS+qmA6mN5o1NlvDMTaai/B/COoVnjJTJikVxoe0n4EhXPpIM1rsz0Y/3Rr321ym/JVT9rS+UtbdkzQiwdjzWHDsE6YmU49sXnOziZzoxhyz3HKGekvZ6XPXSCg/E2Fp2Jdnva3VyaZz9ncKZAgMBAAECggEAS/tqkNpp6l0W/jxD+uQpSLr5FugNB59/ViwAJGT0HAhkixHFAoS//fY8eKCVwnQGdIxpAVngXN5pA+Qi3Ibdk65WJM/Ffy0S08MUNWHqXc0Ltj0THW/LrP97xSuC6A4eYD0tHHv5iQsPSSMYca7fN/znuAtJ3rD8dG9ah35m3CAswY0GaqiQDMsUlCBFuBklEBcgYWPbr/MJjn3Zlev6LYc0YnragkIXxXYVhgV7FGg1uChfCC2EoH8gmc8uch1rgHacbyECg/nB5H5x40+naKH0ZwT0sxg6hz4P160IEfrcc2C+tM2niPLoZHQtIdvXXETOa7EiQhDKvDJrHvkT0QKBgQDxgmBcE4XxTqusCtIL6unSd8wWGGV9665FNGuZJ2nGAFHpCdkAoMg9rFa8c9N+y8Fzoj//lZLC+RIrZuGlsJmrAKm5b5Mtq0kEz4ZogHW9NNrL7JL14NvpjshC76CysEsGuAICyNqjrYZuuwEvqc3LkxFoNzephmiya2LVzSqQLQKBgQCd6xekkD7JltAEGuabWoSHwPaVSucsuT2NsjLEgyTbQT8WGYG/dE++7kYJQmH9oeBtgovo9E7M3Rmm2ruKlzfu/Q3qUdebieazCUE+EyX1R4Aq1Up4DKXnKgQajyzTQnCFznLaAqkMTCgCBv7vgev53wtEMVD1isVggMGnWxATnQKBgEE455MWRq8TgvNUfMNxLrKDXd99uTMXihnwfTqAVWV+wqA/45Xy1gEp4MbGNRDiMB1tbQE2QMtc/pYwE7JujiGLbUrCIipMPs0IypDAWIu/kXNPMaUrk9hwkg7ZrjlzuN9YpFpiKgHYJB0Vbc2UexqGXkYZ23Nx9CZzL97zqJIFAoGACj0IvmGfwIAnomyAEQhwWUDYe9tsW4jzBbHM+tKp3BFB3tPuhpQq620VW1pTchdBrrftDEByr3OSSzAZQqYFOVhsw4lk2i+5plfuQ/EH3/wvwhcsCdaAFxmxRGuN83B2TSaSabIMdcCmQ2NiufKJcxzT5SrxXcoOe/A9HfWYeD0CgYEA6ueXY1p1xXTpik7PZWpmrrSHYRitxH4lOm7jBvwFqGFv4jWSJXRk3VU9N4OUFEh/4tYU97xJn8oYI8Tza8CgM0s85jYMjWtrpVA0LxRjWn18QcVwFeT3ZHHogGfB8cqBUPAs3WFjsWQF7Zljklj7WNnFHwA0C8Qb/IrSxMaALLQ=',
    //支付宝公钥，账户中心->密钥管理->开放平台密钥，找到添加了支付功能的应用，根据你的加密类型，查看支付宝公钥
    'alipayPublicKey'           =>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2nakfAL1Z04xL4m20e2bpivAx+wBlHKkR2xLr/JlkU0KMgbrt7e2V3ZWdvojFsLof7PJrOIPVa/Og7Cqh6RclqX8bejZNAw3Ik+c41aRn9NQsXKHW7Cs1+1if0NpfSl6BgTAPiJZ9ZXD9i9o3Xh3MqEx2BXaXHpFU+tt04kwgfR7r0ZP//+q6tPmON+C+rS4U+3gUBwtzMMunhmvFhR7mPEeWSRj8fmmNGEUxG3iRh6NGG5VoJm47jb2QwslKtb07gh0m2gqvco3jfVXGTGRktF7TshhJlQN2ZFHIdwQQQcVFpv0wOEFJviCKeXGdA1raEYjI5MXZ+OQN/zJKzH7SwIDAQAB',
    'limit_pay'                 => [
        //'balance',// 余额
        //'moneyFund',// 余额宝
        //'debitCardExpress',// 	借记卡快捷
        //'creditCard',//信用卡
        //'creditCardExpress',// 信用卡快捷
        //'creditCardCartoon',//信用卡卡通
        //'credit_group',// 信用支付类型（包含信用卡卡通、信用卡快捷、花呗、花呗分期）
    ],// 用户不可用指定渠道支付当有多个渠道时用“,”分隔

    'timeout_express' => time() + 600,// 表示必须 600s 内付款
    'gatewayUrl' => 'https://openapi.alipaydev.com/gateway.do',
    'charset' => 'UTF-8',


];