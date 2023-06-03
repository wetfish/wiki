## How do I deploy this container stack?

See [https://github.com/wetfish/production-manifests](https://github.com/wetfish/production-manifests)
for production deployment and full stack dev env info.

For development, to run just this stack, do 
```bash
cp mariadb.env.example mariadb.env
# -> edit, change passwords and other info as needed
cp php.env.example php.env
# -> edit, change passwords to match
cp config/ban.php.example config/ban.php
# -> add ipbans
cp config/config.php.example config/config.php
# -> admin password


docker compose \
  -f docker-compose.dev.yml \
  up -d \
  --build \
  --force-recreate

docker compose -f docker-compose.dev.yml logs -f
```

The service will be available at [http://127.0.0.1:2405](http://127.0.0.1:2405)

If you'd like, /etc/hosts wiki.wetfish.net.local to 127.0.0.1 and browse to that.

## When do I need to rebuild the container?

Whenever you make an edit in wwwroot. \
If you're brave, you could edit docker-compose.dev.yml and uncomment the bind mount for wwwroot \
If you do that, you're responsible for running `npm install` !

## To get search, tags, etc to work

Open your local wiki in a browser, and edit the page source

 - Popular

```js
left,load{popular.php}
```

 - Browse

```js
load{fun/browse.php}
```

- Search

```js
load{search.php}
```

- Tags

```js
load{src/pages/tags.php} 
 
 
See also {{tag cloud}}!
```

