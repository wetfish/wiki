## How do I deploy this container stack?

See [https://github.com/wetfish/production-manifests](https://github.com/wetfish/production-manifests)
for production deployment and full stack dev env info.

For development, to run just this stack, do 
```bash
cp mariadb.env.example mariadb.env
# -> edit, change passwords and other info as needed
cp php.env.example php.env
# -> edit, change db info to match mariadb, other passwords as needed

# in dev env, run npm install manually
cd wwwroot/src && npm install

docker compose \
  -f docker-compose.dev.yml \
  up -d \
  --build \
  --force-recreate

docker compose -f docker-compose.dev.yml logs -f
```

The service will be available at [http://127.0.0.1:2405](http://127.0.0.1:2405)

If you'd like, /etc/hosts wiki.wetfish.net.local to 127.0.0.1 and browse to that.

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

