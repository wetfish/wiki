## How do I deploy this container stack?

See [https://github.com/wetfish/server](https://github.com/wetfish/server)
for setting up a server. Then add the wiki to it with

```bash
./add-project.sh --name wiki --repo https://github.com/wetfish/wiki.git --domain <domain>
```

and it'll redeploy whenever `main` (staging) or `release` (production) is pushed.

Adding the wiki sets up its `php.env` from [setup.conf](setup.conf): it gets its own database
on the server's shared database, new login, admin, and ban passwords, and asks for the captcha
bypass regex, which can be left empty. The file is kept in `config/wiki/php.env` on the server;
to change a value, such as to keep an existing wiki's passwords, edit it and run `./deploy.sh wiki`.

The first deploy creates the wiki's database with its tables from `database/schema.sql`,
which never runs again once the database exists, so a new wiki is ready to use right away.

User uploads are kept in the `wiki_uploads` Docker volume, which deploys never replace.
To work with the database on the server:

```bash
./database.sh shell wiki
./database.sh dump wiki > wiki.sql
```

To move an existing wiki onto the server, import only the rows of its database, dumped with `--no-create-info`,
into the new wiki's empty tables, so the tables stay as `database/schema.sql` creates them:

```bash
./database.sh import wiki < wiki-data.sql
```

For development, to run just this stack, do

```bash
cp mariadb.env.example mariadb.env
# -> edit, change passwords and other info as needed
cp php.env.example php.env
# -> edit, change db info to match mariadb, other passwords as needed

docker compose up -d --build

docker compose logs -f
```

The service will be available at [http://127.0.0.1:2405](http://127.0.0.1:2405),
and the database at `127.0.0.1:3405` for database tools.
The database's tables are created the first time it starts.

If you want to test it like it's on a real server, set up a
[local copy](https://github.com/wetfish/server/blob/main/docs/how-it-works.md#a-local-copy-for-testing)
of wetfish/server and add your clone by its path, with a domain like `wiki.localhost`. \
Heads up, it only deploys committed changes!

## When do I need to rebuild the container?

Whenever you make an edit in public_html, config, or upload, or change a Dockerfile.

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
