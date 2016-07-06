# Shopmacher Meleven

http://www.meleven.de/

## Configuration

Shop configuration need to be changes

```
# config.php
    'cdn' => [
        'backend' => 'local',
        'strategy' => 'md5',
        'adapters' => [
            'local' => [
                'type' => 'local',
                'mediaUrl' => '',
                'path' => realpath(__DIR__ . '/')
            ],
            'meleven' => [
                'type' => 'meleven',
                'strategy' => 'meleven',
                'mediaUrl' => 'https://api.meleven.de/out/XXXXX/',
                'auth' => [
                    'user' => '',
                    'password' => '',
                    'channel' => 'XXXXXX'
                ]
            ]
        ]
    ]
```

## Plugin config in Backend

```
sm_meleven_enabled
sm_meleven_user
sm_meleven_password
sm_meleven_channel
```

## Database

Images mapping from main origin images and Shopware path are mapped by table

```
sm_meleven_images
```

## Command

To migrate from local filesystem to external

```
bin/console sw:media:migrate --from=local --to=meleven
bin/console sw:thumbnail:generate
```