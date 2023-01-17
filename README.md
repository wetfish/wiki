## How do I deploy this container stack?

See [https://github.com/wetfish/production-manifests](https://github.com/wetfish/production-manifests)
for production deployment and full stack dev env info.

For development, to run just this stack, do 
```bash
cp mariadb.env.example mariadb.env
# -> edit, change passwords and other info as needed
cp php.env.example php.env
# -> edit, change passwords to match

docker compose \
  -f docker-compose.dev.yml \
  up -d \
  --build \
  --force-recreate

docker compose logs -f
```

The service will be available at [http://127.0.0.1:80](http://127.0.0.1:80)

## When do I need to rebuild the container?

Whenever you make an edit in wwwroot. \
If you're brave, you could bind mount in a devlopment directory to `/var/www`

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

