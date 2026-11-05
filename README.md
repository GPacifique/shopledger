## Subscription operations

Run Laravel's scheduler every minute so the daily subscription reminder command
executes:

```cron
* * * * * cd /path/to/shopledger && php artisan schedule:run >> /dev/null 2>&1
```

Run a queue worker to deliver subscription reminder emails and database
notifications:

```sh
php artisan queue:work
```

Configure `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`,
`MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, and `MAIL_FROM_NAME` in the environment.
Set `SUBSCRIPTION_TRIAL_DAYS` (default `14`),
`SUBSCRIPTION_GRACE_DAYS` (default `7`), and
`SUBSCRIPTION_PAYMENT_INSTRUCTIONS` in the environment to customize the
subscription lifecycle and renewal instructions.