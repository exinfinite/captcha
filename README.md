# 驗證碼使用

## 安裝

```php
composer require exinfinite/captcha
```

## 使用

### 產生驗證碼

```php
use Exinfinite\Captcha\Builder;
$captcha = new Builder();

//使用預設值產生
$captcha->build();

//自訂參數
$captcha->setSize(150, 45)
        ->setLine(6)
        ->setPixel(180)
        ->build();
```

> ![](https://github.com/exinfinite/captcha/blob/main/sample/1.png)
> ![](https://github.com/exinfinite/captcha/blob/main/sample/2.png)
> ![](https://github.com/exinfinite/captcha/blob/main/sample/3.png)

### 比對使用者輸入驗證碼是否正確

```php
$is_valid = $captcha->verify("使用者輸入內容");//Case-insensitive
$is_valid = $captcha->verify("使用者輸入內容", true);//Case-sensitive

if($is_valid === true){
    ...
}
```

### 取得產出的驗證碼內容

```php
$captcha->getTxt();
```