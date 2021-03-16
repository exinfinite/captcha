# 驗證碼

## 安裝

```php
composer require exinfinite/captcha
```

## 使用

### 產生驗證碼

```php
use Exinfinite\Captcha\Builder;
$captcha = new Builder();
$captcha->setSize(120, 45)
        ->setLine(6)
        ->setPixel(180)
        ->build();
```

### 比對使用者輸入驗證碼是否正確

```php
if($captcha->verify("使用者輸入內容") === true){
    ...
}
```

### 取得產出的驗證碼內容

```php
$captcha->getTxt();
```