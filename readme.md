## Lavarel 入门教程学习笔记

### Model

#### 事件监听

```php

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    .
    .
    .
    protected $hidden = ['password', 'remember_token'];

    public static function boot()
    {
        parent::boot();

        // 模型创建之前
        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });

        //模型创建之后
        static::created(function ($user) {
        });
    }
    .
    .
    .
}

```

### Migrate

#### 添加列

```bash
$ php artisan make:migration add_activation_to_users_table --table=users

```

```php
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('activation_token')->nullable();
            $table->boolean('activated')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activation_token');
            $table->dropColumn('activated');
        });
    }
```

### 邮件

#### 配置

配置`.env` ，日志调试

```
.
.
.
MAIL_DRIVER=log
.
.
.
```

#### 发送邮件

```php
use Mail

Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
    $message->from($from, $name)->to($to)->subject($subject);
});
```
