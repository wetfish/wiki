## To start this with docker

1. Create a **.env** based on `.env.example`
2. Create a **src/ban.php** based on `src/ban.example.php`
3. Run ```docker-compose up -d```

## If you're working with uploads, an extra step is required

```

docker-compose exec -u root wiki bash

# Inside wiki
mkdir -p /var/www/upload/thumb
chown -R fishy:fishy /var/www/upload

```

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

